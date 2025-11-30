# 🚨 Production Server 404 Error - Troubleshooting Guide

## ✅ Frontend Rebuilt Successfully

Your frontend has been rebuilt with the correct production API URL:
```
https://aiquiz.vibeai.cv/api
```

---

## 🔍 Step 1: Test API Directly

Before re-uploading, test if your API is accessible:

**Open in browser:** `https://aiquiz.vibeai.cv/api/index.php`

### Expected Result:
```json
{
  "message": "AI Quiz API",
  "version": "1.0.0",
  "status": "running"
}
```

### If you get 404:
Your API files are not uploaded or not in the correct location.

### If you get 403:
Permission issue - files exist but can't be accessed.

### If you get blank page or error:
PHP error - check error logs.

---

## 🔧 Step 2: Verify Server File Structure

Your production server should have this structure:

```
/public_html/  (or /www/ or /htdocs/)
│
├── index.html              ← Frontend entry point
├── assets/                 ← Frontend JS/CSS
│   ├── index-[hash].js
│   └── index-[hash].css
│
└── api/                    ← API folder (MUST EXIST!)
    ├── auth/
    │   ├── login.php       ← Login endpoint
    │   ├── register.php    ← Signup endpoint
    │   └── logout.php
    ├── quiz/
    ├── admin/
    ├── config.php          ← Database config
    ├── db.php              ← Database connection
    ├── index.php           ← API info endpoint
    └── utils.php
```

---

## 📤 Step 3: Re-upload Files via FTP

### Option A: Use Preparation Script (Recommended)

```powershell
# Run this to prepare files
.\prepare-ftp-upload.ps1
```

This creates `upload_package` folder with everything ready.

### Option B: Manual Upload

1. **Upload Frontend Files:**
   - Source: `e:\projects\playqzv4\client\dist\*`
   - Destination: `/public_html/` (your web root)
   - **Important:** Upload ALL files, overwrite existing

2. **Upload API Files:**
   - Source: `e:\projects\playqzv4\api\*`
   - Destination: `/public_html/api/`
   - **Important:** Make sure `api` folder exists on server

---

## 🔍 Step 4: Verify Upload via FTP

Using FileZilla or your FTP client, check that these files exist on server:

### Frontend Files (in web root):
- ✅ `/public_html/index.html`
- ✅ `/public_html/assets/index-[hash].js`
- ✅ `/public_html/assets/index-[hash].css`

### API Files (in /api/ folder):
- ✅ `/public_html/api/index.php`
- ✅ `/public_html/api/config.php`
- ✅ `/public_html/api/db.php`
- ✅ `/public_html/api/auth/login.php`
- ✅ `/public_html/api/auth/register.php`

---

## 🔧 Step 5: Check .htaccess Files

### Frontend .htaccess (in web root)

Create or verify `/public_html/.htaccess`:

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
```

### API .htaccess (in /api/ folder)

Create or verify `/public_html/api/.htaccess`:

```apache
# Disable error display
php_flag display_errors Off
php_flag log_errors On

# Set maximum upload size
php_value upload_max_filesize 10M
php_value post_max_size 10M

# Security - protect sensitive files
<FilesMatch "\.(sql|md|json|lock|txt)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## 🔍 Step 6: Test Each Endpoint

After uploading, test these URLs in your browser:

### 1. API Info:
```
https://aiquiz.vibeai.cv/api/index.php
```
**Expected:** JSON response with API info

### 2. Login Endpoint:
```
https://aiquiz.vibeai.cv/api/auth/login.php
```
**Expected:** Error message (because no POST data sent)
**NOT Expected:** 404 error

### 3. Register Endpoint:
```
https://aiquiz.vibeai.cv/api/auth/register.php
```
**Expected:** Error message (because no POST data sent)
**NOT Expected:** 404 error

---

## 🔧 Step 7: Check File Permissions

Via FTP or cPanel File Manager, verify permissions:

### Directories:
- `/public_html/` → 755
- `/public_html/api/` → 755
- `/public_html/api/auth/` → 755

### Files:
- `/public_html/index.html` → 644
- `/public_html/api/*.php` → 644 or 755
- `/public_html/.htaccess` → 644

### How to set permissions in FileZilla:
1. Right-click on file/folder
2. Select "File permissions..."
3. Set numeric value (755 or 644)
4. Click OK

---

## 🔍 Step 8: Check Server Configuration

### Via cPanel:

1. **Check PHP Version:**
   - Go to "Select PHP Version" or "MultiPHP Manager"
   - Ensure PHP 7.4 or higher is selected

2. **Check Error Logs:**
   - Go to "Error Log" or "Logs"
   - Look for recent errors
   - Common issues:
     - "File not found" → Wrong path
     - "Permission denied" → Wrong permissions
     - "Parse error" → PHP syntax error

3. **Check .htaccess:**
   - Make sure mod_rewrite is enabled
   - Some hosts require you to enable it in cPanel

---

## 🔧 Step 9: Verify Database Configuration

Check `/public_html/api/config.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_actual_database_name');
define('DB_USER', 'your_actual_database_user');
define('DB_PASS', 'your_actual_database_password');
define('DB_CHARSET', 'utf8mb4');

define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');
?>
```

**Make sure:**
- ✅ Database name is correct
- ✅ Database user is correct
- ✅ Database password is correct
- ✅ ALLOWED_ORIGIN matches your domain

---

## 🔍 Step 10: Common Issues & Solutions

### Issue 1: "api" folder in wrong location

**Wrong:**
```
/public_html/
└── client/
    └── api/  ❌
```

**Correct:**
```
/public_html/
├── index.html
└── api/  ✅
```

### Issue 2: API files uploaded to wrong server path

Some hosts use different paths:
- `/public_html/`
- `/www/`
- `/htdocs/`
- `/domains/aiquiz.vibeai.cv/public_html/`

**Solution:** Find your web root and upload there.

### Issue 3: .htaccess not working

**Solution:** Check if mod_rewrite is enabled:
1. Create test file: `/public_html/test.php`
```php
<?php phpinfo(); ?>
```
2. Visit: `https://aiquiz.vibeai.cv/test.php`
3. Search for "mod_rewrite"
4. If not found, contact your hosting provider

### Issue 4: CORS errors

**Solution:** Update `api/config.php`:
```php
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');
```

And ensure `.htaccess` has CORS headers.

---

## 📋 Quick Checklist

- [ ] Frontend rebuilt with production URL
- [ ] API files uploaded to `/public_html/api/`
- [ ] Frontend files uploaded to `/public_html/`
- [ ] `.htaccess` files created and uploaded
- [ ] File permissions set correctly (755/644)
- [ ] `config.php` has correct database credentials
- [ ] Database exists and has tables
- [ ] Admin user created
- [ ] Can access `https://aiquiz.vibeai.cv/api/index.php`
- [ ] Can access `https://aiquiz.vibeai.cv/api/auth/login.php`
- [ ] No 404 errors in browser console

---

## 🚀 Re-upload Instructions

### Using FileZilla:

1. **Connect to FTP:**
   - Host: `ftp.aiquiz.vibeai.cv`
   - Username: [your FTP username]
   - Password: [your FTP password]
   - Port: 21

2. **Upload Frontend:**
   - Local: `e:\projects\playqzv4\client\dist\*`
   - Remote: `/public_html/`
   - Select all files and drag to upload
   - Choose "Overwrite" when prompted

3. **Upload API:**
   - Local: `e:\projects\playqzv4\api\*`
   - Remote: `/public_html/api/`
   - Select all files and drag to upload
   - Choose "Overwrite" when prompted

4. **Upload .htaccess files:**
   - Upload `.htaccess` to `/public_html/`
   - Upload `.htaccess` to `/public_html/api/`

---

## 🧪 Final Test

After re-uploading:

1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Visit:** `https://aiquiz.vibeai.cv`
3. **Open browser console** (F12)
4. **Try to login:**
   - Email: `vibeaicasv@gmail.com`
   - Password: `password123`

### Success Indicators:
- ✅ No 404 errors in console
- ✅ API requests go to `https://aiquiz.vibeai.cv/api/...`
- ✅ Login succeeds or shows proper error message

### Still 404?
Check browser console Network tab:
- What URL is it trying to access?
- What's the exact error?
- Screenshot and share the error

---

## 📞 Need More Help?

If still getting 404 after following all steps:

1. **Check exact error in browser console (F12)**
2. **Test API directly:** `https://aiquiz.vibeai.cv/api/index.php`
3. **Check server error logs** in cPanel
4. **Verify file structure** via FTP
5. **Contact hosting support** if needed

---

**Most common cause:** API files not uploaded to correct location on server.

**Solution:** Re-upload API files to `/public_html/api/` (or your web root's `api` folder).
