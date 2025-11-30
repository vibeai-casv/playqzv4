# 🎉 FTP Deployment Package Ready!

## What I've Created for You

Since you don't have SSH access, I've created a complete **FTP deployment solution** for your AI Quiz application.

---

## 📦 New Files Created

### 1. **DEPLOY_FTP_GUIDE.md** - Complete FTP Deployment Guide
   - Step-by-step instructions for FTP deployment
   - FileZilla setup and usage
   - Database setup via phpMyAdmin (cPanel)
   - .htaccess configuration
   - SSL certificate installation
   - Troubleshooting guide

### 2. **prepare-ftp-upload.ps1** - Automated Preparation Script
   - Builds your frontend automatically
   - Copies all files to `upload_package` folder
   - Creates production config files
   - Generates .htaccess files
   - Creates upload instructions

---

## 🚀 Quick Start - FTP Deployment

### Step 1: Prepare Files

```powershell
# Run this script to prepare everything
.\prepare-ftp-upload.ps1
```

This will:
- ✅ Build your frontend
- ✅ Copy all files to `upload_package` folder
- ✅ Create .htaccess files
- ✅ Generate config templates

### Step 2: Update Database Credentials

Edit this file: `upload_package\api\config.php`

Update these lines:
```php
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
```

### Step 3: Set Up Database

1. Login to your hosting control panel (cPanel)
2. Create MySQL database
3. Create database user
4. Grant all privileges
5. Open phpMyAdmin
6. Import `api/schema.sql`
7. Run SQL to create admin user (see DEPLOY_FTP_GUIDE.md)

### Step 4: Upload Files via FTP

1. Download FileZilla: https://filezilla-project.org/
2. Connect to your FTP server
3. Upload `upload_package/public/*` to your web root
4. Upload `upload_package/api/*` to `/api/` folder

### Step 5: Install SSL & Test

1. Install SSL certificate via cPanel
2. Visit https://aiquiz.vibeai.cv
3. Login with admin credentials
4. Change password

---

## 📁 Upload Package Structure

After running `prepare-ftp-upload.ps1`, you'll have:

```
upload_package/
├── public/              → Upload to web root (/public_html/)
│   ├── index.html
│   ├── assets/
│   │   ├── index-[hash].js
│   │   └── index-[hash].css
│   └── .htaccess       ← Handles SPA routing
│
├── api/                 → Upload to /api/ folder
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── quiz/
│   ├── admin/
│   ├── config.php      ← EDIT THIS FIRST!
│   ├── db.php
│   ├── schema.sql
│   └── .htaccess       ← Security settings
│
├── README.txt          ← Upload instructions
└── FILE_LIST.txt       ← Complete file list
```

---

## 🔧 What You Need

### From Your Hosting Provider:

1. **FTP Credentials**:
   - FTP Host: ftp.aiquiz.vibeai.cv
   - FTP Username: [your username]
   - FTP Password: [your password]
   - Port: 21 (or 22 for SFTP)

2. **Control Panel Access**:
   - cPanel/Plesk URL
   - Username
   - Password

3. **Database Access**:
   - phpMyAdmin access (via cPanel)

---

## 📋 FTP Deployment Checklist

### Before Upload:
- [ ] Run `.\prepare-ftp-upload.ps1`
- [ ] Edit `upload_package\api\config.php` with database credentials
- [ ] Create database in cPanel
- [ ] Import schema via phpMyAdmin
- [ ] Create admin user via SQL
- [ ] Install FileZilla

### During Upload:
- [ ] Connect to FTP server
- [ ] Upload `public/*` to web root
- [ ] Upload `api/*` to `/api/` folder
- [ ] Verify all files uploaded

### After Upload:
- [ ] Install SSL certificate
- [ ] Test website loads
- [ ] Test login
- [ ] Change admin password
- [ ] Set up backups

---

## 🎯 FileZilla Quick Guide

### Connect to Server:
1. Open FileZilla
2. Host: `ftp.aiquiz.vibeai.cv`
3. Username: Your FTP username
4. Password: Your FTP password
5. Port: `21`
6. Click "Quickconnect"

### Upload Files:
1. **Left panel**: Navigate to `e:\projects\playqzv4\upload_package\public\`
2. **Right panel**: Navigate to `/public_html/` (your web root)
3. Select all files in left panel
4. Drag and drop to right panel
5. Wait for upload to complete

Repeat for API files:
1. **Left panel**: `e:\projects\playqzv4\upload_package\api\`
2. **Right panel**: `/public_html/api/`
3. Drag and drop all files

---

## 🗄️ Database Setup via phpMyAdmin

### 1. Create Database (cPanel):
- Go to "MySQL Databases"
- Create database: `aiqz_production`
- Create user: `aiqz_user`
- Set strong password
- Add user to database with ALL PRIVILEGES

### 2. Import Schema (phpMyAdmin):
- Open phpMyAdmin
- Select `aiqz_production` database
- Click "Import" tab
- Choose file: `api/schema.sql`
- Click "Go"

### 3. Create Admin User (phpMyAdmin):
- Click "SQL" tab
- Paste SQL from DEPLOY_FTP_GUIDE.md
- Click "Go"

**Admin Login**:
- Email: vibeaicasv@gmail.com
- Password: password123
- ⚠️ Change immediately!

---

## 🔒 SSL Certificate

### Via cPanel:
1. Login to cPanel
2. Find "SSL/TLS" or "Let's Encrypt"
3. Select domain: `aiquiz.vibeai.cv`
4. Click "Install"
5. Wait 5-10 minutes

### Verify:
Visit: `https://aiquiz.vibeai.cv`
Look for green padlock 🔒

---

## ✅ Testing

### 1. Website Loads:
```
https://aiquiz.vibeai.cv
```
Should show login page

### 2. Login Works:
- Email: vibeaicasv@gmail.com
- Password: password123

### 3. No Errors:
- Press F12 (browser console)
- Check for errors
- Should see no CORS or 404 errors

### 4. Quiz Works:
- Create a quiz
- Take a quiz
- View results
- Check admin dashboard

---

## 🆘 Common Issues

### Issue: Blank Page
**Solution**: 
- Check `.htaccess` uploaded to web root
- Verify all files uploaded correctly
- Check browser console (F12) for errors

### Issue: 500 Error
**Solution**:
- Check `config.php` has correct database credentials
- Verify database exists
- Check PHP error logs in cPanel

### Issue: Can't Login
**Solution**:
- Verify admin user created in database
- Check `api/auth/login.php` exists
- Verify database connection

### Issue: CORS Errors
**Solution**:
- Check `ALLOWED_ORIGIN` in `config.php`
- Verify `.htaccess` in web root has CORS headers

---

## 📚 Documentation

For detailed instructions, see:

1. **[DEPLOY_FTP_GUIDE.md](DEPLOY_FTP_GUIDE.md)** - Complete FTP deployment guide
2. **[DEPLOYMENT_INDEX.md](DEPLOYMENT_INDEX.md)** - All deployment resources
3. **[prepare-ftp-upload.ps1](prepare-ftp-upload.ps1)** - Preparation script

---

## 🎉 You're Ready!

### Next Steps:

1. ✅ Read **DEPLOY_FTP_GUIDE.md**
2. ✅ Run **prepare-ftp-upload.ps1**
3. ✅ Edit **config.php** with database credentials
4. ✅ Set up database in cPanel
5. ✅ Upload files via FileZilla
6. ✅ Install SSL certificate
7. ✅ Test and enjoy!

---

**Good luck with your FTP deployment!** 🚀

Your application will be live at: **https://aiquiz.vibeai.cv**
