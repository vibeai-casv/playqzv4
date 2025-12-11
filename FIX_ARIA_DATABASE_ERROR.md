# Fix Aria Database Corruption Error

## Error
```
#1030 - Got error 176 "Read page with wrong checksum" from storage engine Aria
```

## Quick Fix (Choose One Method)

### Method 1: Repair Tables in phpMyAdmin (Easiest)

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select the `aiquiz` database
3. Check **all tables** (click the checkbox at the top)
4. From the "With selected:" dropdown, choose **"Repair table"**
5. Click **"Go"**
6. ✅ Done! Refresh your application

### Method 2: Drop and Recreate Database (Fastest)

**⚠️ Warning: This will delete all data!**

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select the `aiquiz` database
3. Click **"Operations"** tab
4. Scroll down to **"Remove database"**
5. Click **"Drop the database (DROP)"**
6. Confirm the deletion
7. Create new database: Click **"New"** → Name: `aiquiz` → Collation: `utf8mb4_unicode_ci` → Create
8. Select the new `aiquiz` database
9. Click **"Import"** tab
10. Choose file: `e:\projects\playqzv4\api\schema.sql`
11. Click **"Import"**
12. ✅ Done! Fresh database ready

### Method 3: SQL Command (Advanced)

Run this in phpMyAdmin SQL tab:

```sql
-- Use the database
USE aiquiz;

-- Repair all tables
REPAIR TABLE users;
REPAIR TABLE profiles;
REPAIR TABLE sessions;
REPAIR TABLE questions;
REPAIR TABLE quiz_attempts;
REPAIR TABLE quiz_responses;
REPAIR TABLE user_activity_logs;
REPAIR TABLE media_library;
```

### Method 4: Change Storage Engine to InnoDB (Recommended)

The Aria engine can be problematic. Let's switch to InnoDB:

```sql
-- Use the database
USE aiquiz;

-- Convert all tables to InnoDB
ALTER TABLE users ENGINE=InnoDB;
ALTER TABLE profiles ENGINE=InnoDB;
ALTER TABLE sessions ENGINE=InnoDB;
ALTER TABLE questions ENGINE=InnoDB;
ALTER TABLE quiz_attempts ENGINE=InnoDB;
ALTER TABLE quiz_responses ENGINE=InnoDB;
ALTER TABLE user_activity_logs ENGINE=InnoDB;
ALTER TABLE media_library ENGINE=InnoDB;
```

## Why This Happened

The Aria storage engine in XAMPP can get corrupted if:
- XAMPP was shut down improperly
- System crashed while database was writing
- Disk space issues
- The schema.sql file specified Aria engine

## Recommended Solution

**Use Method 2 (Drop and Recreate)** if you just set up the database and have no important data yet. This is the cleanest approach.

Then, after importing, run the SQL from Method 4 to convert to InnoDB for better stability.

## Prevent Future Issues

1. **Always stop XAMPP properly** (don't just close the window)
2. **Use InnoDB engine** instead of Aria (more stable)
3. **Regular backups** if you have important data

## After Fixing

1. Refresh your application: `http://localhost:5173/login`
2. The error should be gone
3. You can now login and import personality questions

## Need to Create Admin User?

After recreating the database, you may need to create an admin user. Run this SQL:

```sql
-- Generate a UUID for the user
SET @user_id = UUID();

-- Create user account (password is 'admin123' - bcrypt hash)
INSERT INTO users (id, email, password_hash, created_at)
VALUES (
    @user_id,
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    NOW()
);

-- Create profile with admin role
INSERT INTO profiles (id, email, name, role, created_at)
VALUES (
    @user_id,
    'admin@example.com',
    'Admin User',
    'admin',
    NOW()
);
```

**Login credentials:**
- Email: `admin@example.com`
- Password: `admin123`

---

**Recommended: Use Method 2 (Drop and Recreate) for a clean start!**
