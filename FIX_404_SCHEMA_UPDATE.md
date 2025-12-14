# 🔧 Troubleshooting 404 Error for Schema Update

## Problem
Getting error: `❌ Error: HTTP error! status: 404` when clicking "Run Schema Update"

---

## 🔍 Quick Diagnosis

The 404 error means the API endpoint file is not found on the server. This happens when:
1. ✅ The file exists in the package (`upload-schema.php` is present)
2. ❌ The file hasn't been uploaded to the server yet
3. ❌ The file is in the wrong location on the server

---

## ✅ SOLUTION: Upload the Missing File

### Option 1: Upload Just the Missing File (FASTEST)

**Upload this file via FTP:**

```
Local:  upload_package_aiq2_v7/api/admin/update-schema.php
Remote: /public_html/aiq2/api/admin/update-schema.php
```

**Steps:**
1. Open FileZilla/WinSCP
2. Connect to your server
3. Navigate to `/public_html/aiq2/api/admin/`
4. Upload `update-schema.php` from the package
5. Refresh the page and try again

---

### Option 2: Upload Entire API Directory (RECOMMENDED)

**Upload all API files to ensure nothing is missing:**

```
Local:  upload_package_aiq2_v7/api/*
Remote: /public_html/aiq2/api/
```

This ensures:
- `update-schema.php` is uploaded
- All other API files are up to date
- No other files are missing

---

## 🧪 Test the Upload

### 1. Test if admin directory exists:

Visit this URL in your browser:
```
https://aiquiz.vibeai.cv/aiq2/api/admin/test.php
```

**Expected response:**
```json
{
  "status": "ok",
  "message": "Admin API endpoint is accessible",
  "files_in_directory": [...]
}
```

If you get 404, the `/api/admin/` directory doesn't exist or wasn't uploaded.

### 2. Test if update-schema.php exists:

Try accessing directly (will show error but confirms file exists):
```
https://aiquiz.vibeai.cv/aiq2/api/admin/update-schema.php
```

**Expected:** Some response (even an error is OK - means file exists)
**404 Error:** File not uploaded

---

## 📁 Verify File Structure on Server

Your server should have this structure:

```
/public_html/aiq2/
├── index.html
├── assets/
│   └── [React bundles]
├── api/
│   ├── config.php
│   ├── db.php
│   ├── admin/
│   │   ├── update-schema.php  ← THIS FILE MUST EXIST!
│   │   ├── test.php           ← NEW test file
│   │   ├── users.php
│   │   └── [other admin files]
│   ├── auth/
│   ├── questions/
│   └── [other API files]
```

---

## 🚀 Quick Fix Steps

### IMMEDIATE FIX:

1. **Upload the file:**
   ```
   File: upload_package_aiq2_v7/api/admin/update-schema.php
   To:   /public_html/aiq2/api/admin/update-schema.php
   ```

2. **Verify upload:**
   ```
   Visit: https://aiquiz.vibeai.cv/aiq2/api/admin/test.php
   ```

3. **Test schema update:**
   ```
   Go to: Admin → System Tools → Run Schema Update
   ```

---

## 🔍 Debugging Checklist

- [ ] `update-schema.php` exists in local package
- [ ] Connected to FTP server
- [ ] Navigated to correct directory (`/public_html/aiq2/api/admin/`)
- [ ] Uploaded `update-schema.php`
- [ ] File permissions are correct (644)
- [ ] Tested with test.php endpoint
- [ ] Refreshed browser cache (Ctrl+F5)
- [ ] Tried schema update again

---

## 🎯 Alternative: Manual Upload via cPanel

If FTP isn't working:

1. Log in to cPanel
2. Open **File Manager**
3. Navigate to `/public_html/aiq2/api/admin/`
4. Click **Upload**
5. Select `update-schema.php` from `upload_package_aiq2_v7/api/admin/`
6. Wait for upload to complete
7. Verify file appears in directory
8. Try again

---

## 📊 Expected Files in /api/admin/

After upload, you should have these files:

```
/api/admin/
├── analytics.php
├── generate_questions.php
├── test.php                ← NEW
├── toggle_user.php
├── update-schema.php       ← REQUIRED!
├── update_role.php
├── user_activity.php
└── users.php
```

---

## ❓ Still Getting 404?

### Check these:

1. **File actually uploaded?**
   - Use FTP client to browse server
   - Verify file exists at `/public_html/aiq2/api/admin/update-schema.php`

2. **Correct path on server?**
   - Your hosting might use different structure
   - Common alternatives:
     - `/home/username/public_html/aiq2/api/admin/`
     - `/var/www/html/aiq2/api/admin/`
     - `/www/aiq2/api/admin/`

3. **File permissions?**
   - Should be: 644 (rw-r--r--)
   - Directory should be: 755 (rwxr-xr-x)

4. **.htaccess blocking?**
   - Check `/api/.htaccess`
   - Ensure it's not blocking `*.php` files

---

## 💡 Pro Tip

Upload the **test.php** file first to verify the admin directory is accessible:

```
1. Upload test.php to /aiq2/api/admin/
2. Visit: https://aiquiz.vibeai.cv/aiq2/api/admin/test.php
3. If OK → Upload update-schema.php
4. If 404 → Check directory structure
```

---

## 🎯 Quick Command (for future reference)

To re-upload all API files:

**Via FTP:**
```
From: upload_package_aiq2_v7/api/
To:   /public_html/aiq2/api/
Mode: Overwrite existing files
```

---

**Bottom Line:** You need to upload `update-schema.php` to your server. The file exists in the package but isn't on the production server yet.

---

**Files to Upload:**
1. `api/admin/update-schema.php` (REQUIRED)
2. `api/admin/test.php` (for testing)

**Where to Upload:**
- `/public_html/aiq2/api/admin/`

**Test URLs:**
- https://aiquiz.vibeai.cv/aiq2/api/admin/test.php
- https://aiquiz.vibeai.cv/aiq2/api/admin/update-schema.php
