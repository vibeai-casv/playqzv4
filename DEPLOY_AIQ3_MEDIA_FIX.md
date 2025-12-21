# Production Deployment - Media Page Flickering Fix
**Date**: December 17, 2025  
**Fix**: Reduced media page auto-refresh from 1 minute to 5 minutes

---

## 🎯 What Changed

### Fixed File:
- `client/src/pages/admin/Media.tsx`
  - Auto-refresh interval: `60000ms` → `300000ms` (1 min → 5 min)
  - Prevents frequent page flickering
  - Manual refresh button still available

### Build Info:
- ✅ Production build completed successfully
- Build time: 8.10s
- Modules transformed: 2,503
- Output location: `e:\projects\playqzv4\client\dist\`

---

## 📦 Files to Upload to Production

Since you're using the `/aiq3/` deployment path based on your previous work:

### Option 1: FTP Upload (Recommended for Quick Update)

**Upload only the frontend build:**

```
Local:  e:\projects\playqzv4\client\dist\*
Remote: /var/www/aiquiz.vibeai.cv/aiq3/
```

**Files to upload:**
- `index.html`
- `assets/` folder (contains the updated JS bundle)
- Any other static assets

### Option 2: Using FileZilla

1. **Connect to server:**
   - Host: `aiquiz.vibeai.cv`
   - Username: [your FTP username]
   - Password: [your FTP password]
   - Port: 22 (SFTP)

2. **Navigate to:**
   - Server path: `/var/www/aiquiz.vibeai.cv/aiq3/`

3. **Upload:**
   - Select all files from `e:\projects\playqzv4\client\dist\`
   - Drag & drop to server `/aiq3/` directory
   - Overwrite existing files when prompted

### Option 3: Using Git (if you have SSH access)

```bash
# On production server
cd /var/www/aiquiz.vibeai.cv/aiq3
git pull origin main
cd client
npm install
npm run build
cp -r dist/* ../
```

---

## ✅ Testing After Deployment

1. **Visit**: `https://aiquiz.vibeai.cv/aiq3/admin/media`

2. **Verify**:
   - Page should not flicker every minute
   - Auto-refresh happens every 5 minutes instead
   - Manual "Refresh" button works immediately

3. **Browser Cache**:
   - Clear browser cache (Ctrl + Shift + Delete)
   - Or hard reload (Ctrl + F5)

---

## 🔍 Verification Checklist

After upload:

- [ ] Clear browser cache
- [ ] Visit `/aiq3/admin/media` page
- [ ] Observe page for 1-2 minutes - should NOT refresh
- [ ] Click manual "Refresh" button - should work immediately
- [ ] Wait 5 minutes - page should auto-refresh
- [ ] Upload a new image - should work normally

---

## 📋 Quick Upload Summary

**What to upload:**
```
Source: e:\projects\playqzv4\client\dist\*
Target: server:/var/www/aiquiz.vibeai.cv/aiq3/
Method: FTP/SFTP
```

**Key files:**
- `index.html` - Main HTML
- `assets/index-*.js` - Contains the Media.tsx fix
- `assets/index-*.css` - Styles

---

## 🚨 Rollback Plan (if needed)

If something goes wrong:

1. Keep a backup of current `/aiq3/` files before uploading
2. Simply re-upload the backup files
3. Or revert the code change and rebuild:
   ```bash
   cd e:\projects\playqzv4\client
   # Change 300000 back to 60000 in src/pages/admin/Media.tsx
   npm run build
   ```

---

## 📝 Notes

- **No backend changes** - Only frontend updated
- **No database changes** - Database remains unchanged
- **Safe to deploy** - Only affects media page refresh timing
- **User impact** - Minimal; improves user experience

---

**Deployment prepared by**: Antigravity AI  
**Ready to deploy**: ✅ Yes  
**Risk level**: 🟢 Low
