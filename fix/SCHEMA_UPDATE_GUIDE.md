# Schema Update Script Usage Guide

## 📁 Location

**Local Development:**
- `fix/update_schema.php`

**Production Package:**
- `upload_package/fix/update_schema.php`

---

## 🎯 What It Does

This script automatically:

1. ✅ Adds `is_demo` column to questions table (if missing)
2. ✅ Creates performance index on `is_demo` column
3. ✅ Verifies all required tables exist
4. ✅ Validates questions table structure
5. ✅ Checks foreign key constraints
6. ✅ Tests database connection

**Safe to run multiple times** - checks before making changes!

---

## 🚀 How to Use

### Option 1: Via Browser (Easiest)

**Local Development:**
```
http://localhost:8000/fix/update_schema.php
```

**Production:**
```
https://aiquiz.vibeai.cv/fix/update_schema.php
```

You'll see a nice formatted HTML output with:
- ✅ Success messages (green)
- ℹ Info messages (blue)
- ⚠ Warning messages (orange)
- ❌ Error messages (red)

### Option 2: Via Command Line

**Local:**
```bash
php fix/update_schema.php
```

**Production (SSH):**
```bash
cd /var/www/aiquiz.vibeai.cv
php fix/update_schema.php
```

---

## 📊 Expected Output

### First Run (Changes Needed)

```
========================================
  Database Schema Update
========================================

[STEP 1] Checking 'is_demo' column in questions table
ℹ Column 'is_demo' not found. Adding...
✓ Successfully added 'is_demo' column to questions table

[STEP 2] Verifying foreign key constraints
✓ Found 11 foreign key constraints

[STEP 3] Checking database indexes
ℹ Adding index on 'is_demo' column for better query performance
✓ Successfully added index on 'is_demo' column

[STEP 4] Verifying all required tables exist
✓ All 8 required tables exist

[STEP 5] Verifying questions table structure
✓ All 26 required columns exist in questions table

[STEP 6] Testing database connection
✓ Database connection verified. Found X questions

========================================
  Schema Update Complete
========================================

📝 Changes Applied:
  • Added 'is_demo' column to questions table
  • Added index on questions.is_demo

✅ All schema updates completed successfully!
```

### Subsequent Runs (Already Updated)

```
========================================
  Database Schema Update
========================================

[STEP 1] Checking 'is_demo' column in questions table
✓ Column 'is_demo' already exists in questions table

[STEP 2] Verifying foreign key constraints
✓ Found 11 foreign key constraints

[STEP 3] Checking database indexes
✓ Index on 'is_demo' column already exists

[STEP 4] Verifying all required tables exist
✓ All 8 required tables exist

[STEP 5] Verifying questions table structure
✓ All 26 required columns exist in questions table

[STEP 6] Testing database connection
✓ Database connection verified. Found X questions

========================================
  Schema Update Complete
========================================

✓ No schema changes were needed. Database is up to date!

✅ All schema updates completed successfully!
```

---

## 🔄 When to Run

### Scenario 1: Fresh Installation
**❌ NOT NEEDED**

If you're doing a fresh installation and importing `schema.sql`, you don't need to run this script. The schema already includes all changes.

### Scenario 2: Updating Existing Database
**✅ RUN THIS SCRIPT**

If you have an existing production database and are deploying an update, run this script to add the new `is_demo` column.

---

## 📋 Deployment Steps

### For Production Deployment:

1. **Upload Files**
   ```
   Upload upload_package/fix/ folder to your server
   ```

2. **Run the Script**
   ```
   https://aiquiz.vibeai.cv/fix/update_schema.php
   ```

3. **Verify Output**
   - Check that all steps show ✓ (success)
   - Note any warnings or errors
   - Confirm changes were applied

4. **Test Application**
   - Visit your website
   - Login to admin panel
   - Check Questions page
   - Test demo quiz feature

5. **Delete Script** ⚠️ IMPORTANT!
   ```
   After successful update, delete fix/update_schema.php for security
   ```

---

## 🔍 What Gets Changed

### SQL Executed:

```sql
-- Add is_demo column (if not exists)
ALTER TABLE questions 
ADD COLUMN is_demo TINYINT(1) DEFAULT 0 
AFTER ai_prompt;

-- Add performance index (if not exists)
ALTER TABLE questions 
ADD INDEX idx_is_demo (is_demo);
```

### Impact:
- ✅ No data loss
- ✅ No downtime required
- ✅ Existing questions default to `is_demo = 0` (not demo)
- ✅ You can mark questions as demo via admin panel after update

---

## 🆘 Troubleshooting

### Error: "Database connection failed"
**Solution:** Check `api/config.php` database credentials

### Error: "Missing tables"
**Solution:** You need to import `schema.sql` first

### Error: "Permission denied"
**Solution:** 
- Check database user has ALTER privileges
- May need to run with admin MySQL user

### Warning: "Missing columns"
**Solution:** Your schema.sql might be outdated. Use the latest version.

---

## ✅ Verification

After running the script, verify the changes:

```sql
-- Check if column exists
SHOW COLUMNS FROM questions LIKE 'is_demo';

-- Check if index exists
SHOW INDEX FROM questions WHERE Key_name = 'idx_is_demo';

-- View questions with demo flag
SELECT id, question_text, is_demo 
FROM questions 
LIMIT 10;
```

---

## 🔒 Security

**⚠️ DELETE THIS FILE AFTER USE!**

This script provides detailed database information. For security:

1. Run the script once to update your schema
2. Verify everything works
3. **Delete `fix/update_schema.php`** from your server

---

## 📞 Support

If you encounter issues:

1. Check the error message carefully
2. Verify database credentials in `config.php`
3. Ensure MySQL user has proper privileges
4. Try running via command line for more details

---

**Created:** 2025-12-13  
**Version:** 1.0  
**Safe to Run:** Multiple times (idempotent)
