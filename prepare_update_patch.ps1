$ErrorActionPreference = "Stop"

$sourceDir = "e:\projects\playqzv4"
$destDir = "e:\projects\playqzv4\upload_prod_update"

# Ensure directories exist
if (-not (Test-Path $destDir)) {
    New-Item -ItemType Directory -Path $destDir | Out-Null
}
if (-not (Test-Path "$destDir\api\questions")) {
    New-Item -ItemType Directory -Path "$destDir\api\questions" | Out-Null
}

Write-Host "Copying patched update.php..."
Copy-Item "$sourceDir\api\questions\update.php" "$destDir\api\questions\"

Write-Host "Done! File ready in $destDir\api\questions\update.php"
Write-Host "Please upload this file to your server's api/questions/ folder."
