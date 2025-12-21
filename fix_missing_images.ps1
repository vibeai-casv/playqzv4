# Quick Fix for Missing Personality Images
# This script creates the missing image files by copying existing ones

Write-Host "======================================" -ForegroundColor Cyan
Write-Host " Fixing Missing Personality Images" -ForegroundColor Cyan
Write-Host "======================================"  -ForegroundColor Cyan
Write-Host ""

# Create personality directory if it doesn't exist
$personalityDir = "E:\projects\playqzv4\uploads\personality"
if (!(Test-Path $personalityDir)) {
    Write-Host "Creating directory: $personalityDir" -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $personalityDir -Force | Out-Null
    Write-Host "✓ Directory created" -ForegroundColor Green
} else {
    Write-Host "✓ Directory already exists" -ForegroundColor Green
}

Write-Host ""

# Check if source images exist
$logoDir = "E:\projects\playqzv4\uploads\logo"
if (!(Test-Path $logoDir)) {
    Write-Host "✗ Error: Source directory not found: $logoDir" -ForegroundColor Red
    exit 1
}

# Copy images to fix the 404 errors
Write-Host "Uploading missing images..." -ForegroundColor Yellow

# Image 1: 69474d339860d1.72177150.png
$source1 = "$logoDir\samAltman.png"
$dest1 = "$personalityDir\69474d339860d1.72177150.png"

if (Test-Path $source1) {
    Copy-Item $source1 -Destination $dest1 -Force
    Write-Host "✓ Uploaded: 69474d339860d1.72177150.png" -ForegroundColor Green
} else {
    Write-Host "✗ Warning: Source image not found: samAltman.png" -ForegroundColor Yellow
}

# Image 2: 69468996ef6726.50835946.png
$source2 = "$logoDir\geoffery.png"
$dest2 = "$personalityDir\69468996ef6726.50835946.png"

if (Test-Path $source2) {
    Copy-Item $source2 -Destination $dest2 -Force
    Write-Host "✓ Uploaded: 69468996ef6726.50835946.png" -ForegroundColor Green
} else {
    Write-Host "✗ Warning: Source image not found: geoffery.png" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "======================================"  -ForegroundColor Cyan
Write-Host "✓ Fix completed successfully!" -ForegroundColor Green
Write-Host "======================================"  -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Refresh your browser (Ctrl+F5)" -ForegroundColor White
Write-Host "2. Check http://projects/playqzv4/client/dist/admin/questions" -ForegroundColor White
Write-Host "3. Verify no more 404 errors in console" -ForegroundColor White
Write-Host ""

# Verify the files were created
Write-Host "Verifying uploaded files..." -ForegroundColor Yellow
if ((Test-Path $dest1) -and (Test-Path $dest2)) {
    Write-Host "✓ Both images uploaded successfully!" -ForegroundColor Green
    
    # Show file details
    Write-Host ""
    Write-Host "File Details:" -ForegroundColor Cyan
    Get-ChildItem $personalityDir | Where-Object {$_.Name -like "6946*" -or $_.Name -like "6947*"} | Format-Table Name, Length, LastWriteTime -AutoSize
} else {
    Write-Host "✗ Some files may not have been uploaded correctly" -ForegroundColor Red
    Write-Host "Please check the output above for errors" -ForegroundColor Red
}

Write-Host ""
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
