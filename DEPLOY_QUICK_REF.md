# Quick Deployment Reference for aiquiz.vibeai.cv

## 🚀 Quick Start

### Option 1: Automated Deployment (Windows)

```powershell
# Update credentials in deploy.ps1 first
.\deploy.ps1
```

### Option 2: Manual Deployment

```bash
# 1. Build
cd client
npm run build

# 2. Upload
scp -r dist/* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/public/
scp -r ../api/* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/api/

# 3. Set permissions (on server)
ssh user@aiquiz.vibeai.cv
sudo chown -R www-data:www-data /var/www/aiquiz.vibeai.cv
sudo systemctl restart apache2
```

## 📋 Pre-Deployment Checklist

- [ ] Update `deploy.ps1` with your SSH credentials
- [ ] Update `api/config.production.php` with database credentials
- [ ] Build succeeds locally: `cd client && npm run build`
- [ ] Database is set up on production server
- [ ] SSL certificate is installed

## 🔧 Configuration Files

### Frontend Environment
**File**: `client/.env.production`
```env
VITE_API_URL=https://aiquiz.vibeai.cv/api
```

### Backend Configuration
**File**: `api/config.production.php` (on server)
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'aiqz_production');
define('DB_USER', 'aiqz_user');
define('DB_PASS', 'your_password');
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');
```

## 🗄️ Database Setup

### Create Database
```sql
CREATE DATABASE aiqz_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'aiqz_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON aiqz_production.* TO 'aiqz_user'@'localhost';
FLUSH PRIVILEGES;
```

### Import Schema
```bash
mysql -u aiqz_user -p aiqz_production < api/schema.sql
```

### Create Admin User
```sql
SET @user_id = UUID();

INSERT INTO users (id, email, password_hash, created_at)
VALUES (@user_id, 'vibeaicasv@gmail.com', 
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        NOW());

INSERT INTO profiles (id, email, name, role, created_at)
VALUES (@user_id, 'vibeaicasv@gmail.com', 'Admin User', 'admin', NOW());
```

Default password: `password123` (change immediately!)

## 🌐 Web Server Configuration

### Apache Virtual Host
```apache
<VirtualHost *:80>
    ServerName aiquiz.vibeai.cv
    DocumentRoot /var/www/aiquiz.vibeai.cv/public
    Alias /api /var/www/aiquiz.vibeai.cv/api
    
    <Directory /var/www/aiquiz.vibeai.cv/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>
</VirtualHost>
```

### Enable and Restart
```bash
sudo a2enmod rewrite
sudo a2ensite aiquiz.vibeai.cv.conf
sudo systemctl restart apache2
```

## 🔒 SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d aiquiz.vibeai.cv -d www.aiquiz.vibeai.cv
```

## 📊 Monitoring & Logs

### View Logs
```bash
# Apache
tail -f /var/log/apache2/aiquiz_error.log
tail -f /var/log/apache2/aiquiz_access.log

# MySQL
tail -f /var/log/mysql/error.log

# PHP errors
tail -f /var/log/php_errors.log
```

### Check Status
```bash
# Web server
sudo systemctl status apache2

# Database
sudo systemctl status mysql

# Disk space
df -h

# Memory usage
free -h
```

## 💾 Backup & Restore

### Create Backup
```bash
# Database
mysqldump -u aiqz_user -p aiqz_production > backup_$(date +%Y%m%d).sql

# Files
tar -czf backup_$(date +%Y%m%d).tar.gz /var/www/aiquiz.vibeai.cv
```

### Restore Backup
```bash
# Database
mysql -u aiqz_user -p aiqz_production < backup_20250129.sql

# Files
tar -xzf backup_20250129.tar.gz -C /
```

## 🔍 Testing

### Test Website
```bash
curl https://aiquiz.vibeai.cv
```

### Test API
```bash
# Health check
curl https://aiquiz.vibeai.cv/api/index.php

# Login
curl -X POST https://aiquiz.vibeai.cv/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"vibeaicasv@gmail.com","password":"password123"}'
```

### Test Database
```bash
mysql -u aiqz_user -p aiqz_production -e "SHOW TABLES;"
```

## 🚨 Troubleshooting

### Issue: 404 on page refresh
**Solution**: Check Apache rewrite rules are enabled

### Issue: CORS errors
**Solution**: Verify `ALLOWED_ORIGIN` in `config.php` matches your domain

### Issue: Database connection failed
**Solution**: Check credentials in `config.php` and MySQL user permissions

### Issue: 500 Internal Server Error
**Solution**: Check error logs and file permissions

### Issue: SSL certificate errors
**Solution**: Renew certificate with `sudo certbot renew`

## 📞 Important Contacts

- **Server**: aiquiz.vibeai.cv
- **Database**: aiqz_production
- **Admin Email**: vibeaicasv@gmail.com
- **Documentation**: See `.agent/workflows/deploy-production.md`

## 🔗 Useful Links

- [Full Deployment Guide](.agent/workflows/deploy-production.md)
- [Deployment Checklist](deploy-checklist.md)
- [API Documentation](README_API.md)
- [Quick Reference](QUICK_REFERENCE.md)

## ⚡ Common Commands

```bash
# Rebuild and deploy
cd client && npm run build && cd ..
.\deploy.ps1

# Restart services
ssh user@aiquiz.vibeai.cv "sudo systemctl restart apache2"

# View recent errors
ssh user@aiquiz.vibeai.cv "tail -n 50 /var/log/apache2/aiquiz_error.log"

# Check disk space
ssh user@aiquiz.vibeai.cv "df -h"

# Database backup
ssh user@aiquiz.vibeai.cv "mysqldump -u aiqz_user -p aiqz_production > ~/backup.sql"
```

## 📝 Post-Deployment Tasks

1. ✅ Change default admin password
2. ✅ Test all user flows (register, login, quiz)
3. ✅ Verify SSL certificate
4. ✅ Set up automated backups
5. ✅ Configure monitoring
6. ✅ Document any custom configurations
7. ✅ Update DNS if needed
8. ✅ Test email functionality (if applicable)

---

**Last Updated**: 2025-11-29  
**Version**: 1.0.0
