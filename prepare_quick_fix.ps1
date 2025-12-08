$ErrorActionPreference = "Stop"

$sourceDir = "e:\projects\playqzv4"
$destDir = "e:\projects\playqzv4\upload_prod_update"

# Ensure directories exist
if (-not (Test-Path $destDir)) {
    New-Item -ItemType Directory -Path $destDir | Out-Null
}
if (-not (Test-Path "$destDir\api\admin")) {
    New-Item -ItemType Directory -Path "$destDir\api\admin" | Out-Null
}

Write-Host "Copying patched generate_questions.php..."
Copy-Item "$sourceDir\api\admin\generate_questions.php" "$destDir\api\admin\"

Write-Host "Done! File ready in $destDir\api\admin\generate_questions.php"
Write-Host "Please upload this file to your server's api/admin/ folder."
