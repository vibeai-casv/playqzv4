# Quick FTP Upload Preparation Script (No Build)
# Use this when you've already built the frontend

$ErrorActionPreference = "Continue"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Quick FTP Upload Preparation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$PROJECT_ROOT = $PSScriptRoot
$UPLOAD_PACKAGE = "$PROJECT_ROOT\upload_package_new"
$CLIENT_DIST = "$PROJECT_ROOT\client\dist"
$API_SOURCE = "$PROJECT_ROOT\api"

Write-Host "[INFO] Using existing build from: $CLIENT_DIST" -ForegroundColor Cyan
Write-Host ""

# Step 1: Verify build exists
if (-not (Test-Path "$CLIENT_DIST\index.html")) {
    Write-Host "[ERROR] Build not found! Run 'npm run build' first" -ForegroundColor Red
    exit 1
}

Write-Host "[OK] Build found" -ForegroundColor Green
Write-Host ""

# Step 2: Create upload package directory
Write-Host "[INFO] Creating upload package..." -ForegroundColor Cyan

if (Test-Path $UPLOAD_PACKAGE) {
    Write-Host "[INFO] Removing old package..." -ForegroundColor Cyan
    Remove-Item -Recurse -Force $UPLOAD_PACKAGE -ErrorAction SilentlyContinue
}

New-Item -ItemType Directory -Path $UPLOAD_PACKAGE -Force | Out-Null
New-Item -ItemType Directory -Path "$UPLOAD_PACKAGE\public" -Force | Out-Null
New-Item -ItemType Directory -Path "$UPLOAD_PACKAGE\api" -Force | Out-Null

Write-Host "[OK] Directories created" -ForegroundColor Green
Write-Host ""

# Step 3: Copy frontend files
Write-Host "[INFO] Copying frontend files..." -ForegroundColor Cyan

Copy-Item -Path "$CLIENT_DIST\*" -Destination "$UPLOAD_PACKAGE\public\" -Recurse -Force -ErrorAction Continue

$frontendFileCount = (Get-ChildItem -Path "$UPLOAD_PACKAGE\public" -Recurse -File -ErrorAction SilentlyContinue).Count
Write-Host "[OK] Copied $frontendFileCount frontend files" -ForegroundColor Green
Write-Host ""

# Step 4: Copy API files (excluding config.php)
Write-Host "[INFO] Copying API files..." -ForegroundColor Cyan

$excludeFiles = @('config.php', '*.log', 'test*.php', '.env')

Get-ChildItem -Path $API_SOURCE -Recurse -ErrorAction SilentlyContinue | Where-Object {
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
        New-Item -ItemType Directory -Path $targetPath -Force -ErrorAction SilentlyContinue | Out-Null
    } else {
        $targetDir = Split-Path -Parent $targetPath
        if (-not (Test-Path $targetDir)) {
            New-Item -ItemType Directory -Path $targetDir -Force -ErrorAction SilentlyContinue | Out-Null
        }
        Copy-Item -Path $_.FullName -Destination $targetPath -Force -ErrorAction SilentlyContinue
    }
}

$apiFileCount = (Get-ChildItem -Path "$UPLOAD_PACKAGE\api" -Recurse -File -ErrorAction SilentlyContinue).Count
Write-Host "[OK] Copied $apiFileCount API files" -ForegroundColor Green
Write-Host ""

# Step 5: Create production config
Write-Host "[INFO] Creating production config..." -ForegroundColor Cyan

$configContent = @"
<?php
// Production Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'aiqz_production');
define('DB_USER', 'aiqz_user');
define('DB_PASS', 'YOUR_PASSWORD_HERE'); // CHANGE THIS!
define('DB_CHARSET', 'utf8mb4');
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);
?>
"@

Set-Content -Path "$UPLOAD_PACKAGE\api\config.php" -Value $configContent -ErrorAction SilentlyContinue

Write-Host "[OK] Config created" -ForegroundColor Green
Write-Host ""

# Step 6: Create .htaccess
Write-Host "[INFO] Creating .htaccess files..." -ForegroundColor Cyan

$htaccessFrontend = @"
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !^/api/
    RewriteRule . /index.html [L]
</IfModule>
Options -Indexes
"@

Set-Content -Path "$UPLOAD_PACKAGE\public\.htaccess" -Value $htaccessFrontend -ErrorAction SilentlyContinue

Write-Host "[OK] .htaccess created" -ForegroundColor Green
Write-Host ""

# Summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "READY FOR UPLOAD!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Upload package: $UPLOAD_PACKAGE" -ForegroundColor Yellow
Write-Host ""
Write-Host "Files prepared:" -ForegroundColor Cyan
Write-Host "  - Frontend: $frontendFileCount files"
Write-Host "  - API: $apiFileCount files"
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host "  1. Edit: $UPLOAD_PACKAGE\api\config.php"
Write-Host "  2. Open FileZilla"
Write-Host "  3. Upload public/* to /public_html/"
Write-Host "  4. Upload api/* to /public_html/api/"
Write-Host ""
Write-Host "See UPDATE_PRODUCTION_THEME.md for detailed instructions" -ForegroundColor Cyan
Write-Host ""

# Open folder
$open = Read-Host "Open upload package folder? (y/n)"
if ($open -eq 'y') {
    Start-Process $UPLOAD_PACKAGE
}
