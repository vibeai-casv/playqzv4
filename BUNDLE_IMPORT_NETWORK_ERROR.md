# 🔍 Bundle Import Network Error - Diagnostic Guide

## Common Causes & Solutions

### 1. File Size Too Large

**Check server upload limits:**

PHP configuration might be blocking large uploads.

**Solution - Create this file:**

`/aiq3/api/bundle/.htaccess`

```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 256M
```

---

### 2. CORS Issue

**Check if API endpoint is being blocked.**

**Solution - Update:**

`/aiq3/api/bundle/.htaccess`

```apache
# CORS Headers
<IfModule mod_headers.c>
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
    Header always set Access-Control-Max-Age "3600"
</IfModule>

# Handle OPTIONS
RewriteEngine On
RewriteCond %{REQUEST_METHOD} OPTIONS
RewriteRule ^(.*)$ $1 [R=200,L]
```

---

### 3. Wrong API URL

**Check console logs (F12):**

Look for the line:
```
Step 2: Sending POST request to: [URL]
```

**Should be:**
```
/aiq3/api/bundle/import.php
```

**NOT:**
```
https://aiquiz.vibeai.cv/api/bundle/import.php (wrong!)
```

---

### 4. File Not Found (404)

**Test the endpoint directly:**

Visit in browser:
```
https://aiquiz.vibeai.cv/aiq3/api/bundle/import.php
```

**Expected:** JSON error (method not allowed) - this is OK
**Wrong:** 404 Not Found - file not uploaded

---

### 5. Server Timeout

**File too large or processing too slow.**

**Check:**
- Bundle size (should be < 10MB)
- Server PHP timeout settings
- Database connection

---

## 🧪 Diagnostic Steps

### Step 1: Open Console (F12)

Before importing, open browser console:
```
F12 → Console tab
```

### Step 2: Look for Detailed Error

When you try to import, check console for:
```
=== BUNDLE IMPORT STARTED ===
...
Axios Error: {
    message: "...",
    code: "ERR_NETWORK",
    status: undefined,
    requestURL: "..."
}
```

**The `code` and `requestURL` will tell us the exact issue.**

---

## 🔍 Common Error Codes

### `ERR_NETWORK`
- **Cause:** Cannot reach server
- **Fix:** Check URL, check file exists

### `ERR_BAD_REQUEST` (400)
- **Cause:** Invalid request format
- **Fix:** Check file is valid ZIP

### `413 Payload Too Large`
- **Cause:** File exceeds server limit
- **Fix:** Increase PHP upload limits

### `500 Internal Server Error`
- **Cause:** PHP error on server
- **Fix:** Check server error logs

### `404 Not Found`
- **Cause:** Endpoint doesn't exist
- **Fix:** Verify `/aiq3/api/bundle/import.php` exists

---

## 🛠️ Quick Fixes

### Fix 1: Create .htaccess for bundle endpoint

Create `/aiq3/api/bundle/.htaccess`:

```apache
# Bundle Import Configuration
RewriteEngine On

# CORS
<IfModule mod_headers.c>
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "POST, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>

# OPTIONS
RewriteCond %{REQUEST_METHOD} OPTIONS
RewriteRule ^(.*)$ $1 [R=200,L]

# PHP Settings
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 256M
```

---

### Fix 2: Test with smaller file

Create a minimal test bundle (1 question) to rule out file size issues.

---

### Fix 3: Check server error logs

Look for:
```
/aiq3/import_errors.log
/public_html/error_log
```

---

## 📞 Share This Info

When reporting the issue, share from console (F12):

1. **Full error object:**
   ```
   Axios Error: { message, code, status, requestURL }
   ```

2. **Request URL attempted:**
   ```
   Step 2: Sending POST request to: [URL]
   ```

3. **File size:**
   ```
   File: {name, size in bytes}
   ```

4. **Any network errors from Network tab**

---

## 🎯 Most Likely Causes

Based on "Network error - check connection":

1. **Upload size limit** (most common)
2. **File not found** (wrong upload path)
3. **CORS blocking** (no .htaccess in bundle dir)
4. **Wrong API URL** (path detection issue)

---

## ✅ Quick Test

Try this import URL directly:
```
https://aiquiz.vibeai.cv/aiq3/api/bundle/import.php
```

**Should see:**
```json
{"error": "Method not allowed"}
```

**This confirms the file exists and is accessible.**

---

**Check console logs and share the exact error details!** 🔍
