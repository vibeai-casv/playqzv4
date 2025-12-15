# 🚀 AIQ3 Production Deployment Guide

## 📦 Package Contents

This package contains a complete production build for **aiquiz.vibeai.cv/aiq3/**

### Included:
- ✅ Frontend (React/Vite build with /aiq3/ base path)
- ✅ Backend API (PHP files)
- ✅ .htaccess files (frontend and API)
- ✅ Config template
- ✅ All fixes and diagnostics

---

## 📤 Upload Instructions

### 1. Upload Files

**Via FTP/cPanel:**
`
Upload ALL contents of AIQ3_PRODUCTION/ to:
/public_html/aiq3/
`

**Important:** Upload the CONTENTS of the folder, not the folder itself!

### 2. Configure Database

1. **Copy config template:**
   `
   Copy: /aiq3/api/config.php.template
   To:   /aiq3/api/config.php
   `

2. **Edit config.php:**
   - Update DB_NAME with your database name
   - Update DB_USER with your database username  
   - Update DBPASS with your database password
   - Update JWT_SECRET with a random 32+ character string

### 3. Create Database

If not already created, import the schema:
`
mysql -u YOUR_USER -p YOUR_DATABASE < schema.sql
`

Or use the schema update tool after deployment.

---

## ✅ Verify Installation

### Test URLs:

1. **Frontend:**
   `
   https://aiquiz.vibeai.cv/aiq3/
   `
   Should show the login page

2. **API Test:**
   `
   https://aiquiz.vibeai.cv/aiq3/api/admin/test.php
   `
   Should return JSON (may show auth error - that's OK)

3. **Login:**
   - Use your admin credentials
   - Should redirect to dashboard

---

## 🔧 Key Features in This Build

### Fixed Issues:
- ✅ API path detection (no longer hardcoded)
- ✅ Ignores VITE_API_URL environment variable
- ✅ Path detection from window.location
- ✅ Works in /aiq3/ subdirectory

### New Features:
- ✅ Bundle import with comprehensive diagnostics
- ✅ Schema update tool with debug logging
- ✅ Test API Connection button
- ✅ Detailed error messages

### Diagnostic Features:
- ✅ Frontend console logging (F12)
- ✅ Backend error logging (import_errors.log)
- ✅ Step-by-step import tracking
- ✅ Clear error messages

---

## 📁 Directory Structure

`
/public_html/aiq3/
├── index.html              (Frontend entry)
├── assets/                 (JS/CSS bundles)
├── aiq4.png               (Logo)
├── .htaccess              (Frontend routing)
├── api/
│   ├── .htaccess          (API CORS)
│   ├── config.php         (Your config - create from template!)
│   ├── db.php             (Database connection)
│   ├── utils.php          (Helper functions)
│   ├── admin/             (Admin endpoints)
│   ├── auth/              (Authentication)
│   ├── bundle/            (Import/Export)
│   ├── questions/         (Questions API)
│   └── ...
└── uploads/               (Media storage)
`

---

## 🔒 Security Checklist

After deployment:

- [ ] Updated config.php with production credentials
- [ ] Changed JWT_SECRET to random string
- [ ] Set correct file permissions (644 for files, 755 for dirs)
- [ ] Verified .htaccess files are active
- [ ] Tested HTTPS redirect
- [ ] Deleted config.php.template
- [ ] Removed any diagnostic/test files

---

## 🧪 Testing Checklist

- [ ] Can access https://aiquiz.vibeai.cv/aiq3/
- [ ] Can login with admin credentials
- [ ] Dashboard loads correctly
- [ ] Can view questions
- [ ] Can take a quiz
- [ ] Import/export works (check console logs)
- [ ] Schema update works
- [ ] Media upload works

---

## 🐛 Troubleshooting

### Common Issues:

**1. "Failed to fetch" errors:**
- Check: api/config.php exists and has correct credentials
- Check: Database is accessible
- Check: .htaccess files are uploaded

**2. 404 on API calls:**
- Check: Files uploaded to /aiq3/ not /aiq2/
- Check: .htaccess files prevent directory listing
- Test: /aiq3/api/admin/test.php directly

**3. Bundle import fails:**
- Open console (F12) and check logs
- Look for detailed error messages
- Check import_errors.log on server

**4. Schema update fails:**
- Click "Test API Connection" first
- Should show: "Detected API URL: /aiq3/api"
- Check console for detailed debug info

---

## 📞 Support

For issues:
1. Check console logs (F12)
2. Check import_errors.log on server
3. Verify config.php settings
4. Test API endpoint directly

---

**This is a fresh AIQ3 deployment with all latest fixes!** 🎉

Built: 2025-12-14 23:48:51
Path: /aiq3/
URL: https://aiquiz.vibeai.cv/aiq3/
