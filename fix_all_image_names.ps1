# Fix All Personality Image Names
# This script renames existing hashed images to proper person names

Write-Host "============================================" -ForegroundColor Cyan
Write-Host " Fix Personality Image Names in Database" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

$personalityDir = "E:\projects\playqzv4\uploads\personality"

# Check if directory exists
if (!(Test-Path $personalityDir)) {
    Write-Host "Error: Directory not found: $personalityDir" -ForegroundColor Red
    exit 1
}

Write-Host "Current files in personality folder:" -ForegroundColor Yellow
Get-ChildItem $personalityDir -Filter "*.png" | Select-Object Name, Length | Format-Table

Write-Host ""
Write-Host "This script will:" -ForegroundColor Yellow
Write-Host "1. Identify which person each hashed filename belongs to" -ForegroundColor White
Write-Host "2. Rename files to proper names (e.g., SamAltman.png)" -ForegroundColor White
Write-Host "3. Update database to use new filenames" -ForegroundColor White
Write-Host ""

$confirm = Read-Host "Do you want to proceed? (Y/N)"

if ($confirm -ne "Y" -and $confirm -ne "y") {
    Write-Host "Cancelled." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "Renaming files and updating database..." -ForegroundColor Yellow
Write-Host ""

# Run the PHP update script
php update_image_filenames.php

Write-Host ""
Write-Host "Done! Check the output above for results." -ForegroundColor Green
Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
