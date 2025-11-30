---
description: Deploy to production server aiquiz.vibeai.cv
---

# Production Deployment Guide for aiquiz.vibeai.cv

This guide walks you through deploying the AI Quiz application to your production server at **aiquiz.vibeai.cv**.

## Prerequisites

Before starting, ensure you have:
- [ ] SSH access to your production server (aiquiz.vibeai.cv)
- [ ] MySQL database access on the production server
- [ ] Web server (Apache/Nginx) configured on the production server
- [ ] PHP 7.4+ installed on the production server
- [ ] SSL certificate for HTTPS (recommended)
- [ ] Domain DNS pointing to your server IP

## Part 1: Prepare Your Local Environment

### 1. Build the Frontend

```bash
cd e:/projects/playqzv4/client
npm install
npm run build
```

This creates an optimized production build in the `client/dist` directory.

### 2. Verify the Build

Check that the build completed successfully:
```bash
ls client/dist
```

You should see `index.html`, `assets/` folder, and other static files.

### 3. Update Environment Configuration

Create a production environment file for the client:

**File: `client/.env.production`**
```env
VITE_API_URL=https://aiquiz.vibeai.cv/api
```

Rebuild with production settings:
```bash
npm run build
```

## Part 2: Prepare the Backend

### 1. Update API Configuration

Edit `api/config.php` for production:

```php
<?php
// Database configuration
define('DB_HOST', 'localhost'); // or your MySQL host
define('DB_NAME', 'aiqz_production'); // production database name
define('DB_USER', 'your_db_user'); // production database user
define('DB_PASS', 'your_secure_password'); // production database password
define('DB_CHARSET', 'utf8mb4');

// CORS configuration
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');
?>
```

**Important**: Do NOT commit this file with production credentials. Use environment variables or a separate config file.

### 2. Prepare Database Schema

You'll need to run `api/schema.sql` on your production MySQL database.

## Part 3: Server Setup

### 1. Connect to Your Production Server

```bash
ssh your_username@aiquiz.vibeai.cv
```

### 2. Create Directory Structure

```bash
# Create application directory
sudo mkdir -p /var/www/aiquiz.vibeai.cv
sudo chown -R $USER:$USER /var/www/aiquiz.vibeai.cv

# Create directories
cd /var/www/aiquiz.vibeai.cv
mkdir -p api
mkdir -p public
```

### 3. Set Up MySQL Database

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE aiqz_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create database user
CREATE USER 'aiqz_user'@'localhost' IDENTIFIED BY 'your_secure_password';

# Grant privileges
GRANT ALL PRIVILEGES ON aiqz_production.* TO 'aiqz_user'@'localhost';
FLUSH PRIVILEGES;

# Exit MySQL
EXIT;
```

### 4. Import Database Schema

```bash
# Upload schema.sql to server first (see Part 4), then:
mysql -u aiqz_user -p aiqz_production < /var/www/aiquiz.vibeai.cv/api/schema.sql
```

## Part 4: Upload Files to Server

### Option A: Using SCP (Secure Copy)

From your local machine:

```bash
# Upload frontend build
scp -r e:/projects/playqzv4/client/dist/* your_username@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/public/

# Upload API files
scp -r e:/projects/playqzv4/api/* your_username@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/api/
```

### Option B: Using FTP/SFTP

Use an FTP client like FileZilla:
1. Connect to `aiquiz.vibeai.cv` via SFTP
2. Upload `client/dist/*` to `/var/www/aiquiz.vibeai.cv/public/`
3. Upload `api/*` to `/var/www/aiquiz.vibeai.cv/api/`

### Option C: Using Git (Recommended)

On your production server:

```bash
cd /var/www/aiquiz.vibeai.cv

# Clone repository
git clone https://github.com/vibeai-casv/playqzv4.git temp
mv temp/client/dist/* public/
mv temp/api/* api/
rm -rf temp

# Or if you have the repo already
git pull origin main
cd client && npm install && npm run build
mv dist/* ../public/
```

## Part 5: Configure Web Server

### For Apache

Create a virtual host configuration:

**File: `/etc/apache2/sites-available/aiquiz.vibeai.cv.conf`**

```apache
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
        
        # SPA routing - redirect all requests to index.html
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
        
        # Enable PHP
        <FilesMatch \.php$>
            SetHandler application/x-httpd-php
        </FilesMatch>
    </Directory>
    
    # Logging
    ErrorLog ${APACHE_LOG_DIR}/aiquiz_error.log
    CustomLog ${APACHE_LOG_DIR}/aiquiz_access.log combined
</VirtualHost>
```

Enable the site and required modules:

```bash
# Enable modules
sudo a2enmod rewrite
sudo a2enmod headers

# Enable site
sudo a2ensite aiquiz.vibeai.cv.conf

# Test configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

### For Nginx

**File: `/etc/nginx/sites-available/aiquiz.vibeai.cv`**

```nginx
server {
    listen 80;
    server_name aiquiz.vibeai.cv www.aiquiz.vibeai.cv;
    
    root /var/www/aiquiz.vibeai.cv/public;
    index index.html;
    
    # Frontend - SPA routing
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # API endpoint
    location /api {
        alias /var/www/aiquiz.vibeai.cv/api;
        
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
        
        # Deny access to hidden files
        location ~ /\. {
            deny all;
        }
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Logging
    access_log /var/log/nginx/aiquiz_access.log;
    error_log /var/log/nginx/aiquiz_error.log;
}
```

Enable the site:

```bash
# Create symlink
sudo ln -s /etc/nginx/sites-available/aiquiz.vibeai.cv /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

## Part 6: SSL Certificate (HTTPS)

### Using Let's Encrypt (Free)

```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-apache  # For Apache
# OR
sudo apt install certbot python3-certbot-nginx   # For Nginx

# Obtain certificate
sudo certbot --apache -d aiquiz.vibeai.cv -d www.aiquiz.vibeai.cv  # Apache
# OR
sudo certbot --nginx -d aiquiz.vibeai.cv -d www.aiquiz.vibeai.cv   # Nginx

# Auto-renewal is set up automatically
# Test renewal
sudo certbot renew --dry-run
```

## Part 7: Set Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/aiquiz.vibeai.cv

# Set proper permissions
sudo find /var/www/aiquiz.vibeai.cv -type d -exec chmod 755 {} \;
sudo find /var/www/aiquiz.vibeai.cv -type f -exec chmod 644 {} \;

# Make sure PHP files are executable
sudo chmod 755 /var/www/aiquiz.vibeai.cv/api/*.php
```

## Part 8: Create Admin User

Connect to your production database and create an admin user:

```bash
mysql -u aiqz_user -p aiqz_production
```

```sql
-- Generate a UUID for the user
SET @user_id = UUID();

-- Create user account
INSERT INTO users (id, email, password_hash, created_at)
VALUES (
    @user_id,
    'vibeaicasv@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password123
    NOW()
);

-- Create profile with admin role
INSERT INTO profiles (id, email, name, role, created_at)
VALUES (
    @user_id,
    'vibeaicasv@gmail.com',
    'Admin User',
    'admin',
    NOW()
);

-- Verify
SELECT u.email, p.role FROM users u JOIN profiles p ON u.id = p.id;
```

**Note**: Change the password immediately after first login!

## Part 9: Testing

### 1. Test Frontend

Visit: `https://aiquiz.vibeai.cv`

You should see the login page.

### 2. Test API

```bash
# Health check
curl https://aiquiz.vibeai.cv/api/index.php

# Test login endpoint
curl -X POST https://aiquiz.vibeai.cv/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"vibeaicasv@gmail.com","password":"password123"}'
```

### 3. Test Database Connection

Create a test file temporarily:

```php
<?php
// /var/www/aiquiz.vibeai.cv/api/test-db.php
require_once 'db.php';
echo json_encode(['status' => 'Database connected successfully']);
?>
```

Visit: `https://aiquiz.vibeai.cv/api/test-db.php`

**Delete this file after testing!**

## Part 10: Post-Deployment

### 1. Security Checklist

- [ ] Remove test files (test-db.php, etc.)
- [ ] Ensure `config.php` is not publicly accessible
- [ ] Set up firewall rules (UFW/iptables)
- [ ] Enable HTTPS redirect
- [ ] Set up database backups
- [ ] Configure fail2ban for brute force protection
- [ ] Review file permissions

### 2. Enable HTTPS Redirect

**Apache** - Add to virtual host:
```apache
<VirtualHost *:80>
    ServerName aiquiz.vibeai.cv
    Redirect permanent / https://aiquiz.vibeai.cv/
</VirtualHost>
```

**Nginx** - Add to server block:
```nginx
server {
    listen 80;
    server_name aiquiz.vibeai.cv;
    return 301 https://$server_name$request_uri;
}
```

### 3. Set Up Automated Backups

```bash
# Create backup script
sudo nano /usr/local/bin/backup-aiquiz.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/aiquiz"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u aiqz_user -p'your_password' aiqz_production > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/aiquiz.vibeai.cv

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-aiquiz.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-aiquiz.sh
```

### 4. Monitoring

Set up basic monitoring:

```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Monitor logs
tail -f /var/log/apache2/aiquiz_error.log  # Apache
# OR
tail -f /var/log/nginx/aiquiz_error.log    # Nginx

# Monitor MySQL
sudo tail -f /var/log/mysql/error.log
```

### 5. Performance Optimization

**Enable PHP OPcache** - Edit `/etc/php/7.4/apache2/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

**Enable Gzip Compression** (Apache):
```bash
sudo a2enmod deflate
sudo systemctl restart apache2
```

## Troubleshooting

### Issue: 404 errors on page refresh

**Solution**: Ensure rewrite rules are properly configured in your web server config.

### Issue: CORS errors

**Solution**: Check `api/config.php` - ensure `ALLOWED_ORIGIN` matches your domain.

### Issue: Database connection failed

**Solution**: 
1. Verify database credentials in `api/config.php`
2. Check MySQL is running: `sudo systemctl status mysql`
3. Verify user permissions: `SHOW GRANTS FOR 'aiqz_user'@'localhost';`

### Issue: 500 Internal Server Error

**Solution**:
1. Check error logs
2. Verify PHP version compatibility
3. Check file permissions
4. Enable error display temporarily in `php.ini` (disable in production!)

## Rollback Plan

If deployment fails:

```bash
# Restore database backup
mysql -u aiqz_user -p aiqz_production < /var/backups/aiquiz/db_YYYYMMDD_HHMMSS.sql

# Restore files
cd /var/www
sudo rm -rf aiquiz.vibeai.cv
sudo tar -xzf /var/backups/aiquiz/files_YYYYMMDD_HHMMSS.tar.gz
```

## Success Criteria

Your deployment is successful when:

- ✅ Website loads at `https://aiquiz.vibeai.cv`
- ✅ Login works with admin credentials
- ✅ API endpoints respond correctly
- ✅ Database queries execute successfully
- ✅ SSL certificate is valid
- ✅ No console errors in browser
- ✅ Admin dashboard accessible

## Next Steps

After successful deployment:

1. Change default admin password
2. Create additional admin/user accounts as needed
3. Upload quiz questions
4. Configure email settings (if applicable)
5. Set up monitoring and alerts
6. Document any custom configurations

---

**Deployment Date**: _____________  
**Deployed By**: _____________  
**Server IP**: _____________  
**Database Name**: aiqz_production  
**Notes**: _____________
