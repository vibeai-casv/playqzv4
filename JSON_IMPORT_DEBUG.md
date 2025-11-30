# 🔍 JSON Question Import - Troubleshooting Guide

## Issue: JSON Import Not Working Properly

Let me help you debug and fix the JSON question import feature.

---

## 🧪 Quick Test

### Step 1: Test Database Connection

Visit: `http://localhost/api/questions/test-import.php`

This will show:
- Database connection status
- Test question format
- Configuration details

---

## 🔧 Common Issues & Fixes

### Issue 1: JSON Format Mismatch

**Problem**: JSON files have `"id"` field that API doesn't expect

**Your JSON Format** (from qbank/question2.json):
```json
[
  {
    "id": 1,  ← This field is ignored by the API
    "type": "text_mcq",
    "category": "Fundamentals",
    "difficulty": "easy",
    "question": "What is AI?",
    "options": ["A", "B", "C", "D"],
    "correct_answer": "A"
  }
]
```

**Solution**: The API already ignores the `id` field. It generates its own UUID.

---

### Issue 2: Wrong API Endpoint

**Check**: Make sure you're calling the correct endpoint

```typescript
// In JSONImporter.tsx (line 59)
const response = await axios.post(
    `${import.meta.env.VITE_API_URL}/questions/import.php`,
    { questions, skipDuplicates },
    { withCredentials: true }
);
```

**Verify**: `VITE_API_URL` is set correctly in `.env`

---

### Issue 3: CORS Issues

**Symptoms**:
- Network error in browser console
- 403 Forbidden
- CORS policy error

**Fix**: Check `api/config.php`:
```php
define('ALLOWED_ORIGIN', 'http://localhost:5173');
```

---

### Issue 4: Authentication Required

**Problem**: Import requires admin login

**Solution**:
1. Login as admin first
2. Then try importing

---

## 📝 Step-by-Step Debug Process

### 1. Check Browser Console

Open DevTools (F12) → Console tab

Look for:
- ✅ Network requests to `/api/questions/import.php`
- ❌ Error messages
- ⚠️ CORS warnings

### 2. Check Network Tab

Open DevTools (F12) → Network tab

1. Click "Import Questions"
2. Look for `import.php` request
3. Check:
   - **Status**: Should be 200
   - **Response**: Check for errors
   - **Request Payload**: Verify JSON structure

### 3. Test with Small JSON

Create `test-import.json`:
```json
[
  {
    "type": "text_mcq",
    "category": "Test",
    "difficulty": "easy",
    "question": "Test question?",
    "options": ["A", "B", "C", "D"],
    "correct_answer": "A"
  }
]
```

Try importing this first.

---

## 🐛 Common Error Messages

### "Invalid input. Expected 'questions' array"

**Cause**: JSON structure is wrong

**Fix**: Ensure JSON is an array of questions, not an object

### "Unauthorized"

**Cause**: Not logged in as admin

**Fix**: Login as admin first

### "Failed to import questions"

**Cause**: Various - check browser console for details

**Fix**: Look at the actual error message in console

---

## 🔍 Manual Test via cURL

Test the API directly:

```bash
curl -X POST http://localhost/api/questions/import.php \
  -H "Content-Type: application/json" \
  -d '{
    "questions": [
      {
        "type": "text_mcq",
        "category": "Test",
        "difficulty": "easy",
        "question": "Test?",
        "options": ["A", "B", "C", "D"],
        "correct_answer": "A"
      }
    ],
    "skipDuplicates": true
  }'
```

---

## ✅ Verification Steps

After import, verify:

```sql
-- Check if questions were imported
SELECT COUNT(*) FROM questions;

-- View recent imports
SELECT * FROM questions ORDER BY created_at DESC LIMIT 10;

-- Check for duplicates
SELECT question, COUNT(*) as count 
FROM questions 
GROUP BY question 
HAVING count > 1;
```

---

## 🛠️ Quick Fixes

### Fix 1: Update .env File

```env
VITE_API_URL=http://localhost/api
```

### Fix 2: Clear Browser Cache

```
Ctrl + Shift + Delete
→ Clear cached images and files
```

### Fix 3: Restart Dev Server

```powershell
# Stop current server (Ctrl+C)
cd e:\projects\playqzv4\client
npm run dev
```

---

## 📊 Expected Behavior

### Successful Import:

1. **Select JSON file** → File name appears
2. **Click "Import Questions"** → Shows "Importing..."
3. **Success toast** → "Successfully imported X questions!"
4. **Result panel** → Shows:
   - ✅ Imported: X questions
   - ⚠️ Skipped: Y duplicates
   - ❌ Errors: Z (if any)

---

## 🎯 What to Check Right Now

1. **Is dev server running?**
   ```
   http://localhost:5173
   ```

2. **Is MySQL running?**
   ```powershell
   Get-Service -Name "*mysql*"
   ```

3. **Can you access the API?**
   ```
   http://localhost/api/questions/test-import.php
   ```

4. **Are you logged in as admin?**
   - Check browser: Should see admin dashboard

---

## 📞 Debugging Commands

### Check API Response:

```javascript
// In browser console
fetch('http://localhost/api/questions/import.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  credentials: 'include',
  body: JSON.stringify({
    questions: [{
      type: 'text_mcq',
      category: 'Test',
      difficulty: 'easy',
      question: 'Test?',
      options: ['A', 'B', 'C', 'D'],
      correct_answer: 'A'
    }],
    skipDuplicates: true
  })
})
.then(r => r.json())
.then(console.log)
.catch(console.error);
```

---

## 🎬 Next Steps

1. **Open browser DevTools** (F12)
2. **Go to Admin → Questions**
3. **Click "Import JSON"**
4. **Select a JSON file**
5. **Watch the Console tab** for errors
6. **Check Network tab** for API response

---

**Tell me what error you see and I'll help you fix it!** 🚀

Common things to check:
- Browser console errors
- Network tab response
- Are you logged in as admin?
- Is the dev server running?
