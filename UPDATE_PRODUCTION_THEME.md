# 🚀 Update Production Server with New Theme (FTP)

## Quick Update Guide - Vibeai.cv Theme Deployment

This guide will help you update your production server at **aiquiz.vibeai.cv** with the new vibeai.cv-inspired theme.

---

## ⚡ Quick Steps (5 Minutes)

### Step 1: Build the Frontend
```powershell
cd e:\projects\playqzv4\client
npm run build
```

This creates optimized production files in `client/dist/` folder.

### Step 2: Prepare Upload Package
```powershell
cd e:\projects\playqzv4
.\prepare-ftp-upload.ps1
```

This will:
- ✅ Copy built frontend to `upload_package/public/`
- ✅ Copy API files to `upload_package/api/`
- ✅ Create necessary .htaccess files

### Step 3: Upload via FTP

#### Option A: Using FileZilla (Recommended)

1. **Open FileZilla**
2. **Connect to Server**:
   - Host: `ftp.aiquiz.vibeai.cv` (or your FTP host)
   - Username: [your FTP username]
   - Password: [your FTP password]
   - Port: `21`

3. **Upload Frontend Files**:
   - **Local**: Navigate to `e:\projects\playqzv4\upload_package\public\`
   - **Remote**: Navigate to `/public_html/` (or your web root)
   - Select all files and folders
   - Right-click → Upload
   - **Overwrite** when prompted

4. **Upload API Files** (if changed):
   - **Local**: Navigate to `e:\projects\playqzv4\upload_package\api\`
   - **Remote**: Navigate to `/public_html/api/`
   - Select all files
   - Right-click → Upload
   - **Skip** `config.php` (don't overwrite production config)

#### Option B: Using WinSCP

1. Download WinSCP: https://winscp.net/
2. Create new session with your FTP credentials
3. Drag and drop files from local to remote

---

## 📦 What Files to Upload

### Frontend Files (MUST UPLOAD):
```
upload_package/public/
├── index.html              ← Main HTML file
├── assets/
│   ├── index-[hash].js    ← JavaScript bundle
│   ├── index-[hash].css   ← CSS with new theme
│   └── [other assets]
├── aiqmpm.png             ← Logo
└── .htaccess              ← Routing rules
```

### API Files (OPTIONAL - Only if you changed PHP):
```
upload_package/api/
├── auth/
├── quiz/
├── admin/
└── [other PHP files]
```

⚠️ **DO NOT UPLOAD**: `config.php` (keep production database credentials)

---

## 🎯 Detailed FileZilla Instructions

### 1. Download and Install FileZilla
- Download: https://filezilla-project.org/download.php?type=client
- Install with default settings

### 2. Connect to Your Server
1. Open FileZilla
2. Click **File** → **Site Manager** (or Ctrl+S)
3. Click **New Site**
4. Enter details:
   ```
   Protocol: FTP
   Host: ftp.aiquiz.vibeai.cv
   Port: 21
   Encryption: Use explicit FTP over TLS if available
   Logon Type: Normal
   User: [your FTP username]
   Password: [your FTP password]
   ```
5. Click **Connect**

### 3. Navigate to Correct Folders

**Left Panel (Local - Your Computer)**:
```
e:\projects\playqzv4\upload_package\public\
```

**Right Panel (Remote - Server)**:
```
/public_html/
or
/htdocs/
or
/www/
(depends on your hosting)
```

### 4. Upload Files

**Method 1: Drag and Drop**
1. Select all files in left panel
2. Drag to right panel
3. Click **OK** when asked to overwrite

**Method 2: Right-Click Upload**
1. Select all files in left panel
2. Right-click → **Upload**
3. Click **OK** when asked to overwrite

### 5. Verify Upload
- Check **Transfer Queue** at bottom
- Wait for all files to show "Successful transfer"
- Verify file sizes match

---

## ✅ Post-Upload Checklist

### 1. Clear Browser Cache
```
Ctrl + Shift + Delete
→ Clear cached images and files
→ Clear for "All time"
```

### 2. Test Website
Visit: `https://aiquiz.vibeai.cv`

**Should see**:
- ✅ New cyan theme
- ✅ Glassmorphic login card
- ✅ Gradient background
- ✅ Cyan accent colors

### 3. Test Login
- Login with your credentials
- Check dashboard loads
- Verify new theme applied

### 4. Check Console for Errors
- Press `F12` in browser
- Click **Console** tab
- Should see no errors

---

## 🔧 Troubleshooting

### Issue: Old Theme Still Showing

**Solution 1: Hard Refresh**
```
Windows: Ctrl + Shift + R
Mac: Cmd + Shift + R
```

**Solution 2: Clear Browser Cache**
```
Chrome: Ctrl + Shift + Delete
Firefox: Ctrl + Shift + Delete
Edge: Ctrl + Shift + Delete
```

**Solution 3: Verify Files Uploaded**
- Check FileZilla transfer log
- Verify `index.html` and `assets/` folder uploaded
- Check file timestamps on server

### Issue: 404 Errors

**Solution**: Upload `.htaccess` file
```
Local: upload_package/public/.htaccess
Remote: /public_html/.htaccess
```

### Issue: Blank Page

**Solution 1**: Check browser console (F12)
**Solution 2**: Verify all files in `assets/` folder uploaded
**Solution 3**: Check `.htaccess` file exists

### Issue: Can't Connect to FTP

**Solution**:
1. Verify FTP credentials with hosting provider
2. Try port `22` for SFTP
3. Check firewall settings
4. Contact hosting support

---

## 📊 Upload Progress Tracking

### Before Upload:
- [ ] Frontend built (`npm run build`)
- [ ] Upload package prepared (`prepare-ftp-upload.ps1`)
- [ ] FileZilla installed and configured
- [ ] FTP credentials ready

### During Upload:
- [ ] Connected to FTP server
- [ ] Navigated to correct folders
- [ ] Selected all files
- [ ] Upload started
- [ ] All files transferred successfully

### After Upload:
- [ ] Browser cache cleared
- [ ] Website loads with new theme
- [ ] Login works
- [ ] No console errors
- [ ] Dashboard shows new theme

---

## 🚀 Alternative: Using PowerShell FTP

If you prefer command-line:

```powershell
# Navigate to project
cd e:\projects\playqzv4

# Build frontend
cd client
npm run build
cd ..

# Prepare package
.\prepare-ftp-upload.ps1

# Upload using WinSCP command-line
# (Install WinSCP first)
"C:\Program Files (x86)\WinSCP\WinSCP.com" `
  /command `
  "open ftp://username:password@ftp.aiquiz.vibeai.cv" `
  "lcd upload_package\public" `
  "cd /public_html" `
  "put * -delete" `
  "exit"
```

---

## 📝 Quick Reference

### FTP Credentials Template
```
Host: ftp.aiquiz.vibeai.cv
Username: _______________
Password: _______________
Port: 21 (or 22 for SFTP)
```

### Local Paths
```
Build Output: e:\projects\playqzv4\client\dist\
Upload Package: e:\projects\playqzv4\upload_package\
Frontend: upload_package\public\
API: upload_package\api\
```

### Remote Paths (Common)
```
Web Root: /public_html/ or /htdocs/ or /www/
API Folder: /public_html/api/
```

---

## ⏱️ Estimated Time

- **Build**: 1-2 minutes
- **Prepare**: 30 seconds
- **Upload**: 2-5 minutes (depends on connection)
- **Testing**: 1 minute

**Total**: ~5-10 minutes

---

## 🎉 Success Indicators

After successful upload, you should see:

✅ **Login Page**:
- Dark gradient background
- Glassmorphic card
- Cyan "Sign In" button
- Gradient logo container

✅ **Dashboard**:
- Cyan gradient welcome banner
- Updated stat cards
- Cyan accent colors throughout

✅ **Buttons**:
- Cyan gradient on hover
- Smooth animations

✅ **No Errors**:
- Browser console clean
- All assets loaded
- No 404 errors

---

## 📞 Need Help?

### Check These First:
1. FileZilla transfer log (bottom panel)
2. Browser console (F12)
3. Server error logs (cPanel → Error Logs)

### Common Hosting Providers:
- **Hostinger**: FTP details in hPanel
- **Bluehost**: FTP in cPanel → FTP Accounts
- **GoDaddy**: FTP in cPanel → FTP Accounts
- **SiteGround**: FTP in Site Tools → FTP Manager

---

## 🔄 Future Updates

To update theme again in the future:

1. Make changes locally
2. Test at `http://localhost:5173`
3. Run `npm run build`
4. Run `.\prepare-ftp-upload.ps1`
5. Upload via FTP
6. Clear cache and test

---

**Ready to deploy? Let's go!** 🚀

1. Build: `npm run build`
2. Prepare: `.\prepare-ftp-upload.ps1`
3. Upload via FileZilla
4. Test and enjoy!

Your new vibeai.cv theme will be live at: **https://aiquiz.vibeai.cv**
