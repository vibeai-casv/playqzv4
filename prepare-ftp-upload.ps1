# FTP Upload Preparation Script
# This script prepares your files for FTP upload

$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "AI Quiz - FTP Upload Preparation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$PROJECT_ROOT = $PSScriptRoot
$UPLOAD_PACKAGE = "$PROJECT_ROOT\upload_package"
$CLIENT_DIST = "$PROJECT_ROOT\client\dist"
$API_SOURCE = "$PROJECT_ROOT\api"

# Functions
function Print-Success {
    param($Message)
    Write-Host "[OK] $Message" -ForegroundColor Green
}

function Print-Error {
    param($Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

function Print-Warning {
    param($Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Print-Info {
    param($Message)
    Write-Host "[INFO] $Message" -ForegroundColor Cyan
}

# Step 1: Check if we're in the right directory
Write-Host ""
Print-Info "Step 1: Checking project directory..."

if (-not (Test-Path "$PROJECT_ROOT\client")) {
    Print-Error "Project directory not found. Please run this script from the project root."
    exit 1
}

Print-Success "Project directory found"

# Step 2: Build Frontend
Write-Host ""
Print-Info "Step 2: Building frontend..."

Push-Location "$PROJECT_ROOT\client"

if (-not (Test-Path "node_modules")) {
    Print-Warning "node_modules not found. Installing dependencies..."
    npm install
}

Print-Info "Running build command..."
npm run build

if ($LASTEXITCODE -eq 0) {
    Print-Success "Frontend build completed"
} else {
    Print-Error "Frontend build failed"
    Pop-Location
    exit 1
}

Pop-Location

# Step 3: Verify build output
Write-Host ""
Print-Info "Step 3: Verifying build output..."

if (Test-Path "$CLIENT_DIST\index.html") {
    Print-Success "Build output verified"
} else {
    Print-Error "Build output not found at: $CLIENT_DIST"
    exit 1
}

# Step 4: Create upload package directory
Write-Host ""
Print-Info "Step 4: Creating upload package directory..."

if (Test-Path $UPLOAD_PACKAGE) {
    Print-Warning "Upload package directory exists. Cleaning..."
    
    # Try to remove with retries
    $retries = 3
    $removed = $false
    
    for ($i = 1; $i -le $retries; $i++) {
        try {
            Get-ChildItem -Path $UPLOAD_PACKAGE -Recurse | Remove-Item -Force -Recurse -ErrorAction Stop
            Remove-Item -Path $UPLOAD_PACKAGE -Force -ErrorAction Stop
            $removed = $true
            break
        } catch {
            if ($i -lt $retries) {
                Start-Sleep -Seconds 1
            }
        }
    }
    
    if (-not $removed) {
        Print-Warning "Could not remove old directory. Creating new one anyway..."
    }
}

New-Item -ItemType Directory -Path $UPLOAD_PACKAGE -Force -ErrorAction SilentlyContinue | Out-Null
New-Item -ItemType Directory -Path "$UPLOAD_PACKAGE\public" -Force | Out-Null
New-Item -ItemType Directory -Path "$UPLOAD_PACKAGE\api" -Force | Out-Null

Print-Success "Upload package directory created"

# Step 5: Copy frontend files
Write-Host ""
Print-Info "Step 5: Copying frontend files..."

Copy-Item -Path "$CLIENT_DIST\*" -Destination "$UPLOAD_PACKAGE\public\" -Recurse -Force

$frontendFileCount = (Get-ChildItem -Path "$UPLOAD_PACKAGE\public" -Recurse -File).Count
Print-Success "Copied $frontendFileCount frontend files"

# Step 6: Copy API files
Write-Host ""
Print-Info "Step 6: Copying API files..."

# Exclude certain files
$excludeFiles = @('config.php', '*.log', 'test*.php', '.env')

Get-ChildItem -Path $API_SOURCE -Recurse | Where-Object {
    $exclude = $false
    foreach ($pattern in $excludeFiles) {
        if ($_.Name -like $pattern) {
            $exclude = $true
            break
        }
    }
    -not $exclude
} | ForEach-Object {
    $targetPath = $_.FullName.Replace($API_SOURCE, "$UPLOAD_PACKAGE\api")
    
    if ($_.PSIsContainer) {
        New-Item -ItemType Directory -Path $targetPath -Force | Out-Null
    } else {
        $targetDir = Split-Path -Parent $targetPath
        if (-not (Test-Path $targetDir)) {
            New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
        }
        Copy-Item -Path $_.FullName -Destination $targetPath -Force
    }
}

$apiFileCount = (Get-ChildItem -Path "$UPLOAD_PACKAGE\api" -Recurse -File).Count
Print-Success "Copied $apiFileCount API files"

# Step 7: Create production config template
Write-Host ""
Print-Info "Step 7: Creating production config template..."

$configContent = @"
<?php
// Production Database Configuration
// IMPORTANT: Update these values with your actual hosting credentials

define('DB_HOST', 'localhost'); // Usually 'localhost' for shared hosting
define('DB_NAME', 'aiqz_production'); // Your database name from hosting
define('DB_USER', 'aiqz_user'); // Your database user from hosting
define('DB_PASS', 'YOUR_PASSWORD_HERE'); // CHANGE THIS!
define('DB_CHARSET', 'utf8mb4');

// CORS configuration
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');

// Production settings
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);
define('LOG_ERRORS', true);

// Session configuration
define('SESSION_LIFETIME', 86400); // 24 hours
define('SESSION_SECURE', true); // Require HTTPS
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');

// File upload settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_UPLOAD_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
?>
"@

Set-Content -Path "$UPLOAD_PACKAGE\api\config.php" -Value $configContent

Print-Success "Production config created"
Print-Warning "IMPORTANT: Edit api/config.php with your database credentials before uploading!"

# Step 8: Create .htaccess files
Write-Host ""
Print-Info "Step 8: Creating .htaccess files..."

# Frontend .htaccess
$htaccessFrontend = @"
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

# Enable CORS
<IfModule mod_headers.c>
    SetEnvIf Origin "https://aiquiz.vibeai.cv" AccessControlAllowOrigin=$0
    Header set Access-Control-Allow-Origin %{AccessControlAllowOrigin}e env=AccessControlAllowOrigin
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>
"@

Set-Content -Path "$UPLOAD_PACKAGE\public\.htaccess" -Value $htaccessFrontend

# API .htaccess
$htaccessAPI = @"
# Disable error display (enable logging instead)
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

# Protect config files
<FilesMatch "^(config|db)\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>
"@

Set-Content -Path "$UPLOAD_PACKAGE\api\.htaccess" -Value $htaccessAPI

Print-Success ".htaccess files created"

# Step 9: Create README for upload package
Write-Host ""
Print-Info "Step 9: Creating upload instructions..."

$readmeContent = @"
# FTP Upload Instructions

## Files Prepared for Upload

This folder contains all files ready for FTP upload to your server.

## Directory Structure

upload_package/
├── public/          → Upload to your web root (e.g., /public_html/)
│   ├── index.html
│   ├── assets/
│   └── .htaccess
│
└── api/             → Upload to /api/ folder on server
    ├── auth/
    ├── quiz/
    ├── admin/
    ├── config.php   ⚠️ EDIT THIS FIRST!
    └── .htaccess

## Before Uploading

1. ✅ Edit api/config.php with your database credentials
2. ✅ Create database in your hosting control panel
3. ✅ Import schema.sql via phpMyAdmin
4. ✅ Install FileZilla or FTP client

## Upload Steps

1. Connect to FTP server
2. Navigate to web root (e.g., /public_html/)
3. Upload ALL files from public/ to web root
4. Create /api/ folder on server
5. Upload ALL files from api/ to /api/ folder
6. Verify all files uploaded correctly

## After Uploading

1. ✅ Visit https://aiquiz.vibeai.cv
2. ✅ Test login
3. ✅ Change admin password
4. ✅ Set up SSL certificate
5. ✅ Configure backups

## Admin Credentials

Email: vibeaicasv@gmail.com
Password: password123
⚠️ CHANGE PASSWORD IMMEDIATELY AFTER FIRST LOGIN!

## Need Help?

See DEPLOY_FTP_GUIDE.md for detailed instructions.
"@

Set-Content -Path "$UPLOAD_PACKAGE\README.txt" -Value $readmeContent

Print-Success "Upload instructions created"

# Step 10: Create file list
Write-Host ""
Print-Info "Step 10: Creating file list..."

$fileList = @"
# Files to Upload

## Frontend Files (upload to web root)
"@

Get-ChildItem -Path "$UPLOAD_PACKAGE\public" -Recurse -File | ForEach-Object {
    $relativePath = $_.FullName.Replace("$UPLOAD_PACKAGE\public\", "")
    $fileList += "`n- $relativePath"
}

$fileList += "`n`n## API Files (upload to /api/ folder)`n"

Get-ChildItem -Path "$UPLOAD_PACKAGE\api" -Recurse -File | ForEach-Object {
    $relativePath = $_.FullName.Replace("$UPLOAD_PACKAGE\api\", "")
    $fileList += "`n- $relativePath"
}

Set-Content -Path "$UPLOAD_PACKAGE\FILE_LIST.txt" -Value $fileList

Print-Success "File list created"

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Preparation Complete!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Print-Info "Upload package location:"
Write-Host "  $UPLOAD_PACKAGE" -ForegroundColor Yellow
Write-Host ""

Print-Info "Files prepared:"
Write-Host "  - Frontend: $frontendFileCount files"
Write-Host "  - API: $apiFileCount files"
Write-Host ""

Print-Warning "IMPORTANT: Before uploading via FTP:"
Write-Host "  1. Edit: $UPLOAD_PACKAGE\api\config.php"
Write-Host "  2. Update database credentials"
Write-Host "  3. Create database in hosting control panel"
Write-Host "  4. Import schema.sql via phpMyAdmin"
Write-Host ""

Print-Info "Next steps:"
Write-Host "  1. Open FileZilla or your FTP client"
Write-Host "  2. Connect to your FTP server"
Write-Host "  3. Upload files from upload_package/"
Write-Host "  4. Follow UPDATE_PRODUCTION_THEME.md for detailed instructions"
Write-Host ""

Print-Success "Ready for FTP upload!"
Write-Host ""

# Open upload package folder
$openFolder = Read-Host "Open upload package folder? (y/n)"
if ($openFolder -eq 'y') {
    Start-Process $UPLOAD_PACKAGE
}

Write-Host ""
Print-Info "For detailed FTP upload instructions, see:"
Write-Host "  UPDATE_PRODUCTION_THEME.md" -ForegroundColor Yellow
Write-Host "  DEPLOY_FTP_GUIDE.md" -ForegroundColor Yellow
Write-Host ""
