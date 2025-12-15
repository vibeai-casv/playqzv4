# 🔍 Questions Not Showing - Troubleshooting Guide

## Issue
You imported questions successfully, but they're not showing at:
`https://aiquiz.vibeai.cv/aiq3/admin/questions`

## 🎯 Most Likely Cause

**You imported to LOCAL database, but checking PRODUCTION site**

- Import happened on: **Local development (XAMPP/localhost)**
- Checking questions on: **Production (aiquiz.vibeai.cv/aiq3)**

**These are two separate databases!**

---

## ✅ Solution Options

### **Option 1: Check Local Questions (Recommended to Verify)**

**If you imported locally, check local:**
```
http://localhost:3000/admin/questions
or
http://localhost:8000/admin/questions
```

**Expected:** Questions should show here if import was successful

---

### **Option 2: Import to Production Database**

**Steps to import questions to production:**

**Method A: Via Web Interface**
```
1. Go to: https://aiquiz.vibeai.cv/aiq3/
2. Login as admin
3. Go to Admin → Import/Export
4. Select "Import JSON" tab
5. Paste the JSON from: qbank/questions/il/dsset1cp-25_fixed.json
6. Click Import
7. Check Admin → Questions
```

**Method B: Via SSH (Advanced)**
```bash
# SSH to production server
ssh user@aiquiz.vibeai.cv

# Copy JSON file to server (or paste content to a file)
nano /tmp/questions.json
# Paste content, save

# Use phpMyAdmin or import via API
# Or use the web interface (Method A)
```

---

### **Option 3: Export from Local, Import to Production**

**Step 1: Check Local Database**
```sql
-- On local MySQL (XAMPP)
USE aiqz;
SELECT COUNT(*) FROM questions;
SELECT id, question_text, question_type, created_at 
FROM questions 
ORDER BY created_at DESC 
LIMIT 10;
```

**Step 2: If questions are there, export them**
```
1. Go to local site: http://localhost:3000
2. Admin → Import/Export
3. Select questions (filter by type: image_identify_logo)
4. Click "Export JSON"
5. Save the JSON
```

**Step 3: Import to production**
```
1. Go to: https://aiquiz.vibeai.cv/aiq3/
2. Admin → Import/Export → Import JSON
3. Paste exported JSON
4. Import
```

---

## 🔍 Quick Diagnostic Steps

### **Step 1: Verify Where Import Happened**

**Check your import location:**
- Did you import via: `http://localhost:3000` or similar?
  - ✅ Import went to **local DB**
- Did you import via: `https://aiquiz.vibeai.cv/aiq3/`?
  - ✅ Import went to **production DB**

### **Step 2: Check Local Database**

**Via phpMyAdmin (XAMPP):**
```
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select database: aiqz
3. Click on table: questions
4. Check if new questions are there
5. Look at created_at dates (should be recent)
```

**Via SQL:**
```sql
SELECT 
    COUNT(*) as total_questions,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as imported_today
FROM questions;
```

### **Step 3: Check Production Database**

**Option A: Via Web Interface**
```
1. Go to: https://aiquiz.vibeai.cv/aiq3/admin/diagnostics
2. Check database status
3. See question counts
```

**Option B: Via API**
```bash
# Check total questions on production
curl https://aiquiz.vibeai.cv/aiq3/api/questions/list.php?limit=1

# Count specific type
curl "https://aiquiz.vibeai.cv/aiq3/api/questions/list.php?type=image_identify_logo&limit=1"
```

**Option C: Via SSH**
```bash
ssh user@aiquiz.vibeai.cv

mysql -u vibeaicb_aiqzuser -p vibeaicb_aiqz -e "SELECT COUNT(*) FROM questions;"
mysql -u vibeaicb_aiqzuser -p vibeaicb_aiqz -e "SELECT COUNT(*) FROM questions WHERE question_type='image_identify_logo';"
```

---

## 🎯 Common Scenarios

### **Scenario 1: "Questions show locally but not on production"**
**Cause:** Imported to local DB
**Solution:** Use Option 2 above - Import to production via web interface

### **Scenario 2: "Import said successful but no questions anywhere"**
**Causes:**
- Import validation failed (should show errors)
- All questions were duplicates (check import message)
- Database connection issue

**Check:**
```
1. Look at import success message
2. Did it say "0 questions imported, X skipped"?
3. If skipped = duplicates, questions already exist
```

### **Scenario 3: "Questions imported but filtered out"**
**Cause:** Frontend filters hiding them
**Solution:** Clear all filters on questions page
```
1. Go to Questions page
2. Reset filters:
   - Status: Show All (not just Active)
   - Type: All Types
   - Category: All Categories
   - Search: Clear
```

---

## 📋 Verification Checklist

**After importing to production, verify:**

- [ ] Go to: https://aiquiz.vibeai.cv/aiq3/admin/questions
- [ ] Clear all filters (Status: All, Type: All)
- [ ] Sort by: Created At (Descending)
- [ ] Should see 25 new questions at top
- [ ] Type should be: image_identify_logo
- [ ] Status should be: Inactive (Draft)
- [ ] Created date should be today

---

## 🚀 Quick Fix - Import to Production Now

**Fastest way to get questions on production:**

**Step 1: Copy this JSON**
```
Open: e:\projects\playqzv4\qbank\questions\il\dsset1cp-25_fixed.json
Select All (Ctrl+A)
Copy (Ctrl+C)
```

**Step 2: Import to Production**
```
1. Open: https://aiquiz.vibeai.cv/aiq3/
2. Login
3. Click: Admin → Import/Export
4. Tab: Import JSON
5. Paste JSON (Ctrl+V)
6. Click: Import Questions
7. Wait for success message
```

**Step 3: Verify**
```
1. Click: Admin → Questions
2. Filter: Type = image_identify_logo
3. Should see 25 questions
4. Status will be: Inactive
```

**Step 4: Upload Logos & Activate**
```
1. For each question, click Edit
2. Upload corresponding company logo
3. Save
4. Click "Activate" button
5. Repeat for all 25
```

---

## 💡 Pro Tip: Checking Which Database

**To confirm where you are:**

**Local indicators:**
- URL: `localhost`, `127.0.0.1`, or `192.168.x.x`
- Port: `:3000`, `:8000`, `:5173`
- Database: Typically `aiqz` on XAMPP

**Production indicators:**
- URL: `aiquiz.vibeai.cv`
- Path: `/aiq3/`
- No port number (uses standard :80/:443)
- Database: `vibeaicb_aiqz` (or similar with hosting prefix)

---

## 🔧 Still Not Showing?

**If questions still don't appear:**

**1. Browser Cache:**
```
Hard refresh: Ctrl+Shift+R (Chrome/Firefox)
Or clear browser cache
```

**2. Check Console Errors:**
```
1. Open browser DevTools (F12)
2. Go to Console tab
3. Look for red errors
4. Check Network tab for failed API calls
```

**3. Check API Response:**
```
1. F12 → Network tab
2. Refresh questions page
3. Look for: /api/questions/list.php
4. Click on it
5. Check Response tab
6. Should see JSON with questions array
```

**4. Verify Import Response:**
```
When you import, check the success message:
✅ "Successfully imported 25 questions"
❌ "0 questions imported, 25 skipped (duplicates)"

If skipped = they already exist!
```

---

## 📞 Quick Decision Tree

```
Questions not showing?
    |
    ├─ Where did you import?
    │   ├─ Localhost → Check local questions page
    │   └─ Production → Continue below
    |
    ├─ Import successful?
    │   ├─ Yes → Clear filters, check Status=All
    │   └─ No → Check error message, retry
    |
    ├─ Can you see in database?
    │   ├─ Yes → Frontend filtering issue
    │   └─ No → Import didn't work or wrong DB
    |
    └─ Still stuck?
        └─ Import again to production using web interface
```

---

**Most common fix:** Import to production via web interface at `https://aiquiz.vibeai.cv/aiq3/admin/import-export`, then check with all filters cleared.
