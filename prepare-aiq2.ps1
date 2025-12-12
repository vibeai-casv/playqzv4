# AIQ2 FTP Upload Preparation Script
# This script prepares your files for FTP upload to /aiq2/ subdirectory

$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "AI Quiz - AIQ2 Upload Preparation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$PROJECT_ROOT = $PSScriptRoot
$UPLOAD_PACKAGE = "$PROJECT_ROOT\upload_package_aiq2_v7"
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

# Step 2: Build Frontend (Skip if already built recently to save time/error)
# Write-Host ""
# Print-Info "Step 2: Building frontend..."
# Push-Location "$PROJECT_ROOT\client"
# npm run build
# Pop-Location

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
    Remove-Item -Path $UPLOAD_PACKAGE -Recurse -Force -ErrorAction SilentlyContinue
}

New-Item -ItemType Directory -Path $UPLOAD_PACKAGE -Force | Out-Null
New-Item -ItemType Directory -Path "$UPLOAD_PACKAGE\public" -Force | Out-Null
New-Item -ItemType Directory -Path "$UPLOAD_PACKAGE\api" -Force | Out-Null

Print-Success "Upload package directory created: $UPLOAD_PACKAGE"

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

# Step 7: Create production config template (Standard)
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
?>
"@

Set-Content -Path "$UPLOAD_PACKAGE\api\config.php" -Value $configContent

Print-Success "Production config created"

# Step 8: Create .htaccess files
Write-Host ""
Print-Info "Step 8: Creating .htaccess files..."

# Frontend .htaccess for SUBDIRECTORY /aiq2/
$htaccessFrontend = @"
# Enable Rewrite Engine
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /aiq2/
    
    # Don't rewrite files or directories
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Don't rewrite API requests
    RewriteCond %{REQUEST_URI} !^/aiq2/api/
    
    # Rewrite everything else to index.html
    RewriteRule . /aiq2/index.html [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# Disable directory browsing
Options -Indexes
"@

Set-Content -Path "$UPLOAD_PACKAGE\public\.htaccess" -Value $htaccessFrontend

# API .htaccess
$htaccessAPI = @"
# Enable Rewrite Engine
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Fix for Authorization header being stripped by some shared hosts
    RewriteCond %{HTTP:Authorization} ^(.*)
    RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
</IfModule>

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

Print-Success ".htaccess files created (Configured for /aiq2/)"

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "AIQ2 Preparation Complete!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

Print-Info "Package location: $UPLOAD_PACKAGE"
Write-Host "Upload the CONTENTS of '$UPLOAD_PACKAGE\public' to your '/public_html/aiq2/' folder."
Write-Host "Upload the CONTENTS of '$UPLOAD_PACKAGE\api' to your '/public_html/aiq2/api/' folder."
