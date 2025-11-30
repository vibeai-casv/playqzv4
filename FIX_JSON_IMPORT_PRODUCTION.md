# 🚀 Fix JSON Import on Production Server

## Issue: Import Failed on Production

The JSON import is failing on **aiquiz.vibeai.cv**. I've fixed the code with better error handling.

---

## ✅ Files Updated

1. **`api/questions/import.php`** - Enhanced with:
   - Better error messages
   - Transaction support
   - Field validation
   - Detailed logging

2. **`client/src/components/admin/JSONImporter.tsx`** - Enhanced with:
   - Detailed error display
   - Partial import support
   - Better error messages

---

## 🚀 Deploy Fix to Production

### Step 1: Build Frontend

```powershell
cd e:\projects\playqzv4\client
npm run build
```

### Step 2: Upload Files via FTP

**Upload these 2 files**:

1. **Backend** (upload to `/public_html/api/questions/`):
   ```
   e:\projects\playqzv4\api\questions\import.php
   ```

2. **Frontend** (upload to `/public_html/`):
   ```
   e:\projects\playqzv4\client\dist\*
   ```

### Using FileZilla:

1. **Connect to server**:
   - Host: `ftp.aiquiz.vibeai.cv`
   - Username: [your FTP username]
   - Password: [your FTP password]

2. **Upload Backend**:
   - Local: `e:\projects\playqzv4\api\questions\import.php`
   - Remote: `/public_html/api/questions/import.php`
   - **Overwrite** existing file

3. **Upload Frontend**:
   - Local: `e:\projects\playqzv4\client\dist\*`
   - Remote: `/public_html/`
   - **Overwrite** all files

---

## 🧪 Test After Upload

1. **Clear browser cache**: `Ctrl + Shift + Delete`
2. **Visit**: `https://aiquiz.vibeai.cv`
3. **Login as admin**
4. **Go to Questions page**
5. **Click "Import JSON"**
6. **Select a JSON file**
7. **Click "Import Questions"**
8. **Check browser console** (F12) for detailed error

---

## 🔍 What the Fix Does

### Better Error Messages

**Before**: "Failed to import questions"

**After**: Shows specific errors like:
- "Missing fields: question, options"
- "Database error: [specific error]"
- "Authentication failed: [reason]"
- "Invalid JSON: [parse error]"

### Partial Import Support

If some questions import successfully but others fail:
- Shows how many imported
- Shows how many skipped
- Lists specific errors for failed questions

### Transaction Support

- All imports in a single transaction
- If critical error occurs, rolls back all changes
- Prevents partial database corruption

---

## 🐛 Common Errors & Solutions

### Error: "Unauthorized. Admin access required"

**Cause**: Not logged in or session expired

**Fix**:
1. Logout
2. Login again as admin
3. Try import again

### Error: "Database error: ..."

**Cause**: Database connection issue

**Fix**:
1. Check if MySQL is running on server
2. Verify `config.php` has correct credentials
3. Check database exists

### Error: "Invalid JSON: ..."

**Cause**: JSON file is malformed

**Fix**:
1. Validate JSON at https://jsonlint.com
2. Ensure it's an array: `[{...}, {...}]`
3. Check for syntax errors

### Error: "No response from server"

**Cause**: API endpoint not accessible

**Fix**:
1. Check `/api/questions/import.php` exists on server
2. Verify file permissions (644)
3. Check `.htaccess` allows PHP execution

---

## 📊 Expected Behavior After Fix

### Successful Import:
```
✅ Successfully imported 50 questions!
ℹ️ Skipped 5 duplicate questions
```

### Partial Import:
```
✅ Partially imported 45 questions
❌ 5 errors occurred (see details below)
```

### Complete Failure:
```
❌ Database error: Table 'questions' doesn't exist
```

---

## 🔧 Quick Test Locally First

Before deploying to production, test locally:

```powershell
# 1. Ensure dev server is running
cd e:\projects\playqzv4\client
npm run dev

# 2. Visit http://localhost:5173
# 3. Login as admin
# 4. Test import with a small JSON file
```

If it works locally, deploy to production.

---

## 📝 Deployment Checklist

- [ ] Build frontend (`npm run build`)
- [ ] Upload `import.php` to server
- [ ] Upload `dist/*` to server
- [ ] Clear browser cache
- [ ] Test import with small JSON file
- [ ] Check browser console for errors
- [ ] Verify questions appear in database

---

## 🆘 If Still Failing

**Check browser console** (F12) and tell me:

1. **What error message appears?**
2. **What's in the Network tab?** (look for `import.php` request)
3. **What's the response?** (click on request → Response tab)

With this info, I can provide the exact fix! 🎯

---

**Deploy these fixes and try again!** 🚀
