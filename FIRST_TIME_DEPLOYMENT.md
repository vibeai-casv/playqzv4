# First-Time Deployment Guide
## Step-by-Step Instructions for aiquiz.vibeai.cv

This is a simplified guide for deploying your AI Quiz application for the **first time**. Follow these steps in order.

---

## ✅ Prerequisites Check

Before starting, make sure you have:

- [ ] SSH access to your server (aiquiz.vibeai.cv)
- [ ] Root or sudo privileges on the server
- [ ] Your domain DNS is pointing to the server IP
- [ ] Node.js and npm installed locally (for building)

---

## 📝 Step 1: Prepare Your Local Machine

### 1.1 Update Deployment Script

Open `deploy.ps1` and update line 11:

```powershell
$REMOTE_USER = "your_actual_username"  # Replace with your SSH username
```

### 1.2 Test SSH Connection

```powershell
ssh your_username@aiquiz.vibeai.cv
```

If this works, you're good! Type `exit` to disconnect.

---

## 🖥️ Step 2: Set Up the Production Server

### 2.1 Upload Server Setup Script

```powershell
scp server-setup.sh your_username@aiquiz.vibeai.cv:~/
```

### 2.2 Connect to Server

```powershell
ssh your_username@aiquiz.vibeai.cv
```

### 2.3 Run Server Setup Script

```bash
sudo bash server-setup.sh
```

This will:
- Install Apache, PHP, and MySQL
- Create the database and user
- Configure the web server
- Set up directories
- Create backup scripts

**IMPORTANT**: The script will display database credentials. **SAVE THEM!**

Example output:
```
Database: aiqz_production
User: aiqz_user
Password: [random password]
```

Also saved to: `/root/aiquiz_db_credentials.txt`

### 2.4 Exit Server

```bash
exit
```

---

## 🔧 Step 3: Update Production Configuration

### 3.1 Update Backend Config

Edit `api/config.production.php` and update these lines with the credentials from Step 2.3:

```php
define('DB_PASS', 'YOUR_ACTUAL_PASSWORD_FROM_STEP_2.3');
```

The other settings should already be correct:
- `DB_HOST`: localhost
- `DB_NAME`: aiqz_production
- `DB_USER`: aiqz_user
- `ALLOWED_ORIGIN`: https://aiquiz.vibeai.cv

### 3.2 Verify Frontend Config

Check that `client/.env.production` contains:

```env
VITE_API_URL=https://aiquiz.vibeai.cv/api
```

This should already be correct.

---

## 🚀 Step 4: Build and Deploy

### 4.1 Build Frontend

```powershell
cd client
npm install
npm run build
cd ..
```

Wait for the build to complete. You should see output like:
```
✓ built in 15s
```

### 4.2 Run Deployment Script

```powershell
.\deploy.ps1
```

The script will:
1. Build the frontend ✓
2. Test SSH connection ✓
3. Create backup ✓
4. Upload frontend files ✓
5. Upload API files ✓
6. Set permissions ✓
7. Restart web server ✓
8. Test deployment ✓

---

## 🗄️ Step 5: Set Up Database

### 5.1 Connect to Server

```powershell
ssh your_username@aiquiz.vibeai.cv
```

### 5.2 Import Database Schema

```bash
mysql -u aiqz_user -p aiqz_production < /var/www/aiquiz.vibeai.cv/api/schema.sql
```

Enter the database password when prompted (from Step 2.3).

### 5.3 Create Admin User

```bash
mysql -u aiqz_user -p aiqz_production
```

Then run these SQL commands:

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

SELECT 'Admin user created successfully!' AS status;

EXIT;
```

**Admin Credentials:**
- Email: vibeaicasv@gmail.com
- Password: password123

⚠️ **You MUST change this password after first login!**

### 5.4 Update Production Config

```bash
sudo nano /var/www/aiquiz.vibeai.cv/api/config.php
```

Update with your database credentials:

```php
define('DB_PASS', 'your_password_from_step_2.3');
```

Press `Ctrl+X`, then `Y`, then `Enter` to save.

### 5.5 Exit Server

```bash
exit
```

---

## 🔒 Step 6: Install SSL Certificate

### 6.1 Connect to Server

```powershell
ssh your_username@aiquiz.vibeai.cv
```

### 6.2 Install SSL Certificate

```bash
sudo certbot --apache -d aiquiz.vibeai.cv -d www.aiquiz.vibeai.cv
```

Follow the prompts:
1. Enter your email address
2. Agree to terms of service (Y)
3. Choose whether to share email (Y/N - your choice)
4. Choose option 2: Redirect HTTP to HTTPS

### 6.3 Test Auto-Renewal

```bash
sudo certbot renew --dry-run
```

If successful, you'll see: "Congratulations, all simulated renewals succeeded"

### 6.4 Exit Server

```bash
exit
```

---

## ✅ Step 7: Test Your Deployment

### 7.1 Visit Your Website

Open your browser and go to:
```
https://aiquiz.vibeai.cv
```

You should see the login page with:
- ✅ Green padlock (SSL working)
- ✅ No security warnings
- ✅ Login form visible

### 7.2 Test Login

1. Click "Login"
2. Enter:
   - Email: vibeaicasv@gmail.com
   - Password: password123
3. Click "Sign In"

You should be redirected to the dashboard.

### 7.3 Change Admin Password

1. Go to Profile/Settings
2. Change password from "password123" to a secure password
3. Save changes

### 7.4 Test Quiz Functionality

1. Try creating a quiz
2. Take a quiz
3. View results
4. Check admin dashboard

---

## 🎉 Step 8: Post-Deployment Tasks

### 8.1 Set Up Automated Backups

Connect to server:
```powershell
ssh your_username@aiquiz.vibeai.cv
```

Add to crontab:
```bash
sudo crontab -e
```

Add this line (daily backup at 2 AM):
```
0 2 * * * /usr/local/bin/backup-aiquiz.sh
```

Save and exit.

### 8.2 Test Backup Script

```bash
sudo /usr/local/bin/backup-aiquiz.sh
```

Check backup was created:
```bash
ls -lh /var/backups/aiquiz/
```

### 8.3 Monitor Logs

```bash
# Check for errors
sudo tail -f /var/log/aiquiz/error.log
```

Press `Ctrl+C` to stop.

If no errors, you're good!

```bash
exit
```

---

## 📋 Deployment Checklist

Go through this checklist to verify everything is working:

- [ ] Website loads at https://aiquiz.vibeai.cv
- [ ] SSL certificate is valid (green padlock)
- [ ] Can login with admin credentials
- [ ] Admin password has been changed
- [ ] Can create a quiz
- [ ] Can take a quiz
- [ ] Results are saved
- [ ] Admin dashboard shows data
- [ ] No errors in browser console (F12)
- [ ] No errors in server logs
- [ ] Automated backups are scheduled
- [ ] Database credentials are secure

---

## 🆘 Troubleshooting

### Problem: Can't connect via SSH

**Solution:**
```powershell
# Verify server is reachable
ping aiquiz.vibeai.cv

# Check SSH is running on server
# Contact your hosting provider if needed
```

### Problem: Website shows "Connection not secure"

**Solution:**
- SSL certificate not installed yet
- Run Step 6 to install SSL certificate

### Problem: Website shows 404 error

**Solution:**
```bash
# Connect to server
ssh your_username@aiquiz.vibeai.cv

# Check Apache is running
sudo systemctl status apache2

# Restart Apache
sudo systemctl restart apache2

# Check virtual host is enabled
sudo a2ensite aiquiz.vibeai.cv.conf
sudo systemctl reload apache2
```

### Problem: Can't login

**Solution:**
```bash
# Connect to server
ssh your_username@aiquiz.vibeai.cv

# Check database
mysql -u aiqz_user -p aiqz_production -e "SELECT email, role FROM profiles;"

# Check API logs
sudo tail -n 50 /var/log/aiquiz/error.log
```

### Problem: Database connection error

**Solution:**
```bash
# Connect to server
ssh your_username@aiquiz.vibeai.cv

# Verify database credentials in config
sudo cat /var/www/aiquiz.vibeai.cv/api/config.php

# Test database connection
mysql -u aiqz_user -p aiqz_production -e "SELECT 1;"
```

---

## 📞 Need Help?

If you encounter issues:

1. **Check the logs:**
   ```bash
   sudo tail -n 100 /var/log/aiquiz/error.log
   ```

2. **Review the complete guide:**
   - See `.agent/workflows/deploy-production.md`

3. **Check configuration:**
   - Frontend: `client/.env.production`
   - Backend: `api/config.php` (on server)

4. **Verify services are running:**
   ```bash
   sudo systemctl status apache2
   sudo systemctl status mysql
   ```

---

## 🎯 Success!

If you've completed all steps and passed the checklist, congratulations! 🎉

Your AI Quiz application is now live at:
**https://aiquiz.vibeai.cv**

---

## 🔄 Future Deployments

For subsequent updates, you only need to:

1. Make your changes locally
2. Build: `cd client && npm run build && cd ..`
3. Deploy: `.\deploy.ps1`

That's it! The automated script handles everything else.

---

**Deployment Date**: _______________  
**Deployed By**: _______________  
**Notes**: _______________

---

## 📚 Additional Resources

- [Complete Deployment Guide](.agent/workflows/deploy-production.md)
- [Quick Reference](DEPLOY_QUICK_REF.md)
- [Architecture Diagram](ARCHITECTURE.md)
- [Deployment Summary](DEPLOYMENT_SUMMARY.md)
