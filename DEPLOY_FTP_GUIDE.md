# FTP Deployment Guide for aiquiz.vibeai.cv
## Deploy Without SSH Access

This guide is for deploying your AI Quiz application using **FTP only** (no SSH required).

---

## 📋 What You'll Need

- [ ] FTP credentials (host, username, password, port)
- [ ] Access to your hosting control panel (cPanel, Plesk, etc.)
- [ ] FTP client software (FileZilla recommended)
- [ ] Database access through control panel (phpMyAdmin)

---

## 🛠️ Step 1: Install FTP Client

### Download FileZilla (Recommended)

1. Visit: https://filezilla-project.org/download.php?type=client
2. Download FileZilla Client for Windows
3. Install the software

### Alternative FTP Clients

- **WinSCP**: https://winscp.net/
- **Cyberduck**: https://cyberduck.io/
- **Built-in Windows FTP**: Available in File Explorer

---

## 🔧 Step 2: Build Your Application Locally

### 2.1 Build Frontend

```powershell
# Open PowerShell in your project directory
cd e:\projects\playqzv4\client

# Install dependencies (if not already done)
npm install

# Build for production
npm run build
```

This creates the `dist` folder with your production-ready frontend files.

### 2.2 Verify Build

Check that these files exist:
- `e:\projects\playqzv4\client\dist\index.html`
- `e:\projects\playqzv4\client\dist\assets\` (folder with JS/CSS files)

---

## 📁 Step 3: Prepare Files for Upload

### 3.1 Create Upload Folder

Create a temporary folder to organize files:

```powershell
# Create temp upload folder
New-Item -ItemType Directory -Path "e:\projects\playqzv4\upload_package" -Force

# Create subdirectories
New-Item -ItemType Directory -Path "e:\projects\playqzv4\upload_package\public" -Force
New-Item -ItemType Directory -Path "e:\projects\playqzv4\upload_package\api" -Force
```

### 3.2 Copy Frontend Files

```powershell
# Copy frontend build to upload package
Copy-Item -Path "e:\projects\playqzv4\client\dist\*" -Destination "e:\projects\playqzv4\upload_package\public\" -Recurse -Force
```

### 3.3 Copy API Files

```powershell
# Copy API files
Copy-Item -Path "e:\projects\playqzv4\api\*" -Destination "e:\projects\playqzv4\upload_package\api\" -Recurse -Force
```

### 3.4 Update API Configuration

**IMPORTANT**: Before uploading, update the database configuration.

Edit `e:\projects\playqzv4\upload_package\api\config.php`:

```php
<?php
// Database configuration - UPDATE THESE VALUES
define('DB_HOST', 'localhost'); // Usually 'localhost' for shared hosting
define('DB_NAME', 'your_database_name'); // From your hosting control panel
define('DB_USER', 'your_database_user'); // From your hosting control panel
define('DB_PASS', 'your_database_password'); // From your hosting control panel
define('DB_CHARSET', 'utf8mb4');

// CORS configuration
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');
?>
```

---

## 🌐 Step 4: Set Up Database via Control Panel

### 4.1 Access Your Hosting Control Panel

1. Login to your hosting control panel (cPanel/Plesk)
2. Find "MySQL Databases" or "Database Manager"

### 4.2 Create Database

1. **Create a new database**:
   - Database name: `aiqz_production` (or your choice)
   - Click "Create Database"

2. **Create database user**:
   - Username: `aiqz_user` (or your choice)
   - Password: Generate a strong password
   - Click "Create User"

3. **Add user to database**:
   - Select the user you created
   - Select the database you created
   - Grant "ALL PRIVILEGES"
   - Click "Add"

4. **Save credentials**:
   ```
   Database Name: aiqz_production
   Database User: aiqz_user
   Database Password: [your password]
   Database Host: localhost
   ```

### 4.3 Import Database Schema

1. Open **phpMyAdmin** from your control panel
2. Select your database (`aiqz_production`)
3. Click the **Import** tab
4. Click **Choose File**
5. Select: `e:\projects\playqzv4\api\schema.sql`
6. Click **Go** at the bottom
7. Wait for "Import has been successfully finished"

### 4.4 Create Admin User

1. In phpMyAdmin, click **SQL** tab
2. Paste this SQL code:

```sql
SET @user_id = UUID();

INSERT INTO users (id, email, password_hash, created_at)
VALUES (
    @user_id,
    'vibeaicasv@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    NOW()
);

INSERT INTO profiles (id, email, name, role, created_at)
VALUES (
    @user_id,
    'vibeaicasv@gmail.com',
    'Admin User',
    'admin',
    NOW()
);
```

3. Click **Go**
4. You should see "1 row inserted" twice

**Admin Login Credentials**:
- Email: vibeaicasv@gmail.com
- Password: password123
- ⚠️ **Change this password after first login!**

---

## 📤 Step 5: Upload Files via FTP

### 5.1 Connect to FTP Server

**Using FileZilla**:

1. Open FileZilla
2. Enter connection details:
   - **Host**: ftp.aiquiz.vibeai.cv (or your FTP host)
   - **Username**: Your FTP username
   - **Password**: Your FTP password
   - **Port**: 21 (or 22 for SFTP)
3. Click **Quickconnect**

### 5.2 Navigate to Web Root

In FileZilla's right panel (Remote site), navigate to your web root directory:

Common locations:
- `/public_html/`
- `/www/`
- `/htdocs/`
- `/domains/aiquiz.vibeai.cv/public_html/`

### 5.3 Create Directory Structure

In the remote site (right panel):

1. Create folder: `api`
2. The root should serve as your public folder

Your structure should look like:
```
/public_html/  (or your web root)
├── index.html (frontend files here)
├── assets/
└── api/
    ├── auth/
    ├── quiz/
    ├── admin/
    └── config.php
```

### 5.4 Upload Frontend Files

1. In FileZilla's **left panel** (Local site), navigate to:
   `e:\projects\playqzv4\upload_package\public\`

2. Select **ALL files and folders**

3. **Drag and drop** to the web root in the right panel

4. Wait for upload to complete (watch the queue at bottom)

### 5.5 Upload API Files

1. In FileZilla's **left panel**, navigate to:
   `e:\projects\playqzv4\upload_package\api\`

2. Select **ALL files and folders**

3. **Drag and drop** to the `/api/` folder in the right panel

4. Wait for upload to complete

### 5.6 Verify Upload

Check that these files exist on the server:
- ✅ `/public_html/index.html`
- ✅ `/public_html/assets/index-[hash].js`
- ✅ `/public_html/assets/index-[hash].css`
- ✅ `/public_html/api/config.php`
- ✅ `/public_html/api/db.php`
- ✅ `/public_html/api/auth/login.php`

---

## 🔧 Step 6: Configure Web Server

### 6.1 Create .htaccess for Frontend (Apache)

Create a file named `.htaccess` in your web root with this content:

```apache
# Enable Rewrite Engine
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Don't rewrite files or directories
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Don't rewrite API requests
    RewriteCond %{REQUEST_URI} !^/api/
    
    # Rewrite everything else to index.html
    RewriteRule . /index.html [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# Disable directory browsing
Options -Indexes

# Enable CORS for API
<IfModule mod_headers.c>
    SetEnvIf Origin "https://aiquiz.vibeai.cv" AccessControlAllowOrigin=$0
    Header set Access-Control-Allow-Origin %{AccessControlAllowOrigin}e env=AccessControlAllowOrigin
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>
```

**How to create .htaccess**:

1. Create a new text file on your computer
2. Paste the content above
3. Save as `.htaccess` (with the dot at the beginning)
4. Upload to your web root via FTP

### 6.2 Create .htaccess for API

Create another `.htaccess` in the `/api/` folder:

```apache
# Enable error reporting (disable in production after testing)
php_flag display_errors Off
php_flag log_errors On

# Set maximum upload size
php_value upload_max_filesize 10M
php_value post_max_size 10M

# Security
<FilesMatch "\.(sql|md|json|lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect config files
<FilesMatch "^(config|db)\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

Upload this to `/api/.htaccess`

---

## 🔒 Step 7: Set Up SSL Certificate

### Option 1: Through Control Panel (Recommended)

Most hosting providers offer free SSL:

1. Login to your control panel (cPanel)
2. Find "SSL/TLS" or "Let's Encrypt SSL"
3. Select your domain: `aiquiz.vibeai.cv`
4. Click "Install" or "Enable SSL"
5. Wait for installation (usually 5-10 minutes)

### Option 2: Manual SSL

If your host doesn't provide free SSL:

1. Get a free SSL from: https://letsencrypt.org/ or https://www.cloudflare.com/
2. Follow your hosting provider's instructions to install

### Verify SSL

Visit: `https://aiquiz.vibeai.cv`

You should see a green padlock 🔒

---

## ✅ Step 8: Test Your Deployment

### 8.1 Test Website

1. Open browser
2. Go to: `https://aiquiz.vibeai.cv`
3. You should see the login page

### 8.2 Test Login

1. Click "Login"
2. Enter:
   - Email: `vibeaicasv@gmail.com`
   - Password: `password123`
3. Click "Sign In"

If successful, you'll be redirected to the dashboard.

### 8.3 Test API

Open browser console (F12) and check for errors:
- ✅ No CORS errors
- ✅ No 404 errors
- ✅ API requests succeed

### 8.4 Test Database

1. Try creating a quiz
2. Take a quiz
3. View results
4. Check admin dashboard

---

## 🔧 Step 9: Post-Deployment Configuration

### 9.1 Change Admin Password

1. Login to the application
2. Go to Profile/Settings
3. Change password from `password123` to a secure password
4. Save changes

### 9.2 Set Up Automated Backups

**Via Control Panel**:

1. Find "Backup" or "Backup Wizard" in cPanel
2. Enable automatic backups
3. Set schedule (daily recommended)
4. Choose backup location

**Manual Backup**:

1. In phpMyAdmin, select your database
2. Click "Export"
3. Choose "Quick" export method
4. Click "Go"
5. Save the SQL file
6. Download via FTP regularly

### 9.3 Configure Error Logging

Edit `api/config.php` and add:

```php
// Error logging
ini_set('display_errors', 0); // Hide errors from users
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log'); // Ask your host for the path
```

---

## 🔄 Updating Your Application

When you need to deploy updates:

### 1. Build Locally

```powershell
cd e:\projects\playqzv4\client
npm run build
```

### 2. Backup Current Version

1. In FileZilla, download current files to a backup folder
2. Or use control panel backup feature

### 3. Upload New Files

1. Connect via FTP
2. Upload new files (they will overwrite old ones)
3. Clear browser cache and test

---

## 🆘 Troubleshooting

### Issue: Website shows blank page

**Solution**:
1. Check browser console (F12) for errors
2. Verify all files uploaded correctly
3. Check `.htaccess` is present
4. Verify file permissions (usually 644 for files, 755 for folders)

### Issue: API returns 500 error

**Solution**:
1. Check `api/config.php` has correct database credentials
2. Verify database exists and user has permissions
3. Check PHP error logs in control panel
4. Ensure PHP version is 7.4 or higher

### Issue: CORS errors

**Solution**:
1. Verify `ALLOWED_ORIGIN` in `api/config.php` matches your domain
2. Check `.htaccess` CORS headers are correct
3. Clear browser cache

### Issue: Can't login

**Solution**:
1. Verify database has admin user (check in phpMyAdmin)
2. Check `api/auth/login.php` file exists
3. Verify database connection in `api/config.php`
4. Check browser console for API errors

### Issue: 404 on page refresh

**Solution**:
1. Verify `.htaccess` exists in web root
2. Check if mod_rewrite is enabled (ask your host)
3. Verify `.htaccess` has correct rewrite rules

### Issue: File upload fails

**Solution**:
1. Check FTP connection is stable
2. Try uploading smaller batches
3. Use "Binary" transfer mode in FileZilla
4. Check disk space quota in control panel

---

## 📋 FTP Deployment Checklist

### Pre-Deployment
- [ ] Frontend built successfully (`npm run build`)
- [ ] Database credentials obtained from hosting
- [ ] FTP credentials obtained
- [ ] FileZilla or FTP client installed

### Database Setup
- [ ] Database created in control panel
- [ ] Database user created with password
- [ ] User granted all privileges on database
- [ ] Schema imported via phpMyAdmin
- [ ] Admin user created via SQL

### File Upload
- [ ] Frontend files uploaded to web root
- [ ] API files uploaded to `/api/` folder
- [ ] `.htaccess` created and uploaded (web root)
- [ ] `.htaccess` created and uploaded (`/api/`)
- [ ] `config.php` updated with database credentials

### Configuration
- [ ] SSL certificate installed
- [ ] HTTPS redirect enabled
- [ ] Error logging configured
- [ ] File permissions correct

### Testing
- [ ] Website loads at https://aiquiz.vibeai.cv
- [ ] SSL certificate valid (green padlock)
- [ ] Can login with admin credentials
- [ ] Quiz functionality works
- [ ] No console errors
- [ ] API requests succeed

### Post-Deployment
- [ ] Admin password changed
- [ ] Automated backups configured
- [ ] Error logs accessible
- [ ] Documentation updated

---

## 📞 Quick Reference

### FTP Connection Details
- **Host**: ftp.aiquiz.vibeai.cv
- **Port**: 21 (FTP) or 22 (SFTP)
- **Username**: [Your FTP username]
- **Password**: [Your FTP password]

### Database Details
- **Host**: localhost
- **Database**: aiqz_production
- **User**: aiqz_user
- **Password**: [Your database password]

### File Locations
- **Web Root**: `/public_html/` (or similar)
- **Frontend**: `/public_html/`
- **API**: `/public_html/api/`
- **Config**: `/public_html/api/config.php`

### Admin Credentials
- **Email**: vibeaicasv@gmail.com
- **Password**: password123 (change immediately!)

---

## 🎯 Success!

If you've completed all steps and passed the checklist, your application is now live!

Visit: **https://aiquiz.vibeai.cv**

---

## 📚 Additional Resources

- [FileZilla Documentation](https://wiki.filezilla-project.org/Documentation)
- [cPanel Documentation](https://docs.cpanel.net/)
- [phpMyAdmin Documentation](https://docs.phpmyadmin.net/)

---

**Deployment Date**: _______________  
**Deployed By**: _______________  
**FTP Host**: _______________  
**Database Name**: _______________  
**Notes**: _______________
