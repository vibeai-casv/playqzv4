# AI Quizzer v4 - Deployment Guide (/aiq4)

This package is pre-configured to run in the subdirectory `aiquiz.vibeai.cv/aiq4`.

## 📂 Package Contents
- `index.html`, `assets/`, `static files`: The compiled frontend.
- `api/`: The PHP backend engine.
- `.htaccess`: Server instructions for routing and security.
- `api/config.php.template`: Template for your server-side configuration.

## 🚀 Deployment Steps

### 1. Upload Files
Upload all contents of this `AIQ4_SUBDIRECTORY_PACKAGE` folder into your server's `/aiq4/` directory via FTP/File Manager.

### 2. Configure Database
1. Go to the `api/` folder on your server.
2. Rename `config.php.template` to `config.php`.
3. Open `config.php` and enter your production database credentials (`host`, `name`, `user`, `pass`).
4. Enter your **Gemini API Key** to enable AI question generation.

### 3. Permissions
Ensure the following folders are writable by the server (chmod 755 or 777):
- `aiq4/api/uploads/`
- `aiq4/api/bundle/temp/` (if it exists)

### 4. Verify Routing
The `.htaccess` file is pre-set with `RewriteBase /aiq4/`. This ensures that refreshing the page or navigating directly to `/aiq4/admin` works correctly.

## 🔑 Default Super Admin
- **URL**: `https://aiquiz.vibeai.cv/aiq4/`
- **Email**: `vibeaicasv@gmail.com`
- **Password**: `password` (Please change this immediately in the Profile section).

---
*Built with Antigravity AI*
