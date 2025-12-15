# Personality Questions Import Diagnostics

## Issue
You imported 10 personality questions but `SELECT DISTINCT question_type FROM questions;` returns 0 rows.

---

## Diagnostic Queries - Run These in phpMyAdmin

### 1. Check if questions table exists and has ANY data
```sql
SELECT COUNT(*) as total_questions FROM questions;
```
**Expected:** Should show total number of questions
**If 0:** Table is empty - import failed

---

### 2. Check table structure
```sql
DESCRIBE questions;
```
**Expected:** Should show all columns including `question_type`
**If error:** Table doesn't exist

---

### 3. Check for ANY rows with personality type
```sql
SELECT * FROM questions 
WHERE question_type LIKE '%person%' 
   OR question_type LIKE '%personality%'
LIMIT 10;
```

---

### 4. Check media_library for imported images
```sql
SELECT COUNT(*) as total_media FROM media_library;
```
**If 0:** Media didn't import either

---

### 5. Check for ANY media related to personalities
```sql
SELECT * FROM media_library 
WHERE type LIKE '%person%' 
   OR filename LIKE '%person%'
LIMIT 10;
```

---

### 6. Check recent questions (if any exist)
```sql
SELECT id, question_text, question_type, created_at 
FROM questions 
ORDER BY created_at DESC 
LIMIT 10;
```

---

## Common Causes & Solutions

### Cause 1: Import Actually Failed
**Check import response:** Did you see a success message?
```json
{
  "success": true,
  "stats": {
    "questions_imported": 10,
    ...
  }
}
```

**If you saw errors instead:**
- Foreign key constraint failures
- Permission issues
- Invalid manifest format

---

### Cause 2: Wrong Database Selected
**Check which database you're querying:**
```sql
SELECT DATABASE();
```
**Expected:** Should show `rcdzrtua_aiquiz` or your database name

**If wrong database:**
- Select the correct database from phpMyAdmin sidebar
- Your database should be: `rcdzrtua_aiquiz` (based on your error messages)

---

### Cause 3: Questions Imported to Different Table
**Check all tables:**
```sql
SHOW TABLES;
```

Look for tables like:
- `questions` (correct)
- `question` (wrong - singular)
- `aiq3_questions` (wrong - prefixed)

---

### Cause 4: Foreign Key Constraint Blocked Import

Check if the import log has errors. On your server, look for:
```
/public_html/aiq3/import_errors.log
```

Or check PHP error logs in your hosting control panel.

---

## Quick Fix Options

### Option A: Re-import with Diagnostics

1. **Before re-importing**, run this to check starting state:
```sql
SELECT COUNT(*) FROM questions;
SELECT COUNT(*) FROM media_library;
```

2. **Import your bundle again** and watch for errors

3. **After import**, run the same queries to see if count changed

---

### Option B: Manual Insert Test

Try manually inserting ONE test question:

```sql
INSERT INTO questions (
    question_text, question_type, options, correct_answer,
    difficulty, category, points, is_active, created_at, updated_at
) VALUES (
    'Test Question',
    'image_identify_person',
    '["Option A","Option B","Option C","Option D"]',
    'Option A',
    'medium',
    'AI Personalities',
    10,
    1,
    NOW(),
    NOW()
);
```

**If this works:** Import process has an issue
**If this fails:** Check the error message - likely foreign key or schema issue

---

### Option C: Drop Foreign Key Constraint

If you're getting foreign key errors, run:

```sql
-- Drop the created_by foreign key
ALTER TABLE questions DROP FOREIGN KEY questions_ibfk_2;

-- Make created_by nullable
ALTER TABLE questions MODIFY COLUMN created_by INT NULL;
```

Then try importing again.

---

## Verification After Fix

Once you've resolved the issue, verify with:

```sql
-- Should show your question types
SELECT DISTINCT question_type, COUNT(*) as count
FROM questions
GROUP BY question_type;

-- Should show something like:
-- image_identify_person | 10
```

---

## Next Steps

1. ✅ Run **Diagnostic Query #1** first to check if table has ANY data
2. ✅ Run **Diagnostic Query #2** to verify database selection
3. ✅ Check import error logs if available
4. ✅ Report what you find and I'll provide specific fix

---

## Expected Results for Working Import

After successful import of 10 personality questions:

```sql
-- Diagnostic 1: Total questions
SELECT COUNT(*) FROM questions;
-- Result: 10 (or more if you had existing questions)

-- Diagnostic 2: Question types
SELECT DISTINCT question_type FROM questions;
-- Result: 
-- image_identify_person

-- Diagnostic 3: Sample question
SELECT question_text, question_type, category 
FROM questions 
LIMIT 1;
-- Result:
-- "Who is this AI pioneer?" | image_identify_person | AI Personalities
```

---

## Report Back

Please run these queries and tell me:
1. What does `SELECT COUNT(*) FROM questions;` return?
2. What does `SELECT DATABASE();` return?
3. Did you see any error message during import?
4. What was the exact response after clicking "Import"?
