# 🔧 Quick Fix for Network Error

## The Problem

"Network error - check connection" when importing bundles usually means:

1. **Upload size limit exceeded** (most common)
2. **CORS blocking the request**
3. **PHP timeout**

---

## ✅ Quick Fix

### Upload this file:

**File:** `AIQ3_FINAL/api/bundle/.htaccess`  
**Upload to:** `/aiq3/api/bundle/.htaccess`

This file contains:
- ✅ Increased upload limits (50MB)
- ✅ CORS headers
- ✅ Extended timeout (300 seconds)
- ✅ Increased memory limit (256MB)

---

## 🧪 Test Steps

### 1. Upload the .htaccess file

```
Upload: AIQ3_FINAL/api/bundle/.htaccess
To: /aiq3/api/bundle/.htaccess
```

### 2. Test the endpoint

Visit in browser:
```
https://aiquiz.vibeai.cv/aiq3/api/bundle/import.php
```

**Expected response:**
```json
{"error":"Method not allowed"}
```

This confirms the file is accessible.

### 3. Try import again

- Go to: Admin → Import/Export  
- Upload: `question_bundle_FIXED.zip`
- Watch console (F12)

---

## 🔍 If Still Fails

### Check Console Logs:

Press F12, look for:
```
=== BUNDLE IMPORT FAILED ===
Axios Error: {
    code: "...",
    status: ...,
    message: "..."
}
```

### Check These:

1. **API URL detected:**
   ```
   Should be: /aiq3/api/bundle/import.php
   NOT: /api/bundle/import.php or https://...
   ```

2. **File size:**
   ```
   question_bundle_FIXED.zip should be ~1.4 MB
   Well within 50MB limit
   ```

3. **Direct endpoint test:**
   Visit the URL directly (should return JSON)

---

## 📊 Common Scenarios

### Scenario 1: Upload Size
**Error:** `413 Payload Too Large`  
**Fix:** .htaccess increases limit to 50MB ✅

### Scenario 2: CORS
**Error:** `ERR_FAILED` in Network tab  
**Fix:** .htaccess adds CORS headers ✅

### Scenario 3: Timeout
**Error:** Request hangs, then fails  
**Fix:** .htaccess increases timeout to 300s ✅

### Scenario 4: Wrong URL
**Error:** `404 Not Found`  
**Fix:** Check path detection shows `/aiq3/api`

---

## 🎯 Action Plan

1. **Upload** `.htaccess` to `/aiq3/api/bundle/`
2. **Test** endpoint directly in browser
3. **Try import** again
4. **Check console** (F12) for detailed error
5. **Share** the exact error message if still fails

---

**The .htaccess file should fix upload size and CORS issues!** ✅

**File Location:** `E:\projects\playqzv4\AIQ3_FINAL\api\bundle\.htaccess`  
**Upload to:** `/public_html/aiq3/api/bundle/.htaccess`
