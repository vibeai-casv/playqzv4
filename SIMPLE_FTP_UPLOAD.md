# 📤 Simple FTP Upload Guide - Manual Method

## ✅ Good News!

Your frontend was **built successfully** with the correct production URL!

The script had some errors, but the build completed. Now let's upload manually.

---

## 📁 Files to Upload

### Frontend Files (from your computer):
**Location:** `e:\projects\playqzv4\client\dist\`

Upload ALL files from this folder to your server's **web root**.

### API Files (from your computer):
**Location:** `e:\projects\playqzv4\api\`

Upload ALL files from this folder to your server's **api** folder.

---

## 🚀 Step-by-Step Upload Instructions

### Step 1: Download FileZilla

If you don't have it:
1. Go to: https://filezilla-project.org/download.php?type=client
2. Download and install FileZilla Client

### Step 2: Connect to Your Server

1. Open FileZilla
2. Enter your FTP details:
   - **Host:** `ftp.aiquiz.vibeai.cv` (or your FTP host)
   - **Username:** Your FTP username
   - **Password:** Your FTP password
   - **Port:** `21` (or `22` for SFTP)
3. Click **Quickconnect**

### Step 3: Find Your Web Root

In FileZilla's right panel (Remote site), navigate to your web root.

Common paths:
- `/public_html/`
- `/www/`
- `/htdocs/`
- `/domains/aiquiz.vibeai.cv/public_html/`

### Step 4: Upload Frontend Files

1. **Left panel** (Local site): Navigate to `e:\projects\playqzv4\client\dist\`
2. **Right panel** (Remote site): Make sure you're in your web root
3. **Select ALL files** in the left panel (Ctrl+A)
4. **Drag and drop** to the right panel
5. When asked "Target file already exists", choose **Overwrite**
6. Wait for upload to complete

### Step 5: Create API Folder (if it doesn't exist)

1. In the right panel (Remote site), right-click
2. Select "Create directory"
3. Name it: `api`
4. Double-click to enter the `api` folder

### Step 6: Upload API Files

1. **Left panel**: Navigate to `e:\projects\playqzv4\api\`
2. **Right panel**: Make sure you're in the `/api/` folder
3. **Select ALL files and folders** in the left panel (Ctrl+A)
4. **Drag and drop** to the right panel
5. Choose **Overwrite** if asked
6. Wait for upload to complete

### Step 7: Verify Upload

Check that these files exist on your server:

**In web root:**
- ✅ `index.html`
- ✅ `assets/` folder with JS and CSS files

**In /api/ folder:**
- ✅ `index.php`
- ✅ `config.php`
- ✅ `db.php`
- ✅ `auth/` folder with `login.php`, `signup.php`, etc.
- ✅ `quiz/` folder
- ✅ `admin/` folder

---

## 🔧 Step 8: Update Database Configuration

### Via FTP:

1. In FileZilla, navigate to `/api/` folder
2. Right-click on `config.php`
3. Select "View/Edit"
4. Update these lines:

```php
define('DB_NAME', 'your_database_name');  // From your hosting
define('DB_USER', 'your_database_user');  // From your hosting
define('DB_PASS', 'your_database_password');  // From your hosting
```

5. Save the file
6. FileZilla will ask to upload - click **Yes**

### Via cPanel File Manager:

1. Login to cPanel
2. Open "File Manager"
3. Navigate to `/public_html/api/`
4. Right-click `config.php` → Edit
5. Update database credentials
6. Save

---

## 🗄️ Step 9: Set Up Database

### Create Database:

1. Login to cPanel
2. Go to "MySQL Databases"
3. Create new database (e.g., `aiqz_production`)
4. Create new user with strong password
5. Add user to database with ALL PRIVILEGES

### Import Schema:

1. Open phpMyAdmin from cPanel
2. Select your database
3. Click "Import" tab
4. Click "Choose File"
5. Select: `e:\projects\playqzv4\api\schema.sql`
6. Click "Go"
7. Wait for success message

### Create Admin User:

1. In phpMyAdmin, click "SQL" tab
2. Paste this code:

```sql
SET @user_id = UUID();

INSERT INTO users (id, email, password_hash, created_at)
VALUES (
    @user_id,
    'vibeaicasv@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    NOW()
);

INSERT INTO profiles (id, email, name, role, created_at)
VALUES (
    @user_id,
    'vibeaicasv@gmail.com',
    'Admin User',
    'admin',
    NOW()
);
```

3. Click "Go"

---

## ✅ Step 10: Test Your Deployment

### Test 1: API Endpoint

Open in browser:
```
https://aiquiz.vibeai.cv/api/index.php
```

**Expected:** JSON response with API info

**If 404:** API files not uploaded correctly

### Test 2: Website

Open in browser:
```
https://aiquiz.vibeai.cv
```

**Expected:** Login page loads

### Test 3: Login

1. Go to login page
2. Enter:
   - Email: `vibeaicasv@gmail.com`
   - Password: `password123`
3. Click Login

**Expected:** Login succeeds, redirected to dashboard

---

## 🔧 Create .htaccess Files

### Frontend .htaccess

Create a new file in your web root called `.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} !^/api/
    
    RewriteRule . /index.html [L]
</IfModule>

Options -Indexes
```

**How to create via FileZilla:**
1. Right-click in web root → Create new file
2. Name it: `.htaccess`
3. Right-click → View/Edit
4. Paste the content above
5. Save and upload

### API .htaccess

Create another `.htaccess` in the `/api/` folder:

```apache
php_flag display_errors Off
php_flag log_errors On

php_value upload_max_filesize 10M
php_value post_max_size 10M
```

---

## 📋 Quick Checklist

- [ ] Frontend files uploaded to web root
- [ ] API files uploaded to `/api/` folder
- [ ] `config.php` updated with database credentials
- [ ] Database created in cPanel
- [ ] Schema imported via phpMyAdmin
- [ ] Admin user created
- [ ] `.htaccess` files created
- [ ] Can access `https://aiquiz.vibeai.cv/api/index.php`
- [ ] Can access `https://aiquiz.vibeai.cv`
- [ ] Can login successfully

---

## 🆘 Troubleshooting

### Still getting 404 on login?

1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Test API directly:** `https://aiquiz.vibeai.cv/api/index.php`
3. **Check file structure** in FileZilla - make sure `api` folder is in web root
4. **Check config.php** has correct database credentials
5. **Check browser console** (F12) for exact error

### Common Issues:

**Issue:** API returns 404
- **Solution:** API files not in correct location. Re-upload to `/api/` folder in web root.

**Issue:** Can't edit config.php
- **Solution:** Use cPanel File Manager instead of FileZilla.

**Issue:** Database connection error
- **Solution:** Check credentials in `config.php` match your cPanel database.

---

## 📞 Summary

**What you need to do:**

1. ✅ Upload `client/dist/*` to web root via FileZilla
2. ✅ Upload `api/*` to `/api/` folder via FileZilla
3. ✅ Edit `api/config.php` with database credentials
4. ✅ Create database and import schema via cPanel
5. ✅ Test: `https://aiquiz.vibeai.cv/api/index.php`
6. ✅ Test login

**Your files are ready in:**
- Frontend: `e:\projects\playqzv4\client\dist\`
- API: `e:\projects\playqzv4\api\`

---

**Good luck!** 🚀

If you still get 404 after uploading, send me a screenshot of:
1. Your FileZilla file structure (right panel showing web root)
2. The browser console error (F12 → Console tab)
