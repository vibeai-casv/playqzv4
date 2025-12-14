# Fix Bundle Export Issue

## Problem

The bundle export feature is failing with this error:
```
Fatal error: Uncaught Error: Class "ZipArchive" not found
```

This happens because the **PHP ZIP extension** is not enabled in your PHP installation.

## Solution

You need to enable the ZIP extension in your `php.ini` file.

### Steps to Fix:

1. **Locate your `php.ini` file**:
   - For XAMPP: Usually at `T:\server\php\php.ini` or `C:\xampp\php\php.ini`
   - Run this to find it:
     ```powershell
     php --ini
     ```

2. **Edit `php.ini`**:
   - Open the file in a text editor (as Administrator)
   - Find this line (it might have a semicolon `;` in front):
     ```ini
     ;extension=zip
     ```
   - Remove the semicolon to enable it:
     ```ini
     extension=zip
     ```

3. **Restart the PHP server**:
   - Stop the current PHP development server (Ctrl+C in the terminal)
   - Start it again:
     ```powershell
     php -S localhost:8000 -t e:\projects\playqzv4\api
     ```

4. **Verify ZIP extension is loaded**:
   ```powershell
   php -m | Select-String "zip"
   ```
   You should see "zip" in the list of loaded extensions.

## Alternative: Use Bundled PHP (if XAMPP's PHP doesn't work)

If you're using XAMPP, make sure you're using XAMPP's PHP executable, not a system-wide PHP:

```powershell
# Check which PHP you're using
Get-Command php | Select-Object Source

# If it's not XAMPP's PHP, use the full path:
T:\server\php\php.exe -S localhost:8000 -t e:\projects\playqzv4\api
```

## Test After Fix

After enabling the ZIP extension and restarting the server:

1. Go to http://localhost:5173/aiq2/admin/import-export
2. Click "Export Bundle (ZIP)"
3. Select some questions
4. Click "Export Bundle"
5. The download should work and create a valid ZIP file

The ZIP file should contain:
- `manifest.json` - Question data
- `media/` folder - Any images associated with the questions
