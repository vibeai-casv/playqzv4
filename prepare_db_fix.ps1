# Prepare Database Fix Files for Upload
# This script packages the diagnostic tools for easy FTP upload

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Database Fix Package Preparation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$projectRoot = $PSScriptRoot
$fixDir = Join-Path $projectRoot "fix"
$outputDir = Join-Path $projectRoot "upload_db_fix"

# Create output directory
Write-Host "Creating package directory..." -ForegroundColor Yellow
if (Test-Path $outputDir) {
    Remove-Item $outputDir -Recurse -Force
}
New-Item -ItemType Directory -Path $outputDir | Out-Null
New-Item -ItemType Directory -Path (Join-Path $outputDir "fix") | Out-Null

# Copy diagnostic script
Write-Host "Packaging diagnostic tool..." -ForegroundColor Yellow
Copy-Item (Join-Path $fixDir "check-db-connection.php") (Join-Path $outputDir "fix\check-db-connection.php")

# Create README for upload
Write-Host "Creating upload instructions..." -ForegroundColor Yellow
$readmeContent = @"
# DATABASE FIX UPLOAD PACKAGE
Generated: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")

## What's in this package

fix/check-db-connection.php  → Upload to: /aiq2/fix/check-db-connection.php

## Upload Instructions

### Using FileZilla or WinSCP:

1. Connect to your server (aiquiz.vibeai.cv)
2. Navigate to /aiq2/fix/ directory
3. Upload check-db-connection.php
4. Visit: https://aiquiz.vibeai.cv/aiq2/fix/check-db-connection.php
5. The script will tell you exactly what's wrong

### What to expect:

The diagnostic script will return JSON output showing:
- Whether config.php is found
- Current database configuration
- Connection test result
- Specific error messages if any
- Step-by-step solutions

## Next Steps After Upload

1. Visit the diagnostic URL
2. If it shows "PLACEHOLDER PASSWORD NOT CHANGED", that confirms the issue
3. Follow the instructions in URGENT_DATABASE_FIX.md to fix it
4. The main fix is updating /aiq2/api/config.php with real database credentials

## Getting Database Credentials

Log in to your hosting control panel (cPanel/Plesk):
1. Find "MySQL Databases" section
2. Look for your database name (might be like: username_dbname)
3. Look for your database user (might be like: username_user)
4. If you don't know the password, reset it there
5. Note down all three values
6. Update /aiq2/api/config.php lines 6-8 with these values

## Quick Reference

| What | Current Value | Where to Update |
|------|---------------|-----------------|
| DB Host | localhost | Line 5 in config.php |
| DB Name | aiqz_production | Line 6 in config.php (update to your actual DB name) |
| DB User | aiqz_user | Line 7 in config.php (update to your actual DB user) |
| DB Pass | YOUR_PASSWORD_HERE | Line 8 in config.php (MUST UPDATE!) |

## Support

If you get stuck, paste the JSON output from check-db-connection.php and I can help diagnose.

## Security Note

After fixing the database connection issue:
- DELETE check-db-connection.php from the server
- DELETE update_schema.php after running it successfully
- These diagnostic files expose database configuration information
"@

Set-Content -Path (Join-Path $outputDir "README.txt") -Value $readmeContent

# Create a summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  ✅ Package Ready!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Package Location:" -ForegroundColor Cyan
Write-Host "  $outputDir" -ForegroundColor White
Write-Host ""
Write-Host "Files to Upload:" -ForegroundColor Cyan
Write-Host "  • fix/check-db-connection.php → /aiq2/fix/" -ForegroundColor White
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host "  1. Open FileZilla/WinSCP and connect to aiquiz.vibeai.cv" -ForegroundColor Yellow
Write-Host "  2. Navigate to /aiq2/fix/ on the server" -ForegroundColor Yellow  
Write-Host "  3. Upload check-db-connection.php" -ForegroundColor Yellow
Write-Host "  4. Visit: https://aiquiz.vibeai.cv/aiq2/fix/check-db-connection.php" -ForegroundColor Yellow
Write-Host "  5. Follow the diagnostic output" -ForegroundColor Yellow
Write-Host ""
Write-Host "Quick Fix:" -ForegroundColor Cyan
Write-Host "  The main issue is in /aiq2/api/config.php line 8" -ForegroundColor White
Write-Host "  Change: define('DB_PASS', 'YOUR_PASSWORD_HERE');" -ForegroundColor Red
Write-Host "  To:     define('DB_PASS', 'your_actual_password');" -ForegroundColor Green
Write-Host ""
Write-Host "Get your database credentials from your hosting control panel!" -ForegroundColor Magenta
Write-Host ""

# Open the output directory
Start-Process explorer.exe -ArgumentList $outputDir

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
