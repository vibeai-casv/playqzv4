# Quick Fix for Missing Personality Images
Write-Host "======================================" -ForegroundColor Cyan
Write-Host " Fixing Missing Personality Images" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Create personality directory if it doesn't exist
$personalityDir = "E:\projects\playqzv4\uploads\personality"
if (!(Test-Path $personalityDir)) {
    Write-Host "Creating directory: $personalityDir" -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $personalityDir -Force | Out-Null
    Write-Host "Done: Directory created" -ForegroundColor Green
}
else {
    Write-Host "Done: Directory already exists" -ForegroundColor Green
}

Write-Host ""

# Copy images to fix the 404 errors
Write-Host "Uploading missing images..." -ForegroundColor Yellow

# Image 1
$source1 = "E:\projects\playqzv4\uploads\logo\samAltman.png"
$dest1 = "$personalityDir\69474d339860d1.72177150.png"

if (Test-Path $source1) {
    Copy-Item $source1 -Destination $dest1 -Force
    Write-Host "Done: 69474d339860d1.72177150.png" -ForegroundColor Green
}

# Image 2
$source2 = "E:\projects\playqzv4\uploads\logo\geoffery.png"
$dest2 = "$personalityDir\69468996ef6726.50835946.png"

if (Test-Path $source2) {
    Copy-Item $source2 -Destination $dest2 -Force
    Write-Host "Done: 69468996ef6726.50835946.png" -ForegroundColor Green
}

Write-Host ""
Write-Host "======================================"  -ForegroundColor Cyan
Write-Host "SUCCESS: Fix completed!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next: Refresh browser (Ctrl+F5)" -ForegroundColor Yellow
