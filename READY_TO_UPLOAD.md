# 🚀 Production Upload Guide

## ✅ Build Successful!

The frontend has been successfully rebuilt. You are now ready to upload the files to your production server.

---

## 📂 Files to Upload

### 1. Frontend (The Rebuilt App)

Upload the contents of the `dist` folder to your server's public root (usually `/public_html/`).

- **Source:** `e:\projects\playqzv4\client\dist\*` (All files and folders inside `dist`)
- **Destination:** `/public_html/`
- **Action:** Overwrite all existing files.

### 2. Backend Diagnostics (If not already uploaded)

Upload the session diagnostic tool to help debug login issues.

- **Source:** `e:\projects\playqzv4\fix\test-session.php`
- **Destination:** `/public_html/fix/test-session.php`

---

## 🛠️ How to Upload using FileZilla

1.  **Open FileZilla** and connect to `ftp.aiquiz.vibeai.cv`.
2.  **Navigate Local Site** (Left side) to: `e:\projects\playqzv4\client\dist`.
3.  **Navigate Remote Site** (Right side) to: `/public_html`.
4.  **Select All** files on the left (`Ctrl+A`).
5.  **Drag and Drop** them to the right side.
6.  **Confirm Overwrite** if prompted (Select "Apply to current queue only" or "Always").

---

## 🧪 Testing After Upload

1.  **Clear Browser Cache:** Press `Ctrl + Shift + Delete` in your browser to ensure you load the new version.
2.  **Login:** Go to `https://aiquiz.vibeai.cv` and log in as an admin.
3.  **Access Diagnostics:**
    *   Navigate to the new **Diagnostics** page in the Admin Dashboard sidebar.
    *   OR visit: `https://aiquiz.vibeai.cv/admin/diagnostics`
4.  **Run "Session & Auth" Test:** Verify it says you are logged in as Admin.
5.  **Try Import:** If the session test passes, try importing your JSON file again.

---

**Ready to deploy!** 🚀
