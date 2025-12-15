# Database Schema Fix - Foreign Key Constraint Issue

## Problem Identified

The production database has a foreign key constraint on `questions.created_by` that references `profiles.id`, but imported questions may have invalid or NULL values.

## Root Cause

**From production schema** (`rcdzrtua_aiquiz.sql` line 171):
```sql
`created_by` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
```

**With constraint** (likely defined separately):
```sql
CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `profiles` (`id`) ON DELETE CASCADE
```

This causes import to fail with:
```
SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: 
a foreign key constraint fails (`rcdzrtua_aiquiz`.`questions`, 
CONSTRAINT `questions_ibfk_2` FOREIGN KEY (created_by) REFERENCES `profiles` (`id`) ON DELETE CASCADE)
```

## Immediate Fix - Run in phpMyAdmin SQL Tab

### Option 1: Make created_by Nullable (Recommended)

```sql
-- Step 1: Drop the foreign key constraint
ALTER TABLE questions DROP FOREIGN KEY questions_ibfk_2;

-- Step 2: Make created_by nullable
ALTER TABLE questions MODIFY COLUMN created_by char(36) COLLATE utf8mb4_unicode_ci NULL;

-- Step 3 (Optional): Re-add constraint allowing NULL
ALTER TABLE questions 
ADD CONSTRAINT questions_ibfk_2 
FOREIGN KEY (created_by) REFERENCES profiles(id) 
ON DELETE SET NULL;
```

### Option 2: Just Drop the Constraint (Quick Fix)

```sql
ALTER TABLE questions DROP FOREIGN KEY questions_ibfk_2;
```

## Verification

After running the fix, verify:

```sql
-- Check if constraint is removed
SHOW CREATE TABLE questions;

-- Check column definition
DESC questions;
```

## Long-term Solution

The import scripts (`api/questions/import.php` and `api/bundle/import.php`) already have logic to:

1. Disable foreign key checks during import:
   ```php
   $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
   ```

2. Set created_by to current admin:
   ```php
   $createdBy = $session['profile_id'] ?? $session['user_id'];
   ```

But the constraint must be either:
- **Dropped** (simple, allows any value)
- **Made nullable** (better, allows NULL but validates non-NULL)
- **Set to ON DELETE SET NULL** (best practice)

## Updated Schema

The new `schema.sql` now defines:
```sql
`created_by` char(36) COLLATE utf8mb4_unicode_ci NULL,
```

Without a strict foreign key constraint, or with `ON DELETE SET NULL` behavior.

## After Fix - Import Will Work

Once the constraint is fixed, all import methods will work:
- ✅ JSON import via admin panel
- ✅ Bundle import (ZIP files)
- ✅ Manual SQL imports

## Prevention for Future

When setting up a new database:
1. Use the updated `Database/schema.sql`
2. Don't add `created_by` foreign key with `ON DELETE CASCADE`
3. If adding FK, use `ON DELETE SET NULL` instead
