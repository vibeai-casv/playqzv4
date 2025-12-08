$ErrorActionPreference = "Stop"

$sourceDir = "e:\projects\playqzv4"
$destDir = "e:\projects\playqzv4\upload_prod_update"

# Clean up previous
if (Test-Path $destDir) {
    Remove-Item -Path $destDir -Recurse -Force
}
New-Item -ItemType Directory -Path $destDir | Out-Null
New-Item -ItemType Directory -Path "$destDir\api" | Out-Null
New-Item -ItemType Directory -Path "$destDir\api\auth" | Out-Null
New-Item -ItemType Directory -Path "$destDir\api\admin" | Out-Null

Write-Host "Copying Backend Files..."
Copy-Item "$sourceDir\api\auth\login.php" "$destDir\api\auth\"
Copy-Item "$sourceDir\api\admin\generate_questions.php" "$destDir\api\admin\"
Copy-Item "$sourceDir\api\config.php" "$destDir\api\"

Write-Host "Copying Frontend Build..."
Copy-Item "$sourceDir\client\dist\*" "$destDir" -Recurse

Write-Host "Done! Files are ready in $destDir"
Write-Host "Upload contents of '$destDir' to your server's public_html folder."
