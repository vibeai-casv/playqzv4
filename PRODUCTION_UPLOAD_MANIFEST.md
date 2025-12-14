# Production Deployment - File Upload Guide

**Deployment Date:** 2025-12-13  
**Target Server:** aiquiz.vibeai.cv  
**Package Location:** `E:\projects\playqzv4\upload_package`

---

## 📦 Upload Package Structure

```
upload_package/
├── public/              → Upload to /public_html/ or /httpdocs/ (WEB ROOT)
├── api/                 → Upload to /public_html/api/ or /api/
└── fix/                 → Upload to /public_html/fix/ or /fix/ (temporary)
```

---

## 📁 Directory 1: PUBLIC (Frontend Files)

**Local Source:** `upload_package/public/`  
**Server Destination:** `/public_html/` (or `/httpdocs/`, `/www/`, depending on your host)

### Files to Upload: (42 files total)

#### Root Files:
```
✓ index.html                    (622 bytes) - Main HTML file
✓ .htaccess                     (auto-generated) - Apache rewrite rules
```

#### Assets Directory: `assets/`
All files in `upload_package/public/assets/` → `/public_html/assets/`

**CSS Files:**
```
✓ index-CcvHUdOO.css           (99.54 KB) - Main stylesheet
```

**JavaScript Files:** (31 files, ~1.06 MB total)
```
✓ index-B8J9VdUQ.js            (246.85 KB) - Main bundle
✓ charts-D1ec71pP.js           (377.50 KB) - Charts library
✓ schemas-BznUp_a-.js          (82.03 KB) - Form validation
✓ ui-BnwCf6MH.js               (78.41 KB) - UI components
✓ vendor-D8AvkUoV.js           (44.44 KB) - React & vendors
✓ Questions-BqsPpPig.js        (38.03 KB) - Questions page
✓ ImportExport-D8mhGXV8.js     (12.86 KB) - Import/Export
✓ Users-CKtzFMQF.js            (12.98 KB) - User management
✓ Dashboard-DojbCub6.js        (13.09 KB) - Admin dashboard
✓ Dashboard-fP1a9Hp5.js        (7.71 KB) - User dashboard
✓ Demo-CMODBrn6.js             (11.47 KB) - Demo quiz
✓ QuizConfig-BknJ2jfb.js       (11.39 KB) - Quiz config
✓ Media-xTKuLBI8.js            (10.44 KB) - Media manager
✓ ActivityLogs-DtUGGSmj.js     (9.98 KB) - Activity logs
✓ Signup-DoH2w4aX.js           (9.45 KB) - Signup page
✓ TakeQuiz-SPlbM_Xj.js         (9.26 KB) - Quiz interface
✓ QuizResults-DA0kik6x.js      (8.53 KB) - Results page
✓ Diagnostics-aX27knLX.js      (8.08 KB) - Diagnostics
✓ SystemTools-BbGpG8o-.js      (4.56 KB) - System tools
✓ History-DEmamfTn.js          (4.21 KB) - Quiz history
✓ BundleImporter-Db407THh.js   (4.05 KB) - Bundle import
✓ Profile-CljGCI1w.js          (6.13 KB) - User profile
✓ useAdmin-CFE5Lvfq.js         (3.95 KB) - Admin hooks
✓ auth-D_O9DLOT.js             (3.53 KB) - Auth utilities
✓ Login-wBKLNc5i.js            (3.10 KB) - Login page
✓ quizStore-Dx91L3-b.js        (2.09 KB) - State store
✓ Modal-Bbk2xqZI.js            (0.82 KB) - Modal component
✓ Image-DFK2--6t.js            (0.73 KB) - Image component
✓ compat-DOTGHHbm.js           (0.04 KB) - Compatibility
```

---

## 📁 Directory 2: API (Backend Files)

**Local Source:** `upload_package/api/`  
**Server Destination:** `/public_html/api/` (or `/api/`)

### Files to Upload: (47 files total)

#### Root Files:
```
✓ index.php                    - API entry point
✓ config.php                   ⚠️ EDIT FIRST! Database credentials
✓ db.php                       - Database connection
✓ utils.php                    - Utility functions
✓ schema.sql                   - Database schema (import to DB)
✓ .htaccess                    - API routing rules
```

#### Subdirectories:

**1. auth/ (Authentication)** - 4 files
```
/api/auth/
├── login.php                  - User login endpoint
├── signup.php                 - User registration
├── logout.php                 - Logout endpoint
└── refresh.php                - Token refresh
```

**2. quiz/ (Quiz Management)** - 8 files
```
/api/quiz/
├── config.php                 - Quiz configuration
├── start.php                  - Start quiz session
├── submit.php                 - Submit answer
├── complete.php               - Complete quiz
├── results.php                - Get results
├── history.php                - Quiz history
├── demo.php                   - Demo quiz
└── leaderboard.php            - Leaderboard
```

**3. admin/ (Admin Functions)** - 12 files
```
/api/admin/
├── questions/
│   ├── list.php               - List questions
│   ├── create.php             - Create question
│   ├── update.php             - Update question
│   ├── delete.php             - Delete question
│   └── toggle_demo.php        - Toggle demo flag
├── users/
│   ├── list.php               - List users
│   ├── update.php             - Update user
│   └── toggle_user.php        - Enable/disable user
├── analytics.php              - Analytics data
├── activity_logs.php          - Activity logs
├── diagnostics.php            - System diagnostics
└── generate_questions.php     - AI question generation
```

**4. questions/ (Question Operations)** - 3 files
```
/api/questions/
├── list.php                   - Public question list
├── import.php                 - Import questions
└── random.php                 - Get random questions
```

**5. profile/ (User Profile)** - 4 files
```
/api/profile/
├── get.php                    - Get profile
├── update.php                 - Update profile
├── stats.php                  - User statistics
└── activity.php               - User activity
```

**6. media/ (Media Management)** - 3 files
```
/api/media/
├── upload.php                 - Upload media
├── list.php                   - List media files
└── delete.php                 - Delete media
```

**7. bundle/ (Export/Import)** - 2 files
```
/api/bundle/
├── export.php                 - Export bundle (ZIP)
└── import.php                 - Import bundle
```

**8. metadata/ (System Metadata)** - 2 files
```
/api/metadata/
├── categories.php             - Question categories
└── list.php                   - Metadata lists
```

---

## 📁 Directory 3: FIX (Schema Update - Temporary)

**Local Source:** `upload_package/fix/`  
**Server Destination:** `/public_html/fix/` (or `/fix/`)

### Files to Upload: (3 files)

```
✓ update_schema.php            (10.5 KB) - Schema update script
✓ SCHEMA_UPDATE_GUIDE.md       (6.2 KB) - Usage guide
✓ README.md                    (1.5 KB) - Quick reference
```

**⚠️ IMPORTANT:** Delete this directory after running the schema update!

---

## 📊 Upload Summary

| Directory | Files | Total Size | Destination |
|-----------|-------|------------|-------------|
| public/   | 42    | ~1.06 MB   | /public_html/ |
| api/      | 47    | ~150 KB    | /public_html/api/ |
| fix/      | 3     | ~18 KB     | /public_html/fix/ |
| **TOTAL** | **92**| **~1.23 MB**| - |

---

## 🎯 Upload Methods

### Method 1: FTP/SFTP (Recommended for Shared Hosting)

**Using FileZilla:**

1. **Connect to Server**
   ```
   Host: ftp.aiquiz.vibeai.cv (or aiquiz.vibeai.cv)
   Port: 21 (FTP) or 22 (SFTP)
   Username: [your FTP username]
   Password: [your FTP password]
   ```

2. **Upload public/ files**
   ```
   Local: E:\projects\playqzv4\upload_package\public\*
   Remote: /public_html/
   
   Drag and drop all files from upload_package/public/ 
   to your web root directory
   ```

3. **Upload api/ files**
   ```
   Local: E:\projects\playqzv4\upload_package\api\*
   Remote: /public_html/api/
   
   Create /api/ folder if it doesn't exist
   Upload all files maintaining directory structure
   ```

4. **Upload fix/ files**
   ```
   Local: E:\projects\playqzv4\upload_package\fix\*
   Remote: /public_html/fix/
   
   Create /fix/ folder
   Upload all files (temporary, will delete later)
   ```

### Method 2: cPanel File Manager

1. **Login to cPanel**
   ```
   https://aiquiz.vibeai.cv:2083
   Username: [your cPanel username]
   Password: [your cPanel password]
   ```

2. **Navigate to File Manager**
   - Click "File Manager"
   - Go to public_html/ (or httpdocs/)

3. **Upload Files**
   - Click "Upload" button
   - Select all files from upload_package/public/
   - Wait for upload to complete
   - Repeat for api/ and fix/ directories

### Method 3: SSH/SCP (for VPS/Dedicated)

```bash
# From Windows PowerShell
scp -r E:\projects\playqzv4\upload_package\public\* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/public/
scp -r E:\projects\playqzv4\upload_package\api\* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/api/
scp -r E:\projects\playqzv4\upload_package\fix\* user@aiquiz.vibeai.cv:/var/www/aiquiz.vibeai.cv/fix/
```

---

## ⚠️ BEFORE UPLOADING

### 1. Edit Configuration File

**File:** `upload_package/api/config.php`

Open in text editor and update:

```php
<?php
// Production Database Configuration
define('DB_HOST', 'localhost');  // Usually 'localhost'
define('DB_NAME', 'your_database_name');  // Your production DB name
define('DB_USER', 'your_db_username');    // Your DB user
define('DB_PASS', 'your_secure_password'); // Your DB password
define('DB_CHARSET', 'utf8mb4');

// CORS configuration
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');

// Production settings
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);  // IMPORTANT: Keep false in production
define('LOG_ERRORS', true);
?>
```

### 2. Create Production Database

In cPanel → MySQL Databases:

```sql
Database Name: aiqz_production (or your chosen name)
Database User: aiqz_user (or your chosen user)
Password: [strong password]

Grant ALL PRIVILEGES to the user
```

### 3. Import Database Schema

In cPanel → phpMyAdmin:

```
1. Select your database (aiqz_production)
2. Click "Import" tab
3. Choose file: upload_package/api/schema.sql
4. Click "Go"
```

---

## ✅ AFTER UPLOADING

### 1. Set Permissions (if using SSH)

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/aiquiz.vibeai.cv

# Set directory permissions
sudo find /var/www/aiquiz.vibeai.cv -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/aiquiz.vibeai.cv -type f -exec chmod 644 {} \;
```

### 2. Run Schema Update (if updating existing DB)

Visit: `https://aiquiz.vibeai.cv/fix/update_schema.php`

### 3. Test the Application

```
Visit: https://aiquiz.vibeai.cv
Login: vibeaicasv@gmail.com / password123
```

### 4. Delete Sensitive Files

```
Delete: /fix/ directory (after schema update)
```

### 5. Change Admin Password

Login and immediately change the default password!

---

## 📋 Upload Checklist

- [ ] Downloaded/prepared upload_package folder
- [ ] Edited api/config.php with production credentials
- [ ] Created production database
- [ ] Imported schema.sql to database
- [ ] Connected to FTP/SFTP
- [ ] Uploaded all files from public/ to web root
- [ ] Uploaded all files from api/ to /api/
- [ ] Uploaded all files from fix/ to /fix/
- [ ] Verified file structure on server
- [ ] Ran fix/update_schema.php (if updating)
- [ ] Tested website loads
- [ ] Tested login works
- [ ] Changed admin password
- [ ] Deleted /fix/ directory
- [ ] Verified SSL certificate
- [ ] Set up backups

---

## 🆘 Common Upload Issues

### Issue: "413 Request Entity Too Large"
**Solution:** Upload in smaller batches, or increase upload limit in cPanel

### Issue: Files uploaded but site shows 404
**Solution:** Check that files are in correct directory (public_html/ or httpdocs/)

### Issue: White screen / blank page
**Solution:** 
1. Check api/config.php has correct database credentials
2. Check PHP error logs in cPanel
3. Verify database was imported

### Issue: "Database connection failed"
**Solution:**
1. Verify credentials in api/config.php
2. Check database exists
3. Ensure user has privileges

---

## 📞 Support Resources

**cPanel:** Usually at `https://yourdomain.com:2083`  
**phpMyAdmin:** Access via cPanel  
**File Manager:** Access via cPanel  
**Error Logs:** cPanel → Errors

---

**Package Ready:** ✅  
**Total Files:** 92  
**Total Size:** ~1.23 MB  
**Deployment Time:** ~10-15 minutes

Good luck with your deployment! 🚀
