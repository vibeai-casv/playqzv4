$ErrorActionPreference = "Stop"

$sourceDir = "e:\projects\playqzv4"
$destDir = "e:\projects\playqzv4\upload_prod_update"

# Create dest dir if not exists
if (-not (Test-Path $destDir)) {
    New-Item -ItemType Directory -Path $destDir | Out-Null
}

# Create subdirectories
$subDirs = @("api", "api\auth", "api\admin", "api\questions")
foreach ($dir in $subDirs) {
    $path = "$destDir\$dir"
    if (-not (Test-Path $path)) {
        New-Item -ItemType Directory -Path $path -Force | Out-Null
    }
}

Write-Host "Copying Backend Files..."
Copy-Item "$sourceDir\api\config.php" "$destDir\api\" -Force
Copy-Item "$sourceDir\api\auth\login.php" "$destDir\api\auth\" -Force
Copy-Item "$sourceDir\api\admin\generate_questions.php" "$destDir\api\admin\" -Force
Copy-Item "$sourceDir\api\questions\update.php" "$destDir\api\questions\" -Force
Copy-Item "$sourceDir\api\questions\list.php" "$destDir\api\questions\" -Force

Write-Host "Copying Frontend Build..."
Copy-Item "$sourceDir\client\dist\*" "$destDir" -Recurse -Force

Write-Host "Done! Files are ready in $destDir"
Write-Host "Please upload the CONTENTS of '$destDir' to your server's public_html folder."
