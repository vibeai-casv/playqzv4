# FTP Upload Instructions

## Files Prepared for Upload

This folder contains all files ready for FTP upload to your server.

## Directory Structure

upload_package/
├── public/          → Upload to your web root (e.g., /public_html/)
│   ├── index.html
│   ├── assets/
│   └── .htaccess
│
└── api/             → Upload to /api/ folder on server
    ├── auth/
    ├── quiz/
    ├── admin/
    ├── config.php   ⚠️ EDIT THIS FIRST!
    └── .htaccess

## Before Uploading

1. ✅ Edit api/config.php with your database credentials
2. ✅ Create database in your hosting control panel
3. ✅ Import schema.sql via phpMyAdmin
4. ✅ Install FileZilla or FTP client

## Upload Steps

1. Connect to FTP server
2. Navigate to web root (e.g., /public_html/)
3. Upload ALL files from public/ to web root
4. Create /api/ folder on server
5. Upload ALL files from api/ to /api/ folder
6. Verify all files uploaded correctly

## After Uploading

1. ✅ Visit https://aiquiz.vibeai.cv
2. ✅ Test login
3. ✅ Change admin password
4. ✅ Set up SSL certificate
5. ✅ Configure backups

## Admin Credentials

Email: vibeaicasv@gmail.com
Password: password123
⚠️ CHANGE PASSWORD IMMEDIATELY AFTER FIRST LOGIN!

## Need Help?

See DEPLOY_FTP_GUIDE.md for detailed instructions.
