# Upload Personality Images with Proper Names
# This script helps you upload images from your local drive

Write-Host "======================================" -ForegroundColor Cyan
Write-Host " Upload Personality Images" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

$destFolder = "E:\projects\playqzv4\uploads\personality"

# Ensure destination folder exists
if (!(Test-Path $destFolder)) {
    New-Item -ItemType Directory -Path $destFolder -Force | Out-Null
    Write-Host "Created directory: $destFolder" -ForegroundColor Green
}

Write-Host "Please select the folder containing your personality images..." -ForegroundColor Yellow
Write-Host ""

# Function to select folder
Add-Type -AssemblyName System.Windows.Forms
$folderBrowser = New-Object System.Windows.Forms.FolderBrowserDialog
$folderBrowser.Description = "Select folder with personality images"
$folderBrowser.RootFolder = "MyComputer"

if ($folderBrowser.ShowDialog() -eq "OK") {
    $sourceFolder = $folderBrowser.SelectedPath
    Write-Host "Source folder: $sourceFolder" -ForegroundColor Cyan
    Write-Host ""
    
    # Get all image files
    $images = Get-ChildItem -Path $sourceFolder -Filter "*.png" -ErrorAction SilentlyContinue
    $jpgImages = Get-ChildItem -Path $sourceFolder -Filter "*.jpg" -ErrorAction SilentlyContinue
    $allImages = $images + $jpgImages
    
    if ($allImages.Count -eq 0) {
        Write-Host "No PNG or JPG files found in selected folder!" -ForegroundColor Red
        Write-Host "Press any key to exit..."
        $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
        exit
    }
    
    Write-Host "Found $($allImages.Count) image(s)" -ForegroundColor Green
    Write-Host ""
    Write-Host "Files to be uploaded:" -ForegroundColor Yellow
    
    foreach ($img in $allImages) {
        Write-Host "  - $($img.Name)" -ForegroundColor White
    }
    
    Write-Host ""
    $confirm = Read-Host "Upload these files to $destFolder? (Y/N)"
    
    if ($confirm -eq "Y" -or $confirm -eq "y") {
        Write-Host ""
        Write-Host "Uploading files..." -ForegroundColor Yellow
        
        $uploaded = 0
        foreach ($img in $allImages) {
            # Convert JPG to PNG if needed
            $destName = $img.Name
            if ($img.Extension -eq ".jpg" -or $img.Extension -eq ".jpeg") {
                $destName = $img.BaseName + ".png"
            }
            
            $destPath = Join-Path $destFolder $destName
            Copy-Item $img.FullName -Destination $destPath -Force
            Write-Host "  Uploaded: $destName" -ForegroundColor Green
            $uploaded++
        }
        
        Write-Host ""
        Write-Host "======================================" -ForegroundColor Cyan
        Write-Host "SUCCESS: Uploaded $uploaded file(s)!" -ForegroundColor Green
        Write-Host "======================================" -ForegroundColor Cyan
        Write-Host ""
        
        # Show uploaded files
        Write-Host "Files in $destFolder" ":" -ForegroundColor Cyan
        Get-ChildItem $destFolder -Filter "*.png" | Format-Table Name, Length -AutoSize
        
        Write-Host ""
        Write-Host "Next steps:" -ForegroundColor Yellow
        Write-Host "1. Go to http://projects/playqzv4/client/dist/admin/questions" -ForegroundColor White
        Write-Host "2. Click 'Edit' on each personality question" -ForegroundColor White
        Write-Host "3. Use 'Select Media' to choose the new image" -ForegroundColor White
        Write-Host "4. Save the question" -ForegroundColor White
        
    } else {
        Write-Host "Upload cancelled." -ForegroundColor Yellow
    }
    
} else {
    Write-Host "No folder selected. Exiting..." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
