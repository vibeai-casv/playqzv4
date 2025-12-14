# Database Connection Fix Guide

## Problem
Getting error: `Access denied for user 'aiqz_user'@'localhost' (using password: YES)`

## Root Cause
The production `config.php` file still has the placeholder password `YOUR_PASSWORD_HERE` instead of the actual database password.

---

## Solution Steps

### Step 1: Get Your Database Credentials

You need to get the correct database credentials from your hosting provider:

1. **Log in to your hosting control panel** (cPanel, Plesk, or similar)
2. **Find the MySQL/Database section**
3. **Note down:**
   - Database Name (e.g., `aiqz_production` or `your_user_aiqz`)
   - Database Username (e.g., `aiqz_user` or `your_user_aiqz`)
   - Database Password (if you don't know it, you may need to reset it)
   - Database Host (usually `localhost`, but could be different)

### Step 2: Upload Diagnostic Script

Upload `fix/check-db-connection.php` to your server:

```
Local:  fix/check-db-connection.php
Remote: /aiq2/fix/check-db-connection.php
```

Then visit:
```
https://aiquiz.vibeai.cv/aiq2/fix/check-db-connection.php
```

This will tell you exactly what's wrong.

### Step 3: Update Production Config File

**Option A: Via FTP/SFTP (Recommended)**

1. Connect to your server via FTP/SFTP
2. Navigate to `/aiq2/api/config.php`
3. Download the file as backup
4. Edit line 8:
   ```php
   define('DB_PASS', 'YOUR_PASSWORD_HERE'); // CHANGE THIS!
   ```
   Replace with:
   ```php
   define('DB_PASS', 'your_actual_password'); // Actual password
   ```
5. Also verify/update:
   ```php
   define('DB_HOST', 'localhost');           // Usually localhost
   define('DB_NAME', 'aiqz_production');     // Your actual DB name
   define('DB_USER', 'aiqz_user');          // Your actual DB user
   ```
6. Upload the corrected file back to the server
7. Clear browser cache and retry

**Option B: Via SSH (If you have shell access)**

```bash
ssh your_username@aiquiz.vibeai.cv
cd /var/www/aiquiz.vibeai.cv/aiq2/api  # or wherever your files are
nano config.php  # or vi config.php
# Edit the file and save
```

**Option C: Via Hosting Control Panel File Manager**

1. Log in to your hosting control panel
2. Open File Manager
3. Navigate to `/aiq2/api/config.php`
4. Right-click → Edit
5. Update the database credentials
6. Save the file

---

## Common Database Names

Hosting providers often format database names like:
- `username_dbname` (e.g., `vibeai_aiqz`)
- `cpanel_username_database` (e.g., `vibeaicv_aiquiz`)
- Just `aiqz_production` (if you created it yourself)

Same for usernames:
- `username_user` (e.g., `vibeai_user`)
- `aiqz_user`

**Important:** The exact format depends on your hosting provider!

---

## Testing After Fix

1. **Visit the diagnostic script:**
   ```
   https://aiquiz.vibeai.cv/aiq2/fix/check-db-connection.php
   ```
   You should see: `"connection_test": "SUCCESS ✓"`

2. **Run the schema update:**
   ```
   https://aiquiz.vibeai.cv/aiq2/fix/update_schema.php
   ```
   Should now work without errors

3. **Test your application:**
   ```
   https://aiquiz.vibeai.cv
   ```
   Try logging in

---

## If Database Doesn't Exist

If you get error `Unknown database 'aiqz_production'`:

1. Log in to cPanel/Plesk
2. Go to MySQL Databases
3. Create a new database (name it whatever you want, e.g., `aiquiz_db`)
4. Create or assign a user to that database
5. Give the user ALL PRIVILEGES
6. Import the schema:
   - Upload `/api/schema.sql` via phpMyAdmin
   - Or run: `mysql -u username -p database_name < schema.sql`
7. Update `config.php` with the new database name

---

## Still Not Working?

Run the diagnostic script and send me the output. It will tell us exactly what's wrong:

```
https://aiquiz.vibeai.cv/aiq2/fix/check-db-connection.php
```

---

## Security Note

After fixing, remember to:
1. Delete `check-db-connection.php` from production (it exposes config info)
2. Delete `update_schema.php` after running it
3. Never commit real passwords to Git (use .env files locally)

---

## Quick Reference

| File to Edit | Location on Server |
|-------------|-------------------|
| config.php  | `/aiq2/api/config.php` |
| schema.sql  | `/aiq2/api/schema.sql` |

| What to Change | Line Number |
|---------------|-------------|
| DB_HOST       | Line 5 |
| DB_NAME       | Line 6 |
| DB_USER       | Line 7 |
| DB_PASS       | Line 8 |
