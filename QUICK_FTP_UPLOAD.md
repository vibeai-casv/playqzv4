# 🚀 QUICK FTP UPLOAD GUIDE - Theme Update

## ✅ Files Are Ready!

Your production-ready files are in: **`e:\projects\playqzv4\upload_package\`**

---

## 📦 What to Upload

### ✅ MUST UPLOAD (Frontend - New Theme):
```
upload_package\public\
├── index.html
├── assets\
│   ├── index-[hash].js
│   ├── index-[hash].css  ← NEW THEME HERE!
│   └── [other files]
├── aiqmpm.png
└── .htaccess
```

### ⚠️ SKIP (API - No changes needed):
```
upload_package\api\
└── [skip these files]
```

---

## 🎯 FileZilla Upload Steps

### 1. Download FileZilla
https://filezilla-project.org/download.php?type=client

### 2. Connect to Server
```
Host: ftp.aiquiz.vibeai.cv
Username: [your FTP username]
Password: [your FTP password]
Port: 21
```

### 3. Upload Files

**Left Panel (Local)**:
```
e:\projects\playqzv4\upload_package\public\
```

**Right Panel (Remote)**:
```
/public_html/
```

**Action**:
1. Select ALL files in left panel
2. Drag to right panel
3. Click "Overwrite" when asked
4. Wait for upload to complete

---

## ⏱️ Upload Time

- **Small files**: 2-3 minutes
- **Large files**: 5-10 minutes
- **Total**: ~5-10 minutes

---

## ✅ After Upload

### 1. Clear Browser Cache
```
Ctrl + Shift + Delete
→ Clear cached images and files
```

### 2. Visit Website
```
https://aiquiz.vibeai.cv
```

### 3. Verify New Theme
- ✅ Cyan colors
- ✅ Glassmorphic card
- ✅ Gradient background
- ✅ New buttons

---

## 🆘 Troubleshooting

### Old theme showing?
**Solution**: Hard refresh
```
Windows: Ctrl + Shift + R
Mac: Cmd + Shift + R
```

### Files not uploading?
**Solution**: Check FTP credentials

### Blank page?
**Solution**: Upload .htaccess file

---

## 📞 FTP Credentials

Fill in your details:

```
Host: ftp.aiquiz.vibeai.cv
Username: _______________
Password: _______________
Port: 21
```

---

## 🎉 You're Ready!

1. ✅ Files built and prepared
2. ✅ Upload package created
3. ⏳ Upload via FileZilla
4. ✅ Test and enjoy!

**Upload Location**: `e:\projects\playqzv4\upload_package\public\`
**Destination**: `/public_html/` on your server

---

**Good luck! Your new theme will be live in ~10 minutes!** 🚀
