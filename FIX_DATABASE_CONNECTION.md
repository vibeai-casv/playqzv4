# 🔧 FIX DATABASE CONNECTION ERROR

## ❌ Error You're Getting

```
Access denied for user 'root'@'localhost' (using password: NO)
```

**What this means:**
- The `config.php` file on production is NOT configured
- It's using default development settings (root, no password)
- Need to update it with your actual production database credentials

---

## ✅ HOW TO FIX (3 Steps)

### **Step 1: Find Your Database Credentials**

**Where to find them:**
- Login to your hosting control panel (cPanel/Plesk)
- Go to "MySQL Databases" or "Databases"
- Look for database: `rcdzrtua_aiquiz` (from the error)

**You need:**
- ✅ Database Name: `rcdzrtua_aiquiz` (we know this from error)
- ❓ Database Username: Usually `rcdzrtua_user` or similar
- ❓ Database Password: Your hosting provider gave you this
- ✅ Database Host: `localhost` (usually)

**How to find username/password:**
1. cPanel → MySQL Databases
2. Scroll to "Current Users"
3. Note the username (usually `prefix_username`)
4. If you forgot password, you can reset it there

---

### **Step 2: Update config.php on Production**

**Option A: Edit via File Manager (Easiest)**

1. Login to cPanel/hosting control panel
2. Go to **File Manager**
3. Navigate to: `/public_html/aiq3/api/`
4. Find `config.php` (or create it if missing)
5. Right-click → Edit
6. Replace contents with:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'rcdzrtua_aiquiz');
define('DB_USER', 'YOUR_DB_USERNAME');  // Replace with actual username
define('DB_PASS', 'YOUR_DB_PASSWORD');  // Replace with actual password
define('DB_CHARSET', 'utf8mb4');

define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');

define('AI_PROVIDER', 'gemini');
define('AI_API_KEY', '');
define('AI_MODEL', 'gemini-1.5-flash');

define('DEBUG_MODE', false);
?>
```

7. **IMPORTANT:** Replace `YOUR_DB_USERNAME` and `YOUR_DB_PASSWORD` with your actual credentials
8. Save the file

---

**Option B: Upload via FTP**

1. I created: `config_PRODUCTION.php` in your project folder
2. Open it and fill in your database username and password
3. Upload to: `/public_html/aiq3/api/config.php`
4. Make sure it's named `config.php` (not config_PRODUCTION.php)

---

### **Step 3: Test the Connection**

**After updating config.php:**

1. Visit: `https://aiquiz.vibeai.cv/aiq3/api/fix_profile.php`
2. Should now show: "Profile created successfully!" (or already exists)
3. If still errors, check the credentials again

---

## 📋 Common Database Usernames

**Pattern:** `cpanel_username_dbuser`

Examples:
- `rcdzrtua_aiqzuser`
- `rcdzrtua_admin`
- `rcdzrtua_user`

**Check in cPanel:**
- MySQL Databases → Current Users section

---

## 🔍 How to Find Your Exact Credentials

### **Method 1: cPanel → MySQL Databases**

```
1. Login to cPanel
2. Find "MySQL Databases" icon
3. Scroll to "Current Databases"
   - Database: rcdzrtua_aiquiz ✓ (we have this)
4. Scroll to "Current Users"
   - Username: rcdzrtua_????? (copy this)
5. Click "Change Password" if you don't know it
   - Set new password
   - Copy it
```

### **Method 2: Check Existing Config (if any)**

```
1. cPanel → File Manager
2. Go to /public_html/aiq3/api/
3. Check if config.php exists
4. If yes, open it and see what credentials are there
5. If no, you need to create it
```

### **Method 3: Ask Hosting Support**

If you can't find the credentials:
- Contact your hosting provider support
- Ask for: "MySQL database credentials for rcdzrtua_aiquiz"
- They will provide username and can reset password

---

## ✅ After Fixing config.php

**Step 1: Run fix_profile.php again**
```
https://aiquiz.vibeai.cv/aiq3/api/fix_profile.php
```

**Step 2: Should see:**
```
✅ Profile created successfully!
You can now import questions successfully!
```

**Step 3: Delete fix_profile.php**
```
Remove from: /public_html/aiq3/api/fix_profile.php
```

**Step 4: Try importing questions again**
```
Admin → Import/Export → Import JSON
Should work now!
```

---

## 🎯 Quick Checklist

After updating `config.php`, verify it has:

- [ ] `DB_HOST` = `localhost` (or your host's value)
- [ ] `DB_NAME` = `rcdzrtua_aiquiz` (from error message) ✓
- [ ] `DB_USER` = Your actual database username
- [ ] `DB_PASS` = Your actual database password
- [ ] No `root` or empty password!
- [ ] File saved as `config.php` in `/public_html/aiq3/api/`

---

## 🚨 Security Notes

**After fixing:**

1. **Delete fix_profile.php** - Don't leave it on production
2. **Set config.php permissions** to 644 or 600
3. **Never commit config.php** to git with real passwords
4. **Use strong database password**

---

## 💡 Why This Happened

**The config.php file was probably:**
- Not uploaded to production
- Using template/default values
- Copied from development without changing credentials

**The error "root@localhost (no password)" means:**
- Using PHP/MySQL defaults
- config.php not loaded or not configured

---

## 📞 Still Having Issues?

**Test database connection manually:**

Create a file: `test_db.php` in `/public_html/aiq3/api/`

```php
<?php
$host = 'localhost';
$db = 'rcdzrtua_aiquiz';
$user = 'YOUR_USERNAME';  // Change this
$pass = 'YOUR_PASSWORD';  // Change this

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    echo "✅ Database connected successfully!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
```

Visit: `https://aiquiz.vibeai.cv/aiq3/api/test_db.php`

If this works, your credentials are correct!

**Then delete test_db.php for security.**

---

## ✅ Summary

1. **Find database credentials** in cPanel
2. **Update config.php** with correct username/password
3. **Run fix_profile.php** again
4. **Import should work!**

The template is at: `e:\projects\playqzv4\config_PRODUCTION.php`
