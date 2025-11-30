# Production Deployment Script for aiquiz.vibeai.cv (Windows PowerShell)
# This script helps automate the deployment process

$ErrorActionPreference = "Stop"

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "AI Quiz - Production Deployment" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Configuration - UPDATE THESE VALUES
$REMOTE_USER = "your_username"  # Change this
$REMOTE_HOST = "aiquiz.vibeai.cv"
$REMOTE_PATH = "/var/www/aiquiz.vibeai.cv"
$LOCAL_CLIENT_DIST = ".\client\dist"
$LOCAL_API = ".\api"

# Functions
function Print-Success {
    param($Message)
    Write-Host "✓ $Message" -ForegroundColor Green
}

function Print-Error {
    param($Message)
    Write-Host "✗ $Message" -ForegroundColor Red
}

function Print-Warning {
    param($Message)
    Write-Host "⚠ $Message" -ForegroundColor Yellow
}

function Print-Info {
    param($Message)
    Write-Host "ℹ $Message" -ForegroundColor White
}

# Check if we're in the right directory
if (-not (Test-Path ".\client") -and -not (Test-Path ".\api")) {
    Print-Error "Please run this script from the project root directory"
    exit 1
}

# Step 1: Build Frontend
Write-Host ""
Print-Info "Step 1: Building frontend..."

Push-Location client

if (-not (Test-Path "node_modules")) {
    Print-Warning "node_modules not found. Installing dependencies..."
    npm install
}

npm run build

if ($LASTEXITCODE -eq 0) {
    Print-Success "Frontend build completed"
} else {
    Print-Error "Frontend build failed"
    Pop-Location
    exit 1
}

Pop-Location

# Step 2: Verify build output
Write-Host ""
Print-Info "Step 2: Verifying build output..."

if (Test-Path "$LOCAL_CLIENT_DIST\index.html") {
    Print-Success "Build output verified"
} else {
    Print-Error "Build output not found"
    exit 1
}

# Step 3: Check for SCP/SSH tools
Write-Host ""
Print-Info "Step 3: Checking for deployment tools..."

$scpAvailable = Get-Command scp -ErrorAction SilentlyContinue
$sshAvailable = Get-Command ssh -ErrorAction SilentlyContinue

if (-not $scpAvailable -or -not $sshAvailable) {
    Print-Error "SCP/SSH not found. Please install OpenSSH or use WSL."
    Print-Info "Install OpenSSH: Settings > Apps > Optional Features > OpenSSH Client"
    exit 1
}

Print-Success "Deployment tools available"

# Step 4: Test SSH connection
Write-Host ""
Print-Info "Step 4: Testing SSH connection..."

$sshTest = ssh -o ConnectTimeout=5 "$REMOTE_USER@$REMOTE_HOST" "echo 'Connection successful'" 2>&1

if ($LASTEXITCODE -eq 0) {
    Print-Success "SSH connection successful"
} else {
    Print-Error "Cannot connect to server. Please check your SSH credentials."
    exit 1
}

# Step 5: Create backup
Write-Host ""
Print-Info "Step 5: Creating backup on server..."

$BACKUP_DATE = Get-Date -Format "yyyyMMdd_HHmmss"

ssh "$REMOTE_USER@$REMOTE_HOST" @"
    if [ -d '$REMOTE_PATH' ]; then
        echo 'Creating backup...'
        sudo tar -czf /var/backups/aiquiz_backup_$BACKUP_DATE.tar.gz $REMOTE_PATH 2>/dev/null || true
        echo 'Backup created: /var/backups/aiquiz_backup_$BACKUP_DATE.tar.gz'
    fi
"@

Print-Success "Backup completed"

# Step 6: Upload frontend files
Write-Host ""
Print-Info "Step 6: Uploading frontend files..."

# Create a temporary directory for upload
$tempDir = Join-Path $env:TEMP "aiquiz_deploy"
if (Test-Path $tempDir) {
    Remove-Item -Recurse -Force $tempDir
}
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Copy files to temp directory
Copy-Item -Recurse "$LOCAL_CLIENT_DIST\*" $tempDir

# Upload using SCP
scp -r "$tempDir\*" "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/public/" 2>&1 | Out-Null

if ($LASTEXITCODE -eq 0) {
    Print-Success "Frontend files uploaded"
} else {
    Print-Error "Failed to upload frontend files"
    Remove-Item -Recurse -Force $tempDir
    exit 1
}

# Cleanup temp directory
Remove-Item -Recurse -Force $tempDir

# Step 7: Upload API files
Write-Host ""
Print-Info "Step 7: Uploading API files..."

# Get all API files except sensitive ones
$apiFiles = Get-ChildItem -Path $LOCAL_API -Recurse | 
    Where-Object { 
        $_.Name -notmatch "config\.php" -and 
        $_.Name -notmatch "\.log$" -and 
        $_.Name -notmatch "^test.*\.php$"
    }

# Create temp directory for API files
$apiTempDir = Join-Path $env:TEMP "aiquiz_api"
if (Test-Path $apiTempDir) {
    Remove-Item -Recurse -Force $apiTempDir
}
New-Item -ItemType Directory -Path $apiTempDir | Out-Null

# Copy API files
Copy-Item -Path "$LOCAL_API\*" -Destination $apiTempDir -Recurse -Exclude "config.php","*.log","test*.php"

# Upload API files
scp -r "$apiTempDir\*" "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/api/" 2>&1 | Out-Null

if ($LASTEXITCODE -eq 0) {
    Print-Success "API files uploaded"
} else {
    Print-Error "Failed to upload API files"
    Remove-Item -Recurse -Force $apiTempDir
    exit 1
}

# Cleanup
Remove-Item -Recurse -Force $apiTempDir

# Step 8: Set permissions
Write-Host ""
Print-Info "Step 8: Setting file permissions..."

ssh "$REMOTE_USER@$REMOTE_HOST" @"
    sudo chown -R www-data:www-data $REMOTE_PATH
    sudo find $REMOTE_PATH -type d -exec chmod 755 {} \;
    sudo find $REMOTE_PATH -type f -exec chmod 644 {} \;
    sudo chmod 755 $REMOTE_PATH/api/*.php
"@

Print-Success "Permissions set"

# Step 9: Restart web server
Write-Host ""
Print-Info "Step 9: Restarting web server..."

ssh "$REMOTE_USER@$REMOTE_HOST" @"
    if systemctl is-active --quiet apache2; then
        sudo systemctl restart apache2
        echo 'Apache restarted'
    elif systemctl is-active --quiet nginx; then
        sudo systemctl restart nginx
        echo 'Nginx restarted'
    fi
"@

Print-Success "Web server restarted"

# Step 10: Test deployment
Write-Host ""
Print-Info "Step 10: Testing deployment..."

try {
    $response = Invoke-WebRequest -Uri "https://$REMOTE_HOST" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Print-Success "Website is accessible (HTTP $($response.StatusCode))"
    } else {
        Print-Warning "Website returned HTTP $($response.StatusCode)"
    }
} catch {
    Print-Warning "Could not test website accessibility: $($_.Exception.Message)"
}

# Summary
Write-Host ""
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Deployment Summary" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""
Print-Info "Deployment completed at: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
Print-Info "Server: $REMOTE_HOST"
Print-Info "Backup: /var/backups/aiquiz_backup_$BACKUP_DATE.tar.gz"
Write-Host ""
Print-Success "Deployment successful!"
Write-Host ""
Print-Info "Next steps:"
Write-Host "  1. Visit https://$REMOTE_HOST to verify"
Write-Host "  2. Test login functionality"
Write-Host "  3. Check error logs if needed"
Write-Host "  4. Monitor server performance"
Write-Host ""
Print-Warning "Remember to:"
Write-Host "  - Update production config.php with correct credentials"
Write-Host "  - Change default admin password"
Write-Host "  - Verify SSL certificate"
Write-Host ""

# Open browser to the site
$openBrowser = Read-Host "Open website in browser? (y/n)"
if ($openBrowser -eq 'y') {
    Start-Process "https://$REMOTE_HOST"
}
