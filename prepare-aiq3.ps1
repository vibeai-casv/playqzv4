# AIQ3 Production Package Preparation Script
# This creates a clean production build for aiquiz.vibeai.cv/aiq3/

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  AIQ3 Production Package Builder" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Cyan

# Set paths
$packageDir = "AIQ3_PRODUCTION"
$clientDist = "client\dist"
$apiDir = "api"

# Clean old package
if (Test-Path $packageDir) {
    Write-Host "Cleaning old package..." -ForegroundColor Yellow
    Remove-Item $packageDir -Recurse -Force
}

# Create package structure
Write-Host "Creating package structure..." -ForegroundColor Cyan
New-Item -ItemType Directory -Path "$packageDir" -Force | Out-Null
New-Item -ItemType Directory -Path "$packageDir\api" -Force | Out-Null

# Copy frontend build
Write-Host "Copying frontend build..." -ForegroundColor Cyan
Copy-Item "$clientDist\*" "$packageDir\" -Recurse -Force

# Copy backend API files
Write-Host "Copying backend API..." -ForegroundColor Cyan
Copy-Item "$apiDir\*" "$packageDir\api\" -Recurse -Force -Exclude @("*.log", "*.tmp")

# Create frontend .htaccess for /aiq3/
Write-Host "Creating frontend .htaccess..." -ForegroundColor Cyan
$frontendHtaccess = @"
# AIQ3 Frontend .htaccess
RewriteEngine On
RewriteBase /aiq3/

# Redirect HTTP to HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/aiq3/$1 [L,R=301]

# Handle React Router - send all requests to index.html
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /aiq3/index.html [L]

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Cache control
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 year"
</IfModule>
"@

Set-Content -Path "$packageDir\.htaccess" -Value $frontendHtaccess

# Create backend .htaccess for /aiq3/api/
Write-Host "Creating backend .htaccess..." -ForegroundColor Cyan
$backendHtaccess = @"
# AIQ3 Backend API .htaccess
RewriteEngine On
RewriteBase /aiq3/api/

# CORS Headers
<IfModule mod_headers.c>
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
    Header always set Access-Control-Max-Age "3600"
</IfModule>

# Handle OPTIONS requests
RewriteCond %{REQUEST_METHOD} OPTIONS
RewriteRule ^(.*)$ $1 [R=200,L]

# Security
<FilesMatch "\.(sql|log|ini|conf)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# PHP settings
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
php_value max_input_time 300
"@

Set-Content -Path "$packageDir\api\.htaccess" -Value $backendHtaccess

# Create config template
Write-Host "Creating config template..." -ForegroundColor Cyan
$configTemplate = @"
<?php
/**
 * AIQ3 Production Configuration
 * 
 * IMPORTANT: Update these values with your production database credentials!
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'YOUR_DATABASE_NAME');  // ← UPDATE THIS!
define('DB_USER', 'YOUR_DATABASE_USER');  // ← UPDATE THIS!
define('DB_PASS', 'YOUR_DATABASE_PASSWORD');  // ← UPDATE THIS!

// API Configuration
define('API_BASE_URL', 'https://aiquiz.vibeai.cv/aiq3/api');
define('FRONTEND_URL', 'https://aiquiz.vibeai.cv/aiq3');

// Security
define('JWT_SECRET', 'CHANGE_THIS_TO_RANDOM_STRING_32_CHARS_MIN');  // ← UPDATE THIS!
define('SESSION_TIMEOUT', 3600); // 1 hour

// File Upload
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024); // 50MB

// Environment
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);

// Gemini AI (optional)
define('GEMINI_API_KEY', ''); // Add your key if using AI features
?>
"@

Set-Content -Path "$packageDir\api\config.php.template" -Value $configTemplate

# Create deployment guide
Write-Host "Creating deployment guide..." -ForegroundColor Cyan
$deploymentGuide = @"
# 🚀 AIQ3 Production Deployment Guide

## 📦 Package Contents

This package contains a complete production build for **aiquiz.vibeai.cv/aiq3/**

### Included:
- ✅ Frontend (React/Vite build with /aiq3/ base path)
- ✅ Backend API (PHP files)
- ✅ .htaccess files (frontend and API)
- ✅ Config template
- ✅ All fixes and diagnostics

---

## 📤 Upload Instructions

### 1. Upload Files

**Via FTP/cPanel:**
```
Upload ALL contents of AIQ3_PRODUCTION/ to:
/public_html/aiq3/
```

**Important:** Upload the CONTENTS of the folder, not the folder itself!

### 2. Configure Database

1. **Copy config template:**
   ```
   Copy: /aiq3/api/config.php.template
   To:   /aiq3/api/config.php
   ```

2. **Edit config.php:**
   - Update `DB_NAME` with your database name
   - Update `DB_USER` with your database username  
   - Update `DBPASS` with your database password
   - Update `JWT_SECRET` with a random 32+ character string

### 3. Create Database

If not already created, import the schema:
```
mysql -u YOUR_USER -p YOUR_DATABASE < schema.sql
```

Or use the schema update tool after deployment.

---

## ✅ Verify Installation

### Test URLs:

1. **Frontend:**
   ```
   https://aiquiz.vibeai.cv/aiq3/
   ```
   Should show the login page

2. **API Test:**
   ```
   https://aiquiz.vibeai.cv/aiq3/api/admin/test.php
   ```
   Should return JSON (may show auth error - that's OK)

3. **Login:**
   - Use your admin credentials
   - Should redirect to dashboard

---

## 🔧 Key Features in This Build

### Fixed Issues:
- ✅ API path detection (no longer hardcoded)
- ✅ Ignores VITE_API_URL environment variable
- ✅ Path detection from window.location
- ✅ Works in /aiq3/ subdirectory

### New Features:
- ✅ Bundle import with comprehensive diagnostics
- ✅ Schema update tool with debug logging
- ✅ Test API Connection button
- ✅ Detailed error messages

### Diagnostic Features:
- ✅ Frontend console logging (F12)
- ✅ Backend error logging (import_errors.log)
- ✅ Step-by-step import tracking
- ✅ Clear error messages

---

## 📁 Directory Structure

```
/public_html/aiq3/
├── index.html              (Frontend entry)
├── assets/                 (JS/CSS bundles)
├── aiq4.png               (Logo)
├── .htaccess              (Frontend routing)
├── api/
│   ├── .htaccess          (API CORS)
│   ├── config.php         (Your config - create from template!)
│   ├── db.php             (Database connection)
│   ├── utils.php          (Helper functions)
│   ├── admin/             (Admin endpoints)
│   ├── auth/              (Authentication)
│   ├── bundle/            (Import/Export)
│   ├── questions/         (Questions API)
│   └── ...
└── uploads/               (Media storage)
```

---

## 🔒 Security Checklist

After deployment:

- [ ] Updated config.php with production credentials
- [ ] Changed JWT_SECRET to random string
- [ ] Set correct file permissions (644 for files, 755 for dirs)
- [ ] Verified .htaccess files are active
- [ ] Tested HTTPS redirect
- [ ] Deleted config.php.template
- [ ] Removed any diagnostic/test files

---

## 🧪 Testing Checklist

- [ ] Can access https://aiquiz.vibeai.cv/aiq3/
- [ ] Can login with admin credentials
- [ ] Dashboard loads correctly
- [ ] Can view questions
- [ ] Can take a quiz
- [ ] Import/export works (check console logs)
- [ ] Schema update works
- [ ] Media upload works

---

## 🐛 Troubleshooting

### Common Issues:

**1. "Failed to fetch" errors:**
- Check: api/config.php exists and has correct credentials
- Check: Database is accessible
- Check: .htaccess files are uploaded

**2. 404 on API calls:**
- Check: Files uploaded to /aiq3/ not /aiq2/
- Check: .htaccess files prevent directory listing
- Test: /aiq3/api/admin/test.php directly

**3. Bundle import fails:**
- Open console (F12) and check logs
- Look for detailed error messages
- Check import_errors.log on server

**4. Schema update fails:**
- Click "Test API Connection" first
- Should show: "Detected API URL: /aiq3/api"
- Check console for detailed debug info

---

## 📞 Support

For issues:
1. Check console logs (F12)
2. Check import_errors.log on server
3. Verify config.php settings
4. Test API endpoint directly

---

**This is a fresh AIQ3 deployment with all latest fixes!** 🎉

Built: {date}
Path: /aiq3/
URL: https://aiquiz.vibeai.cv/aiq3/
"@ -replace "{date}", (Get-Date -Format "yyyy-MM-dd HH:mm:ss")

Set-Content -Path "$packageDir\DEPLOYMENT_GUIDE.md" -Value $deploymentGuide

# Create README
$readme = @"
# AIQ3 Production Package

Fresh production build for **aiquiz.vibeai.cv/aiq3/**

## Quick Start

1. Upload ALL files to `/public_html/aiq3/`
2. Copy `api/config.php.template` to `api/config.php`
3. Edit `api/config.php` with your database credentials
4. Visit https://aiquiz.vibeai.cv/aiq3/

## Documentation

See `DEPLOYMENT_GUIDE.md` for complete instructions.

## What's Included

- ✅ Frontend build with /aiq3/ path
- ✅ Backend API with all fixes
- ✅ .htaccess files for routing
- ✅ Config template
- ✅ Comprehensive diagnostics

## Key Features

- Path detection from URL (no hardcoding!)
- Bundle import/export with logging
- Schema update tool
- Test API connection
- All latest fixes

Built: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
"@

Set-Content -Path "$packageDir\README.md" -Value $readme

# Summary
Write-Host "`n========================================" -ForegroundColor Green
Write-Host "  ✅ AIQ3 Package Created!" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "Package Location:" -ForegroundColor Yellow
Write-Host "  E:\projects\playqzv4\$packageDir\`n" -ForegroundColor White

Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  1. Upload $packageDir/* to /public_html/aiq3/" -ForegroundColor White
Write-Host "  2. Configure api/config.php with DB credentials" -ForegroundColor White
Write-Host "  3. Visit https://aiquiz.vibeai.cv/aiq3/`n" -ForegroundColor White

Write-Host "Documentation:" -ForegroundColor Yellow
Write-Host "  - DEPLOYMENT_GUIDE.md (complete instructions)" -ForegroundColor White
Write-Host "  - README.md (quick reference)`n" -ForegroundColor White

Write-Host "Features:" -ForegroundColor Yellow
Write-Host "  ✓ Fixed API path detection" -ForegroundColor Green
Write-Host "  ✓ Bundle import diagnostics" -ForegroundColor Green
Write-Host "  ✓ Schema update tool" -ForegroundColor Green
Write-Host "  ✓ Fresh /aiq3/ deployment`n" -ForegroundColor Green

explorer $packageDir
