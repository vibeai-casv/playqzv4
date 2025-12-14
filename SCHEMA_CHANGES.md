# Database Schema Changes Summary

**Last Updated:** 2025-12-13  
**Status:** Schema is current and includes latest changes

---

## ✅ Current Schema Status

The `schema.sql` file **IS UP TO DATE** and includes all necessary changes for production deployment.

---

## 📝 Schema Change: `is_demo` Column

### What Changed?

**Added to `questions` table:**
- **Column**: `is_demo`
- **Type**: `TINYINT(1)`
- **Default**: `0` (false)
- **Line**: 90 in schema.sql

### Purpose

This column allows marking questions as "demo questions" for the demo quiz feature. Demo questions are:
- Used in the public demo quiz (no login required)
- Prioritized when users take the demo quiz
- Easily toggled on/off from the admin panel

### SQL Definition

```sql
CREATE TABLE IF NOT EXISTS `questions` (
    -- ... other columns ...
    `is_demo` TINYINT(1) DEFAULT 0,  -- ← This is the new column
    -- ... other columns ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Migration File

A migration script exists to add this column to existing databases:
- **Location**: `api/migrations/001_add_is_demo.php`
- **Function**: Safely adds the column if it doesn't exist

---

## 🔄 For Existing Production Databases

If you're **updating** an existing production database (not a fresh install), you have **two options**:

### Option 1: Run Migration Script (Recommended)

Upload and run the migration:

```bash
# Via browser
https://aiquiz.vibeai.cv/api/migrations/001_add_is_demo.php

# Or via SSH
php /var/www/aiquiz.vibeai.cv/api/migrations/001_add_is_demo.php
```

**Output:**
```
Checking for is_demo column...
Adding is_demo column to questions table...
Column is_demo added successfully.
```

### Option 2: Run SQL Manually

Login to phpMyAdmin and run:

```sql
ALTER TABLE questions ADD COLUMN is_demo TINYINT(1) DEFAULT 0;
```

---

## 🆕 For Fresh Installations

**No action needed!** The `schema.sql` already includes the `is_demo` column (line 90).

When you import `schema.sql`, it will create all tables with all columns, including `is_demo`.

---

## 📊 Complete Questions Table Schema

Here's the complete structure of the `questions` table with the new column highlighted:

```sql
CREATE TABLE IF NOT EXISTS `questions` (
    `id` CHAR(36) PRIMARY KEY,
    `question_text` TEXT NOT NULL,
    `question_type` ENUM('text_mcq', 'image_identify_logo', 'image_identify_person', 'true_false', 'short_answer') NOT NULL,
    `options` JSON,
    `correct_answer` TEXT NOT NULL,
    `image_url` TEXT,
    `media_id` CHAR(36) NULL,
    `explanation` TEXT,
    `hint` TEXT,
    `difficulty` ENUM('easy', 'medium', 'hard') NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `subcategory` VARCHAR(100),
    `tags` JSON,
    `points` INT DEFAULT 10 NOT NULL,
    `time_limit_seconds` INT DEFAULT 60,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_verified` TINYINT(1) DEFAULT 0,
    `usage_count` INT DEFAULT 0,
    `correct_count` INT DEFAULT 0,
    `ai_generated` TINYINT(1) DEFAULT 0,
    `ai_prompt` TEXT,
    `is_demo` TINYINT(1) DEFAULT 0,  -- ← NEW! Demo quiz flag
    `created_by` CHAR(36) NOT NULL,
    `verified_by` CHAR(36) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`media_id`) REFERENCES `media_library`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`) REFERENCES `profiles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`verified_by`) REFERENCES `profiles`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🎯 Feature Impact

### What Uses This Column?

1. **Demo Quiz**
   - Public demo quiz prioritizes questions where `is_demo = 1`
   - Falls back to random questions if not enough demo questions exist

2. **Admin Panel**
   - Questions page has a toggle to mark/unmark questions as demo
   - Filter to view only demo questions
   - Bulk actions to set/unset demo status

3. **API Endpoints**
   - `GET /quiz/demo.php` - Uses demo questions
   - `GET /questions/list.php` - Can filter by `is_demo`
   - `PATCH /admin/questions/update.php` - Can update `is_demo` status

---

## ✅ Deployment Instructions

### For Fresh Installation (New Production Database)

1. **Import schema.sql** - No additional steps needed
   ```bash
   mysql -u aiqz_user -p aiqz_production < schema.sql
   ```

### For Update (Existing Production Database)

1. **Option A: Run migration script**
   ```bash
   php api/migrations/001_add_is_demo.php
   ```

2. **Option B: Run SQL manually**
   ```sql
   ALTER TABLE questions ADD COLUMN is_demo TINYINT(1) DEFAULT 0;
   ```

3. **Verify**
   ```sql
   SHOW COLUMNS FROM questions LIKE 'is_demo';
   ```

   Expected output:
   ```
   Field    Type        Null  Key  Default  Extra
   is_demo  tinyint(1)  YES        0
   ```

---

## 🔍 Verification Queries

### Check if Column Exists

```sql
SHOW COLUMNS FROM questions LIKE 'is_demo';
```

### Count Demo Questions

```sql
SELECT COUNT(*) as demo_questions FROM questions WHERE is_demo = 1;
```

### List All Demo Questions

```sql
SELECT id, question_text, difficulty, category, is_demo 
FROM questions 
WHERE is_demo = 1;
```

---

## 📦 What's Included in Upload Package

The `upload_package` includes:

✅ **schema.sql** - Complete schema with `is_demo` column (line 90)  
✅ **api/migrations/001_add_is_demo.php** - Migration script for existing DBs  
✅ All API endpoints that use the `is_demo` column  
✅ Admin UI with demo toggle functionality

---

## 🎉 Summary

- ✅ Schema file is **up to date**
- ✅ `is_demo` column **already included** in schema.sql (line 90)
- ✅ Migration script provided for existing databases
- ✅ No manual SQL changes needed for fresh installations
- ✅ Feature is **production-ready**

---

## 🚀 Next Steps for Deployment

1. **Fresh Install**: Just import `schema.sql` - you're done!
2. **Update**: Run the migration script after deploying
3. **Verify**: Check that demo questions feature works in admin panel

No other schema changes exist. You're ready to deploy! 🎉
