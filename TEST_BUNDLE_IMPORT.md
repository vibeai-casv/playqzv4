# 🧪 Local Bundle Import Test

## Test the bundle import locally to diagnose issues

### Steps:

1. **Open the app:**
   ```
   http://localhost:5173/aiq3/
   ```

2. **Login:**
   - Use your local credentials
   - Default: admin / password (or your local setup)

3. **Go to Import/Export:**
   ```
   Admin → Import/Export
   ```

4. **Open Browser Console:**
   - Press F12
   - Go to Console tab

5. **Import the bundle:**
   - Click "Import Bundle"
   - Select: `E:\projects\playqzv4\qbank\question_bundle_2025-12-14.zip`
   - Watch the console logs

6. **Check the logs:**
   - Look for detailed diagnostic messages
   - Should show each step of the import
   - Any errors will be clearly displayed

---

## Expected Console Output:

```
=== BUNDLE IMPORT STARTED ===
File: {name: "question_bundle_2025-12-14.zip", size: 1489925, ...}
Step 1: Creating FormData...
✅ FormData created
Step 2: Sending POST request to: http://localhost:8000/api/bundle/import.php
...
```

---

## What to Look For:

1. **File upload success?**
2. **Manifest parsing success?**
3. **Media extraction success?**
4. **Question import success or errors?**

---

## Server-Side Logs:

After import, check:
```
E:\projects\playqzv4\import_errors.log
```

This will have detailed server-side logs.

---

## Quick Test Command:

If you want to test just the import endpoint directly:

```powershell
# Test upload via curl (if available)
curl -X POST http://localhost:8000/api/bundle/import.php \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "bundle=@qbank/question_bundle_2025-12-14.zip"
```

---

**Test locally first to see the exact error!**
