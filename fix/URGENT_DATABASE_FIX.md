# IMMEDIATE ACTION REQUIRED: Fix Database Connection

## The Problem
❌ **Error:** Access denied for user 'aiqz_user'@'localhost' (using password: YES)

**Cause:** The production config file has a placeholder password instead of your actual database credentials.

---

## Quick Fix (5 minutes)

### What You Need
1. Your hosting control panel login (cPanel, Plesk, etc.)
2. FTP/SFTP access to your server
3. 5 minutes

### Steps

#### 1️⃣ Get Database Credentials (2 min)

Log in to your hosting control panel and find:
- Database Name
- Database Username  
- Database Password (reset if you don't know it)

**Where to find:** Look for "MySQL Databases" or "Database Management" section

#### 2️⃣ Upload Diagnostic Tool (1 min)

Upload this file to check the connection:
```
Local:  fix/check-db-connection.php
Remote: /aiq2/fix/check-db-connection.php
```

Then visit: `https://aiquiz.vibeai.cv/aiq2/fix/check-db-connection.php`

This will show exactly what's wrong.

#### 3️⃣ Fix the Config File (2 min)

**Using FTP:**
1. Connect to your server
2. Navigate to: `/aiq2/api/config.php`
3. Edit the file (download → edit → upload)
4. Change line 8 from:
   ```php
   define('DB_PASS', 'YOUR_PASSWORD_HERE'); // CHANGE THIS!
   ```
   To:
   ```php
   define('DB_PASS', 'your_actual_password');
   ```
5. Also update DB_NAME and DB_USER if needed (lines 6-7)
6. Save and upload

**Using cPanel File Manager:**
1. Log in to cPanel
2. Open File Manager
3. Navigate to: `/aiq2/api/config.php`
4. Right-click → Edit
5. Update database credentials
6. Save

---

## Verify It Works

After updating the config, visit these URLs in order:

1. **Check Connection:**
   ```
   https://aiquiz.vibeai.cv/aiq2/fix/check-db-connection.php
   ```
   Should show: `"connection_test": "SUCCESS ✓"`

2. **Run Schema Update:**
   ```
   https://aiquiz.vibeai.cv/aiq2/fix/update_schema.php
   ```
   Should complete without errors

3. **Test Application:**
   ```
   https://aiquiz.vibeai.cv
   ```
   Should load and allow login

---

## Common Issues

### "Unknown database"
- Database doesn't exist yet
- Create it in cPanel → MySQL Databases
- Import schema.sql via phpMyAdmin
- Update config.php with the correct name

### "Access denied" still happening
- Password has special characters - make sure they're not corrupted
- User doesn't have privileges - grant ALL PRIVILEGES in cPanel
- Wrong username - check in cPanel what the actual username is

### "Can't connect to MySQL server"
- MySQL is down - contact hosting provider
- Wrong host - try 'localhost' or '127.0.0.1'

---

## Files To Upload

I've created these helper files for you:

| File | Upload To | Purpose |
|------|-----------|---------|
| `fix/check-db-connection.php` | `/aiq2/fix/` | Diagnose connection issues |
| `fix/DATABASE_FIX_GUIDE.md` | (Keep local) | Full reference guide |

---

## Need Help?

1. Upload `check-db-connection.php` to `/aiq2/fix/`
2. Visit it in browser
3. Copy the JSON output
4. Send it to me - I'll tell you exactly what to fix

---

## After Fixing

✅ Delete these files from production (security):
- `/aiq2/fix/check-db-connection.php`
- `/aiq2/fix/update_schema.php`
- Any other `/fix/*` files

---

**Bottom Line:** You need to replace `YOUR_PASSWORD_HERE` in `/aiq2/api/config.php` with your actual database password from your hosting panel.
