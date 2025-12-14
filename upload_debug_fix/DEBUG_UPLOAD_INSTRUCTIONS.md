# 🐛 DEBUG VERSION - UPLOAD INSTRUCTIONS

## ✅ Enhanced with Comprehensive Debugging

This package includes detailed debugging to help identify the 404 error.

---

## 📦 What's in This Package

### Frontend (with debugging):
- Debug logging shows exact API URL being called
- Environment detection logs
- Enhanced error messages with troubleshooting tips
- **NEW: Test Connection button** to verify API accessibility

### Backend (with debugging):
- Comprehensive error logging
- Path detection and validation
- Step-by-step execution logs
- File existence checks

---

## 📤 UPLOAD INSTRUCTIONS

### **1. Upload Frontend Files**

```
From: upload_debug_fix/*  (everything EXCEPT api folder)
To:   /public_html/aiq2/
```

**Files to upload:**
- index.html
- assets/ folder
- aiq4.png
- Other static files

###  **2. Upload Backend File**

```
From: upload_debug_fix/api/admin/update-schema.php
To:   /public_html/aiq2/api/admin/update-schema.php
```

---

## ✅ TEST AFTER UPLOAD

### Step 1: Clear Browser Cache
```
Ctrl+F5 or Ctrl+Shift+R
```

### Step 2: Test API Connection
1. Go to: `Admin → System Tools`
2. Click **"Test API Connection"** button (NEW!)
3. Check the logs - it will show:
   - API URL being used
   - Files in admin directory
   - PHP version
   - Server time

### Step 3: Try Schema Update
1. Click **"Run Schema Update"** button
2. Check the detailed logs showing:
   - Environment (PRODUCTION/DEVELOPMENT)
   - API Base URL
   - Full endpoint URL
   - Current page location
   - Error details if any

---

## 🔍 DEBUGGING FEATURES

### Frontend Debug Output:
```
🔍 Debug Information:
  Environment: PRODUCTION
  API Base URL: /aiq2/api
  Full Endpoint: /aiq2/api/admin/update-schema.php
  Current Location: https://aiquiz.vibeai.cv/aiq2/admin/system

Starting database schema update...
```

### If 404 Error Occurs:
```
❌ Request Failed:
  Error: HTTP error! status: 404
  Type: Error

💡 Troubleshooting 404:
  1. Verify file exists on server
  2. Check: https://aiquiz.vibeai.cv/aiq2/api/admin/update-schema.php
  3. Ensure .htaccess is not blocking the file
  4. Check file permissions (should be 644)
```

### Backend Debug Logging:
The PHP script logs to error_log:
- Script execution start
- Request method
- File paths
- Config/DB file existence
- Each step of execution
- Success/failure of operations

---

## 🎯 What to Look For

After clicking "Run Schema Update", check the logs for:

1. **Environment:** Should say "PRODUCTION"
2. **API Base URL:** Should be `/aiq2/api`
3. **Full Endpoint:** Should be `/aiq2/api/admin/update-schema.php`
4. **Current Location:** Should start with `https://aiquiz.vibeai.cv/aiq2/`

If any of these are wrong, it will help us diagnose the issue.

---

## 💡 Using the Test Connection Button

The new "Test API Connection" button will:
1. Test if `/aiq2/api/admin/test.php` is accessible
2. Show all files in the admin directory
3. Display PHP version and server time
4. Confirm update-schema.php exists

**This helps verify:**
- ✅ API path is correct
- ✅ admin directory is accessible
- ✅ update-schema.php file exists on server
- ✅ PHP is executing correctly

---

## 📋 Upload Checklist

- [ ] Connect to FTP server
- [ ] Navigate to `/public_html/aiq2/`
- [ ] Upload ALL frontend files (index.html, assets/, etc.)
- [ ] Navigate to `/public_html/aiq2/api/admin/`
- [ ] Upload `update-schema.php` (overwrite existing)
- [ ] Clear browser cache (Ctrl+F5)
- [ ] Test API Connection first
- [ ] Then try Schema Update
- [ ] Check debug logs in both buttons

---

## 🔍 Server-Side Logs

After running, check your server's error logs (via cPanel or hosting panel):
- Look for lines starting with `[Schema Update]`
- These will show what the PHP script is doing
- Help identify if paths are correct
- Show database connection status

---

## 🆘 If Still 404

If you still get 404 after upload:

### Check Console (F12):
```javascript
// You'll see logs like:
"Schema update error:" Error {...}
"Full URL attempted:" "/aiq2/api/admin/update-schema.php"
```

### Test Directly:
Try accessing the PHP file directly:
```
https://aiquiz.vibeai.cv/aiq2/api/admin/update-schema.php
```

**Expected:** Some error (method not allowed) but NOT 404
**If 404:** File doesn't exist or path is wrong

---

## 📊 Expected Behavior

### Test Connection Success:
```
🔍 Testing API Connection...
API URL: /aiq2/api
Testing: /aiq2/api/admin/test.php
✅ Connection successful!
Server Time: 2025-12-14 04:44:07
PHP Version: 8.2.29

Files in admin directory:
  - analytics.php
  - generate_questions.php
  - test.php
  - toggle_user.php
  - update-schema.php  ← MUST BE PRESENT!
  - update_role.php
  - user_activity.php
  - users.php
```

### Schema Update Success:
```
🔍 Debug Information:
  Environment: PRODUCTION
  API Base URL: /aiq2/api
  Full Endpoint: /aiq2/api/admin/update-schema.php
  Current Location: https://aiquiz.vibeai.cv/aiq2/admin/system

Starting database schema update...
✅ Schema update completed successfully
ℹ️ No changes needed - schema is up to date

Database Statistics:
  - Tables Checked: 1
  - Fields Validated: 1
  - Fields Added: 0
  - Indexes Created: 0
```

---

## 🎯 Quick Reference

| Upload From | Upload To |
|-------------|-----------|
| `upload_debug_fix/*` (frontend) | `/public_html/aiq2/` |
| `upload_debug_fix/api/admin/update-schema.php` | `/public_html/aiq2/api/admin/update-schema.php` |

---

**This debug version will show exactly what's happening at each step!** 🔍

**Package Ready:** `E:\projects\playqzv4\upload_debug_fix\`
