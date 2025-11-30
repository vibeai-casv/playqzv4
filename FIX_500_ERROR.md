# 🔧 Fix 500 Error - Quick Steps

## Error: 500 Internal Server Error on import.php

You're getting a **500 error** which means PHP is crashing. Let's diagnose and fix it.

---

## 🚀 Quick Fix Steps

### Step 1: Upload New Files

Upload these **3 files** to your production server:

**File 1: Simplified import.php**
```
Local:  e:\projects\playqzv4\api\questions\import.php
Remote: /public_html/api/questions/import.php
Action: OVERWRITE
```

**File 2: Diagnostic script**
```
Local:  e:\projects\playqzv4\api\questions\diagnostic.php
Remote: /public_html/api/questions/diagnostic.php
Action: NEW FILE
```

**File 3: Test script**
```
Local:  e:\projects\playqzv4\api\questions\test-import.php
Remote: /public_html/api/questions/test-import.php
Action: NEW FILE (if not already uploaded)
```

---

### Step 2: Check Diagnostic

Visit this URL in your browser:
```
https://aiquiz.vibeai.cv/api/questions/diagnostic.php
```

**Look for**:
- `"php_version_ok": true` ← Should be true
- `"config_loaded": true` ← Should be true
- `"db_connected": true` ← Should be true
- `"verifyAuth_exists": true` ← Should be true

**If any are false**, that's the problem!

---

### Step 3: Check Error Log

In cPanel:
1. Go to **Error Logs**
2. Look for recent errors
3. Find errors related to `/api/questions/import.php`
4. **Copy the error message** and send it to me

---

## 🐛 Common Causes of 500 Error

### Cause 1: PHP Version Too Old

**Symptom**: `"php_version_ok": false`

**Fix**: Your hosting needs PHP 7.0 or higher
- Contact hosting support to upgrade PHP

### Cause 2: Missing config.php

**Symptom**: `"config_loaded": false`

**Fix**: Upload `config.php` to `/public_html/api/`

### Cause 3: Database Connection Failed

**Symptom**: `"db_connected": false`

**Fix**: Check `config.php` has correct database credentials

### Cause 4: Syntax Error in PHP

**Symptom**: Error log shows "Parse error" or "Syntax error"

**Fix**: The new simplified `import.php` should fix this

---

## 📝 What I Changed

### Old Version (causing 500 error):
- Used PHP 7.4+ syntax
- Used `[]` for arrays
- Had complex error handling

### New Version (compatible):
- Uses PHP 5.6+ syntax
- Uses `array()` instead of `[]`
- Simplified error handling
- Better file path handling

---

## 🧪 Test Locally First

Before uploading, test locally:

```powershell
# In browser, visit:
http://localhost/api/questions/diagnostic.php
```

Should show all checks passing.

---

## 🎯 After Upload

1. **Upload the 3 files** via FTP
2. **Visit diagnostic URL**:
   ```
   https://aiquiz.vibeai.cv/api/questions/diagnostic.php
   ```
3. **Copy the output** and send it to me
4. **Try importing** again
5. **Check browser console** for new error (if any)

---

## 📊 Expected Diagnostic Output

```json
{
  "php_version": "7.4.33",
  "php_version_ok": true,
  "config_exists": true,
  "db_exists": true,
  "utils_exists": true,
  "config_loaded": true,
  "db_name": "aiqz_production",
  "allowed_origin": "https://aiquiz.vibeai.cv",
  "db_connected": true,
  "db_type": "mysql",
  "utils_loaded": true,
  "verifyAuth_exists": true,
  "can_write": true,
  "json_encode_exists": true,
  "json_decode_exists": true,
  "pdo_available": true,
  "pdo_drivers": ["mysql"]
}
```

If anything is `false` or missing, that's the problem!

---

## 🆘 If Still 500 Error

**Send me**:
1. Output from `diagnostic.php`
2. Error log from cPanel
3. Screenshot of browser console

I'll give you the exact fix! 🎯

---

**Upload the files and run the diagnostic!** 🚀
