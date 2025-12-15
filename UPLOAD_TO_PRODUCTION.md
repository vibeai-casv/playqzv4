# Production Upload Checklist - AIQ3 Final Build
**Date:** December 15, 2025 23:48  
**Build:** Latest with Original Filename Display Feature

---

## 📋 Files to Upload to `/public_html/aiq3/`

### ✅ Priority 1: Updated Frontend Files (MUST UPLOAD)

#### Root Files:
```
AIQ3_FINAL/index.html              → /public_html/aiq3/index.html
```

#### Assets Folder (Complete folder - overwrite all):
```
AIQ3_FINAL/assets/*.js             → /public_html/aiq3/assets/
AIQ3_FINAL/assets/*.css            → /public_html/aiq3/assets/
AIQ3_FINAL/assets/logos/           → /public_html/aiq3/assets/logos/
```

**Key Files:**
- `assets/index-J9VatTvB.css` (101 KB) - Main stylesheet
- `assets/charts-DTCCMxz6.js` (377 KB) - Main bundle
- `assets/index-fliBAwov.js` (248 KB) - React core
- All 31 JS files in assets/ folder

---

### ⚠️ Priority 2: Configuration Files (Check First)

#### If NOT already on server:
```
AIQ3_FINAL/.htaccess               → /public_html/aiq3/.htaccess
AIQ3_FINAL/config.php.template     → /public_html/aiq3/config.php.template
```

**Note:** If `.htaccess` and `config.php` already exist on server, **DO NOT OVERWRITE** unless you've made changes.

---

### 📦 Priority 3: Backend API (If Updated)

#### Only if you've made API changes:
```
AIQ3_FINAL/api/                    → /public_html/aiq3/api/
```

**Note:** The following API files were NOT modified in this build:
- All question import/export scripts are unchanged
- Database functions are unchanged
- Current deployment should have working API

**Skip uploading API unless you need:**
- Latest import/export scripts
- Database schema updates
- New API endpoints

---

### 🎨 Static Assets (Optional)

#### Images & Media:
```
AIQ3_FINAL/favicon.png
AIQ3_FINAL/vite.svg
AIQ3_FINAL/aiq3.png
AIQ3_FINAL/aiq4.png
AIQ3_FINAL/aiqmpm.png
AIQ3_FINAL/splash1.mp4
AIQ3_FINAL/aiq44.mp4
AIQ3_FINAL/aiq4Anim.mp4
```

**Note:** Only upload if these are new or updated.

---

## 🚀 Quick Upload Method

### Option 1: Upload Only Changed Files (Fastest)

**Upload these 2 items:**
1. `AIQ3_FINAL/index.html` → overwrite
2. `AIQ3_FINAL/assets/` (entire folder) → overwrite

### Option 2: Complete Fresh Install

**Upload entire AIQ3_FINAL folder:**
```
All contents of AIQ3_FINAL/ → /public_html/aiq3/
```

Then:
1. Rename `config.php.template` to `config.php`
2. Update database credentials in `config.php`
3. Set `uploads/` folder permissions to 755

---

## 📊 File Sizes Summary

```
Frontend:
- index.html:           617 bytes
- CSS (total):          101 KB
- JavaScript (total):   ~1.3 MB
- Assets folder:        ~1.5 MB total

Complete Package:
- All files:            ~7 MB (including videos/images)
- Production files only: ~1.5 MB (frontend + API)
```

---

## ✅ Minimal Upload (Recommended)

**For this update, upload ONLY:**

```bash
/public_html/aiq3/index.html           (overwrite)
/public_html/aiq3/assets/*             (overwrite entire folder)
```

---

## 🧪 After Upload - Testing

1. **Clear Browser Cache:**
   - Press `Ctrl + Shift + R` (hard reload)
   - Or clear browser cache completely

2. **Test the New Feature:**
   - Go to: `https://aiquiz.vibeai.cv/aiq3/admin/questions`
   - Click "Edit" on any image question
   - Upload or select an image
   - ✅ Verify: **Original filename appears below image**
   - Example: Shows `alan-turing.png` instead of `69404e87ecff46.83987423.png`

3. **Test Import:**
   - Import the fixed JSON: `qbank/cgptset2-10_FIXED.json`
   - Should import 10 personality questions
   - ✅ Verify: No foreign key errors

---

## 📝 Notes

- **Database is ready:** `created_by` column is now nullable ✅
- **Import fixed:** Foreign key error resolved ✅
- **New feature:** Original filename display added ✅
- **Build time:** 7.88 seconds
- **Bundle size:** 377 KB (110 KB gzipped)

---

## 🔧 Deployment Command (via FTP/cPanel)

1. Navigate to `/public_html/aiq3/`
2. Delete old `assets/` folder
3. Upload new `index.html`
4. Upload new `assets/` folder
5. Done! ✅

---

**Ready for deployment!** 🚀
