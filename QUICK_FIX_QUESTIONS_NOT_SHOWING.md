# 🚨 QUICK FIX - Questions Not Showing

## The Problem
You imported questions but don't see them at:
https://aiquiz.vibeai.cv/aiq3/admin/questions

## ⚡ Most Likely Reason

**YOU IMPORTED TO LOCAL DATABASE, NOT PRODUCTION**

If you used:
- ❌ `http://localhost:3000` → Imported to **local DB**
- ❌ `http://localhost:8000` → Imported to **local DB**  
- ✅ `https://aiquiz.vibeai.cv/aiq3/` → Imported to **production DB**

---

## ✅ SOLUTION (2 Minutes)

### **Step 1: Get the JSON Ready**
```
File: e:\projects\playqzv4\qbank\questions\il\dsset1cp-25_fixed.json
Action: Open and copy all contents (Ctrl+A, Ctrl+C)
```

### **Step 2: Import to PRODUCTION**
```
1. Open: https://aiquiz.vibeai.cv/aiq3/
2. Login (vibeaicasv@gmail.com)
3. Click: Admin → Import/Export
4. Click: "Import JSON" tab
5. Paste JSON (Ctrl+V)
6. Click: "Import Questions"
7. Wait for success: "Successfully imported 25 questions"
```

### **Step 3: View Questions**
```
1. Click: Admin → Questions
2. Clear filters:
   - Status dropdown: Select "All" (not just Active)
   - Type: Select "image_identify_logo"
3. Should see 25 new questions!
4. Status will be: Inactive (draft)
```

### **Step 4: Activate After Upload**
```
For each question:
1. Click Edit
2. Upload company logo
3. Save
4. Click "Activate"
```

---

## 🔍 How to Check Where You Imported

**Check your browser history:**
- If you imported via `localhost` → Questions are LOCAL only
- If you imported via `aiquiz.vibeai.cv` → Questions are PRODUCTION

**To check local questions:**
- Go to: `http://localhost:3000/admin/questions`
- If you see 25 questions there → They're only local!

**To check production:**
- Go to: `https://aiquiz.vibeai.cv/aiq3/admin/questions`  
- Clear ALL filters (Status: All)
- If no questions → Not imported yet

---

## ⚠️ Common Mistakes

**1. Filters Hiding Questions**
- Default filter shows only "Active" questions
- Imported questions are "Inactive" by default
- **Fix:** Set Status filter to "All"

**2. Wrong Database**
- Imported locally but checking production
- **Fix:** Import again via production URL

**3. Questions Were Duplicates**
- Import message says "0 imported, 25 skipped"
- Means questions already exist
- **Fix:** Check with different filters

---

## 📊 Quick Status Check

**Run this on production site (F12 Console):**
```javascript
fetch('/aiq3/api/questions/list.php?type=image_identify_logo&limit=50')
  .then(r => r.json())
  .then(data => console.log('Logo questions:', data.total, 'found'));
```

**Expected:**
- If you see `Logo questions: 25 found` → Questions ARE there, check filters
- If you see `Logo questions: 0 found` → Need to import to production

---

## 🎯 The Fastest Fix

**Don't debug - just import again to production:**

1. ✅ Go to: **https://aiquiz.vibeai.cv/aiq3/** (not localhost!)
2. ✅ Login
3. ✅ Admin → Import/Export → Import JSON
4. ✅ Paste JSON from `dsset1cp-25_fixed.json`
5. ✅ Click Import
6. ✅ Check Admin → Questions (Status: All)

**Done in 2 minutes!**

---

**Still stuck?** See `QUESTIONS_NOT_SHOWING_FIX.md` for detailed troubleshooting.
