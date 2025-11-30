#!/bin/bash

# Server Setup Script for aiquiz.vibeai.cv
# Run this script on your production server to set up the environment
# Usage: sudo bash server-setup.sh

set -e

echo "========================================"
echo "AI Quiz - Production Server Setup"
echo "========================================"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root (use sudo)"
    exit 1
fi

# Configuration
DOMAIN="aiquiz.vibeai.cv"
APP_DIR="/var/www/$DOMAIN"
DB_NAME="aiqz_production"
DB_USER="aiqz_user"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_success() { echo -e "${GREEN}✓ $1${NC}"; }
print_error() { echo -e "${RED}✗ $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠ $1${NC}"; }
print_info() { echo -e "ℹ $1"; }

# Step 1: Update system
echo ""
print_info "Step 1: Updating system packages..."
apt update && apt upgrade -y
print_success "System updated"

# Step 2: Install required packages
echo ""
print_info "Step 2: Installing required packages..."

# Apache, PHP, MySQL
apt install -y apache2 \
    php php-mysql php-cli php-common php-json php-mbstring php-xml php-curl \
    mysql-server \
    curl wget git unzip

print_success "Packages installed"

# Step 3: Enable Apache modules
echo ""
print_info "Step 3: Enabling Apache modules..."
a2enmod rewrite
a2enmod headers
a2enmod ssl
print_success "Apache modules enabled"

# Step 4: Create application directory
echo ""
print_info "Step 4: Creating application directory..."
mkdir -p "$APP_DIR/public"
mkdir -p "$APP_DIR/api"
mkdir -p "$APP_DIR/uploads"
mkdir -p /var/log/aiquiz
print_success "Directories created"

# Step 5: Set up MySQL
echo ""
print_info "Step 5: Setting up MySQL database..."

# Generate random password
DB_PASS=$(openssl rand -base64 16)

mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

print_success "Database created"
print_warning "Database credentials:"
echo "  Database: $DB_NAME"
echo "  User: $DB_USER"
echo "  Password: $DB_PASS"
echo ""
print_warning "SAVE THESE CREDENTIALS! They will be needed for config.php"
echo ""

# Save credentials to file
cat > /root/aiquiz_db_credentials.txt << EOF
Database Credentials for AI Quiz
Generated: $(date)

Database: $DB_NAME
User: $DB_USER
Password: $DB_PASS
Host: localhost

IMPORTANT: Update api/config.php with these credentials
EOF

print_info "Credentials saved to: /root/aiquiz_db_credentials.txt"

# Step 6: Configure Apache
echo ""
print_info "Step 6: Configuring Apache virtual host..."

cat > /etc/apache2/sites-available/$DOMAIN.conf << 'EOF'
<VirtualHost *:80>
    ServerName aiquiz.vibeai.cv
    ServerAlias www.aiquiz.vibeai.cv
    
    DocumentRoot /var/www/aiquiz.vibeai.cv/public
    
    # API endpoint
    Alias /api /var/www/aiquiz.vibeai.cv/api
    
    <Directory /var/www/aiquiz.vibeai.cv/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        # SPA routing
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>
    
    <Directory /var/www/aiquiz.vibeai.cv/api>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        <FilesMatch \.php$>
            SetHandler application/x-httpd-php
        </FilesMatch>
    </Directory>
    
    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
    
    # Logging
    ErrorLog /var/log/aiquiz/error.log
    CustomLog /var/log/aiquiz/access.log combined
</VirtualHost>
EOF

# Enable site
a2ensite $DOMAIN.conf
a2dissite 000-default.conf

print_success "Apache configured"

# Step 7: Set permissions
echo ""
print_info "Step 7: Setting file permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
print_success "Permissions set"

# Step 8: Configure PHP
echo ""
print_info "Step 8: Configuring PHP..."

# Find php.ini
PHP_INI=$(php -i | grep "Loaded Configuration File" | awk '{print $5}')

if [ -f "$PHP_INI" ]; then
    # Backup original
    cp "$PHP_INI" "$PHP_INI.backup"
    
    # Update settings
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 10M/' "$PHP_INI"
    sed -i 's/post_max_size = .*/post_max_size = 10M/' "$PHP_INI"
    sed -i 's/max_execution_time = .*/max_execution_time = 300/' "$PHP_INI"
    sed -i 's/memory_limit = .*/memory_limit = 256M/' "$PHP_INI"
    
    print_success "PHP configured"
else
    print_warning "Could not find php.ini, skipping PHP configuration"
fi

# Step 9: Install Certbot for SSL
echo ""
print_info "Step 9: Installing Certbot for SSL..."
apt install -y certbot python3-certbot-apache
print_success "Certbot installed"

print_info "To obtain SSL certificate, run:"
echo "  sudo certbot --apache -d $DOMAIN -d www.$DOMAIN"

# Step 10: Configure firewall
echo ""
print_info "Step 10: Configuring firewall..."

if command -v ufw &> /dev/null; then
    ufw allow 'Apache Full'
    ufw allow OpenSSH
    print_success "Firewall configured"
else
    print_warning "UFW not found, skipping firewall configuration"
fi

# Step 11: Restart services
echo ""
print_info "Step 11: Restarting services..."
systemctl restart apache2
systemctl restart mysql
print_success "Services restarted"

# Step 12: Create backup script
echo ""
print_info "Step 12: Creating backup script..."

cat > /usr/local/bin/backup-aiquiz.sh << EOF
#!/bin/bash
BACKUP_DIR="/var/backups/aiquiz"
DATE=\$(date +%Y%m%d_%H%M%S)

mkdir -p \$BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p'$DB_PASS' $DB_NAME > \$BACKUP_DIR/db_\$DATE.sql

# Backup files
tar -czf \$BACKUP_DIR/files_\$DATE.tar.gz $APP_DIR

# Keep only last 7 days
find \$BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: \$DATE"
EOF

chmod +x /usr/local/bin/backup-aiquiz.sh
print_success "Backup script created"

print_info "To schedule daily backups, add to crontab:"
echo "  0 2 * * * /usr/local/bin/backup-aiquiz.sh"

# Summary
echo ""
echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo ""
print_success "Server setup completed successfully!"
echo ""
print_info "Next steps:"
echo "  1. Upload your application files to: $APP_DIR"
echo "  2. Update api/config.php with database credentials"
echo "  3. Import database schema: mysql -u $DB_USER -p $DB_NAME < schema.sql"
echo "  4. Obtain SSL certificate: sudo certbot --apache -d $DOMAIN"
echo "  5. Create admin user in the database"
echo "  6. Test the application"
echo ""
print_warning "Important files:"
echo "  - Database credentials: /root/aiquiz_db_credentials.txt"
echo "  - Apache config: /etc/apache2/sites-available/$DOMAIN.conf"
echo "  - Error logs: /var/log/aiquiz/error.log"
echo "  - Backup script: /usr/local/bin/backup-aiquiz.sh"
echo ""
print_info "System information:"
echo "  - PHP version: $(php -v | head -n 1)"
echo "  - MySQL version: $(mysql --version)"
echo "  - Apache version: $(apache2 -v | head -n 1)"
echo ""
