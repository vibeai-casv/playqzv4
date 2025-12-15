# Development Server Build & Deployment Guide

**Build Date:** December 14, 2025  
**Version:** AIQ3 Enhanced - Development Server Build  
**Base Path:** `/aiq3/`  
**Target:** Development/Staging Server

---

## ✅ Build Completed Successfully

```
✓ TypeScript compilation successful
✓ Vite production build completed in 6.05s
✓ All assets optimized and ready for deployment
✓ Build output: client/dist/
```

---

## 📦 What's Included

### 🆕 New Features in This Build:
- ✅ **Enhanced AI Question Generation** - 7 AI providers supported
- ✅ **Multi-Provider Support** - Gemini, OpenRouter, Groq, OpenAI, Anthropic, Hugging Face, Together AI
- ✅ **Improved Security** - config.php protected from version control
- ✅ **Better Error Handling** - Enhanced validation and error messages
- ✅ **Duplicate Tracking** - Skips duplicate questions during generation

### 📊 Build Statistics:
| Asset | Size | Gzipped | Optimization |
|-------|------|---------|--------------|
| Charts | 377.50 kB | 110.92 kB | 70.6% |
| Main App | 247.46 kB | 79.96 kB | 67.7% |
| Schemas | 82.03 kB | 24.67 kB | 69.9% |
| UI Components | 78.41 kB | 24.27 kB | 69.1% |

**Total optimization:** ~70% size reduction with gzip! 🎉

---

## 🚀 Quick Deployment to Development Server

### Option 1: Local Development Server (XAMPP/WAMP)

1. **Copy Frontend Build:**
   ```powershell
   # Copy to XAMPP htdocs
   xcopy /E /I /Y "client\dist" "C:\xampp\htdocs\aiq3"
   ```

2. **Copy Backend API:**
   ```powershell
   # Copy API files
   xcopy /E /I /Y "api" "C:\xampp\htdocs\aiq3\api"
   ```

3. **Configure API:**
   - Edit `C:\xampp\htdocs\aiq3\api\config.php`
   - Set database credentials
   - Add AI API key (see guide below)

4. **Access Application:**
   - URL: `http://localhost/aiq3/`
   - Admin Panel: `http://localhost/aiq3/admin`

### Option 2: Remote Development Server (FTP/SFTP)

1. **Prepare Upload Package:**
   ```powershell
   # Create deployment folder
   New-Item -ItemType Directory -Force -Path "deploy_dev"
   
   # Copy frontend
   Copy-Item -Recurse "client\dist\*" "deploy_dev\"
   
   # Copy backend
   Copy-Item -Recurse "api" "deploy_dev\api"
   
   # Copy documentation
   Copy-Item "AI_PROVIDER_SETUP_GUIDE.md" "deploy_dev\"
   ```

2. **Upload via FTP/FileZilla:**
   - Server: `your-dev-server.com`
   - Path: `/public_html/aiq3/`
   - Upload all files from `deploy_dev/`

3. **Set Permissions (via SSH if available):**
   ```bash
   # Connect to server
   ssh user@your-dev-server.com
   
   # Set permissions
   chmod 755 /path/to/aiq3/
   chmod 755 /path/to/aiq3/api/
   chmod 777 /path/to/aiq3/uploads/
   chmod 600 /path/to/aiq3/api/config.php
   ```

---

## ⚙️ Configuration for Development Server

### 1. Database Configuration

Edit `api/config.php`:

```php
<?php
// Development Server Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'aiqz');
define('DB_USER', 'root');  // Change for remote server
define('DB_PASS', '');      // Add password for remote server

// Development Mode (optional - enables detailed errors)
define('ENVIRONMENT', 'development');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
```

### 2. AI Provider Configuration

**Option A: Google Gemini (FREE - Recommended for Development)**

```php
// Google Gemini - Free and reliable
define('AI_PROVIDER', 'gemini');
define('AI_API_KEY', 'AIzaSy...your-gemini-key');
define('AI_MODEL', 'gemini-1.5-flash');

// Get your key: https://aistudio.google.com/
```

**Option B: OpenRouter (FREE models available)**

```php
// OpenRouter - Access to multiple models
define('AI_PROVIDER', 'openrouter');
define('AI_API_KEY', 'sk-or-v1-...your-key');
define('AI_MODEL', 'google/gemini-2.0-flash-exp:free');

// Get your key: https://openrouter.ai/keys
```

**Option C: Groq (FREE - Ultra Fast)**

```php
// Groq - Fastest inference
define('AI_PROVIDER', 'groq');
define('AI_API_KEY', 'gsk_...your-key');
define('AI_MODEL', 'llama-3.3-70b-versatile');

// Get your key: https://console.groq.com/keys
```

### 3. Security Configuration

```php
// JWT Secret (generate a random string)
define('JWT_SECRET', 'dev_secret_' . bin2hex(random_bytes(32)));

// Session timeout (1 hour)
define('SESSION_TIMEOUT', 3600);

// CORS (allow localhost for development)
define('CORS_ORIGIN', '*'); // Or specific: 'http://localhost:5173'
```

### 4. Complete config.php Template

```php
<?php
// =============================================================================
// DEVELOPMENT SERVER CONFIGURATION
// =============================================================================

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'aiqz');
define('DB_USER', 'root');
define('DB_PASS', '');

// AI Provider (Choose ONE)
define('AI_PROVIDER', 'gemini');  // Options: gemini, openrouter, groq, openai, anthropic, huggingface, together
define('AI_API_KEY', 'YOUR_API_KEY_HERE');
define('AI_MODEL', 'gemini-1.5-flash');

// Security
define('JWT_SECRET', 'CHANGE_THIS_' . bin2hex(random_bytes(32)));
define('SESSION_TIMEOUT', 3600);

// CORS
define('CORS_ORIGIN', '*');

// Development Mode
define('ENVIRONMENT', 'development');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Paths
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('BASE_URL', 'http://localhost/aiq3/');
?>
```

---

## 🔑 Getting Your AI API Key (Quick Setup)

### Fastest Option: Google Gemini (2 minutes setup)

1. **Visit:** https://aistudio.google.com/
2. **Sign in** with your Google account
3. **Click** "Get API Key" (top right)
4. **Click** "Create API Key"
5. **Select** "Create API key in new project"
6. **Copy** the key (starts with `AIza...`)
7. **Paste** into `config.php`:
   ```php
   define('AI_API_KEY', 'AIzaSy...your-key-here');
   ```

**Why Gemini?**
- ✅ 100% FREE
- ✅ No credit card required
- ✅ 1,500 requests per day
- ✅ High quality responses
- ✅ Perfect for development

For other providers, see: **`AI_PROVIDER_SETUP_GUIDE.md`**

---

## 📁 File Structure After Deployment

```
aiq3/                           # Your application root
├── index.html                  # Main entry point
├── assets/                     # Optimized JS/CSS bundles
│   ├── index-*.js             # Main application
│   ├── charts-*.js            # Chart components
│   ├── vendor-*.js            # React, Router, etc.
│   ├── ui-*.js                # UI components
│   └── *.css                  # Stylesheets
├── uploads/                    # User uploads
│   ├── logo/                  # Logo images
│   └── personality/           # Personality images
├── api/                        # Backend API
│   ├── config.php             # ⚠️ CONFIGURE THIS!
│   ├── db.php                 # Database connection
│   ├── utils.php              # Utility functions
│   ├── admin/                 # Admin endpoints
│   │   ├── generate_questions.php  # Enhanced AI generation
│   │   ├── import_bundle.php
│   │   └── export_bundle.php
│   ├── questions/             # Question endpoints
│   ├── metadata/              # Metadata endpoints
│   └── sessions/              # Session management
└── .htaccess                   # URL rewriting (optional)
```

---

## 🧪 Testing Your Deployment

### 1. Test Frontend Access

```
Open Browser: http://localhost/aiq3/
Expected: Landing page with "AI Quizzer" branding
```

### 2. Test API Connection

Create a test file: `api/test.php`

```php
<?php
require_once 'config.php';
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Test database
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
    $result = $stmt->fetch();
    
    echo json_encode([
        'status' => 'ok',
        'database' => 'connected',
        'questions_count' => $result['count'],
        'ai_provider' => AI_PROVIDER ?? 'not configured',
        'ai_key_exists' => !empty(AI_API_KEY)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
```

Visit: `http://localhost/aiq3/api/test.php`

Expected output:
```json
{
  "status": "ok",
  "database": "connected",
  "questions_count": 123,
  "ai_provider": "gemini",
  "ai_key_exists": true
}
```

### 3. Test AI Question Generation

1. Login to admin panel: `http://localhost/aiq3/admin`
2. Default credentials (if using development setup):
   - Username: `admin`
   - Password: `admin123`
3. Navigate to **Question Bank**
4. Click **"Generate with AI"** button
5. Enter:
   - Topic: "JavaScript"
   - Count: 3
   - Difficulty: Medium
6. Click **"Generate"**
7. Expected: "Successfully generated 3 questions as drafts"

---

## 🔧 Troubleshooting Development Server

### Issue: "Cannot connect to database"

**Solution:**
```powershell
# Start XAMPP MySQL
C:\xampp\xampp-control.exe

# Or via command line
net start mysql
```

Verify database exists:
```sql
mysql -u root -p
SHOW DATABASES;
USE aiqz;
SHOW TABLES;
```

### Issue: "config.php not found"

**Solution:**
```powershell
# Check if config.php exists
Test-Path "C:\xampp\htdocs\aiq3\api\config.php"

# If false, create it from template or manually
```

### Issue: "AI API Key is not configured"

**Solution:**
1. Open `api/config.php`
2. Verify this line exists:
   ```php
   define('AI_API_KEY', 'AIza...your-actual-key');
   ```
3. Ensure key has no extra spaces or quotes
4. Test key at provider's website

### Issue: "Uploads folder not writable"

**Solution:**
```powershell
# Give write permissions (Windows)
icacls "C:\xampp\htdocs\aiq3\uploads" /grant Users:F /T

# Or manually:
# Right-click uploads folder → Properties → Security
# Edit → Add "Users" group → Full Control
```

### Issue: Blank page / 404 errors

**Solution:**
1. Check browser console (F12)
2. Verify `index.html` exists in `/aiq3/`
3. Check Apache is running
4. Check `.htaccess` if using clean URLs

### Issue: "Quota exceeded" error

**Solution:**
- Check usage at provider dashboard
- Switch to different free provider temporarily
- Wait for quota reset (usually daily at midnight UTC)

---

## 📊 Development Server Monitoring

### Check Database

```sql
-- Login to MySQL
mysql -u root -p

USE aiqz;

-- Check questions
SELECT COUNT(*) as total, 
       SUM(ai_generated = 1) as ai_generated,
       SUM(is_active = 1) as active
FROM questions;

-- Check recent AI generated questions
SELECT id, question_text, category, created_at 
FROM questions 
WHERE ai_generated = 1 
ORDER BY created_at DESC 
LIMIT 10;
```

### Check API Usage

- **Gemini:** https://aistudio.google.com/app/apikey
- **OpenRouter:** https://openrouter.ai/activity
- **Groq:** https://console.groq.com/usage

### Check PHP Logs

```powershell
# XAMPP error log
Get-Content "C:\xampp\apache\logs\error.log" -Tail 50

# PHP error log
Get-Content "C:\xampp\php\logs\php_error_log" -Tail 50
```

---

## 🔄 Updating Your Development Server

When you make changes:

1. **Rebuild Frontend:**
   ```powershell
   cd client
   npm run build
   ```

2. **Copy Updated Files:**
   ```powershell
   # Update frontend only
   xcopy /E /I /Y "client\dist" "C:\xampp\htdocs\aiq3"
   
   # Or update API only
   xcopy /E /I /Y "api" "C:\xampp\htdocs\aiq3\api"
   ```

3. **Clear Browser Cache:**
   - Press `Ctrl + Shift + Delete`
   - Or hard reload: `Ctrl + F5`

---

## ✅ Development Server Checklist

- [ ] Build completed successfully
- [ ] Files copied to web server directory
- [ ] `config.php` created and configured
- [ ] Database connection working
- [ ] AI API key configured
- [ ] Uploads directory writable (777)
- [ ] Admin login works
- [ ] Question generation tested
- [ ] Import/Export tested
- [ ] No console errors in browser (F12)

---

## 🎯 Quick Start Commands

```powershell
# Build the application
cd client
npm run build

# Copy to XAMPP
xcopy /E /I /Y "dist" "C:\xampp\htdocs\aiq3"
xcopy /E /I /Y "..\api" "C:\xampp\htdocs\aiq3\api"

# Start XAMPP services
# Then visit: http://localhost/aiq3/
```

---

## 📚 Additional Resources

- **AI Setup Guide:** `AI_PROVIDER_SETUP_GUIDE.md`
- **Production Deployment:** `PRODUCTION_BUILD_AIQ3_ENHANCED.md`
- **Database Schema:** `schema.sql`
- **API Documentation:** `README_API.md`

---

## 🎉 Ready to Develop!

Your development server is ready with:
- ✅ Enhanced AI question generation (7 providers)
- ✅ Optimized production build
- ✅ Full admin features
- ✅ Import/Export functionality
- ✅ Complete documentation

**Development URL:** `http://localhost/aiq3/`  
**Admin Panel:** `http://localhost/aiq3/admin`

Happy coding! 🚀

---

## 🆘 Need Help?

1. Check troubleshooting section above
2. Review `AI_PROVIDER_SETUP_GUIDE.md`
3. Check browser console (F12) for errors
4. Verify all configuration in `config.php`
5. Test API endpoint: `http://localhost/aiq3/api/test.php`
