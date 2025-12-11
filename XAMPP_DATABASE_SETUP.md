# XAMPP Database Setup for Testing

## Issue Found

The database `aiquiz` doesn't exist in your XAMPP MySQL installation.

## Quick Fix (2 Steps)

### Step 1: Create the Database

1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **"New"** in the left sidebar
3. Database name: `aiquiz`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

### Step 2: Import the Schema

1. Select the `aiquiz` database you just created
2. Click the **"Import"** tab
3. Click **"Choose File"**
4. Select: `e:\projects\playqzv4\api\schema.sql`
5. Click **"Import"** button at the bottom
6. ✅ Done! The database structure is now ready

## Alternative: Quick SQL Script

If you prefer, you can run this SQL directly in phpMyAdmin:

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS `aiquiz` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `aiquiz`;

-- Then import the schema.sql file using the Import tab
```

## After Database Setup

Once the database is created and schema imported:

1. **Refresh the login page** in your browser
2. The network error should be gone
3. You can now login with your credentials

## Default Admin User

After importing the schema, you may need to create an admin user. Check if there's a default user in the schema, or create one:

```sql
-- Check if there are any users
SELECT * FROM users;

-- If no users exist, you'll need to create one
-- (The schema might have seed data already)
```

## Test the API Connection

After setup, test if the API works:

```powershell
# Test API connection
Invoke-WebRequest -Uri "http://projects/playqzv4/api/auth/login.php" `
  -Method POST `
  -ContentType "application/json" `
  -Body '{"email":"admin@example.com","password":"admin123"}'
```

## Current Configuration

I've updated `api/config.php` to use XAMPP defaults:
- **Host**: localhost
- **Database**: aiquiz
- **User**: root
- **Password**: (empty)
- **CORS**: Enabled for localhost

## Next Steps After Database Setup

1. ✅ Create database `aiquiz`
2. ✅ Import `api/schema.sql`
3. ✅ Refresh login page
4. ✅ Login as admin
5. ✅ Import personality questions
6. ✅ Test the quiz!

---

**Need Help?** 
- Check if XAMPP MySQL is running (green in XAMPP Control Panel)
- Make sure Apache is also running
- Verify you can access phpMyAdmin at `http://localhost/phpmyadmin`
