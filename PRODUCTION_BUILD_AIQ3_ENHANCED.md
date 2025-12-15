# Production Build - AIQ3 with Enhanced AI Question Generation

**Build Date:** December 14, 2025  
**Version:** AIQ3 Enhanced  
**Base Path:** `/aiq3/`

---

## 🎉 What's New in This Build

### ✨ Enhanced Question Generation System
- **Multi-Provider Support:** 7 AI providers now supported
  - Google Gemini (FREE)
  - OpenRouter (FREE models available)
  - Groq (FREE, ultra-fast)
  - OpenAI (GPT models)
  - Anthropic (Claude models)
  - Hugging Face (FREE, open-source)
  - Together AI ($25 FREE credits)

### 🔒 Security Improvements
- ✅ `config.php` added to `.gitignore`
- ✅ API keys now protected from version control
- ✅ Better error handling for API calls

### 🚀 Performance Improvements
- ✅ Enhanced JSON parsing with markdown cleanup
- ✅ Better validation before database insertion
- ✅ Duplicate tracking and reporting
- ✅ 60-second timeout for API calls
- ✅ Improved error messages with context

---

## 📦 Build Statistics

```
✓ TypeScript compilation successful
✓ Vite production build completed in 5.93s

Total Production Assets:
- CSS Files: 2 (7.47 kB total, gzipped)
- JavaScript Chunks: 25+ optimized files
- Largest chunk: charts (377.50 kB → 110.92 kB gzipped)
- Total size optimized with tree-shaking and minification
```

### Key Asset Sizes:
| File | Original | Gzipped | Description |
|------|----------|---------|-------------|
| charts-*.js | 377.50 kB | 110.92 kB | Chart components |
| index-*.js | 247.46 kB | 79.96 kB | Main application |
| schemas-*.js | 82.03 kB | 24.67 kB | Validation schemas |
| ui-*.js | 78.41 kB | 24.27 kB | UI components |

---

## 📁 Production Package Contents

### Frontend (client/dist/)
```
dist/
├── index.html              # Main HTML entry point
├── assets/                 # Optimized JS/CSS bundles
│   ├── *.js               # Code-split JavaScript chunks
│   └── *.css              # Optimized stylesheets
├── aiq3.png               # App logo
├── favicon.png            # Browser favicon
├── *.mp4                  # Demo videos
└── uploads/               # User upload directory
```

### Backend (api/)
```
api/
├── config.php             # ⚠️ CONFIGURE THIS (API keys, DB)
├── db.php                 # Database connection
├── utils.php              # Utility functions
├── admin/
│   ├── generate_questions.php  # 🆕 Enhanced AI generation
│   ├── import_bundle.php       # Bundle import
│   ├── export_bundle.php       # Bundle export
│   └── ... (other admin endpoints)
├── questions/
├── metadata/
└── sessions/
```

---

## 🚀 Deployment Instructions

### Step 1: Prepare Configuration

1. **Copy the frontend build:**
   ```bash
   # On server: /var/www/html/aiq3/ or C:\xampp\htdocs\aiq3\
   cp -r client/dist/* /path/to/server/aiq3/
   ```

2. **Copy the backend API:**
   ```bash
   cp -r api/ /path/to/server/aiq3/api/
   ```

3. **Configure API settings:**
   ```bash
   cd /path/to/server/aiq3/api/
   cp config.php.template config.php  # If template exists
   # Edit config.php with your settings
   ```

### Step 2: Configure config.php

Edit `api/config.php` with your production settings:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'aiqz');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// AI Configuration (Choose ONE provider)
// Option 1: Google Gemini (FREE - Recommended)
define('AI_PROVIDER', 'gemini');
define('AI_API_KEY', 'AIzaSy...your-gemini-key');
define('AI_MODEL', 'gemini-1.5-flash');

// Option 2: OpenRouter (FREE models available)
// define('AI_PROVIDER', 'openrouter');
// define('AI_API_KEY', 'sk-or-v1-...your-key');
// define('AI_MODEL', 'google/gemini-2.0-flash-exp:free');

// Option 3: Groq (FREE, ultra-fast)
// define('AI_PROVIDER', 'groq');
// define('AI_API_KEY', 'gsk_...your-key');
// define('AI_MODEL', 'llama-3.3-70b-versatile');

// Security
define('JWT_SECRET', 'CHANGE_THIS_TO_RANDOM_STRING_' . bin2hex(random_bytes(32)));
define('SESSION_TIMEOUT', 3600); // 1 hour

// API Settings
define('CORS_ORIGIN', 'https://yourdomain.com');
?>
```

### Step 3: Set Permissions

```bash
# Set proper permissions
chmod 755 /path/to/server/aiq3/
chmod 755 /path/to/server/aiq3/api/

# Make uploads writable
chmod 777 /path/to/server/aiq3/uploads/
chmod 777 /path/to/server/aiq3/uploads/logo/
chmod 777 /path/to/server/aiq3/uploads/personality/

# Protect config.php
chmod 600 /path/to/server/aiq3/api/config.php
```

### Step 4: Database Setup

```sql
-- Import the schema if not already done
SOURCE /path/to/schema.sql;

-- Or via command line:
mysql -u root -p aiqz < schema.sql
```

### Step 5: Configure .htaccess (Optional)

Create `.htaccess` in `/aiq3/` for clean URLs:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /aiq3/
  
  # Redirect API requests
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_URI} ^/aiq3/api/
  RewriteRule . /aiq3/api/index.php [L]
  
  # Handle client-side routing
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /aiq3/index.html [L]
</IfModule>
```

---

## 🔑 Getting AI API Keys

See the comprehensive guide: **`AI_PROVIDER_SETUP_GUIDE.md`**

**Quick Start (FREE):**

1. **Google Gemini** (Recommended):
   - Visit: https://aistudio.google.com/
   - Sign in with Google
   - Click "Get API Key" → "Create API Key"
   - Copy key (starts with `AIza...`)

2. **OpenRouter** (Multiple models):
   - Visit: https://openrouter.ai/
   - Sign up
   - Go to https://openrouter.ai/keys
   - Create key (starts with `sk-or-v1-...`)
   - Use free models like `google/gemini-2.0-flash-exp:free`

3. **Groq** (Ultra-fast):
   - Visit: https://console.groq.com/
   - Sign up
   - Go to https://console.groq.com/keys
   - Create key (starts with `gsk_...`)

---

## 🧪 Testing the Deployment

### 1. Test Frontend
```bash
# Visit in browser:
https://yourdomain.com/aiq3/

# Should see the landing page
```

### 2. Test API
```bash
# Test database connection:
curl https://yourdomain.com/aiq3/api/test.php

# Should return: {"status":"ok","database":"connected"}
```

### 3. Test AI Question Generation
1. Login to admin panel
2. Go to Question Bank
3. Click "Generate with AI"
4. Enter topic, select settings
5. Click Generate
6. Should see: "Successfully generated X questions as drafts"

---

## 📊 Monitoring Production

### Check API Usage
- **Gemini:** https://aistudio.google.com/app/apikey
- **OpenRouter:** https://openrouter.ai/activity
- **Groq:** https://console.groq.com/usage

### Check Application Logs
```bash
# PHP error logs
tail -f /var/log/apache2/error.log

# Check database
mysql -u root -p
USE aiqz;
SELECT COUNT(*) FROM questions WHERE ai_generated = 1;
```

---

## 🔧 Troubleshooting

### Frontend Issues

**Issue:** Blank page or 404 errors
- ✅ Check that `index.html` is in `/aiq3/` directory
- ✅ Verify base path in `vite.config.ts` is set to `/aiq3/`
- ✅ Check browser console for errors

**Issue:** Assets not loading
- ✅ Check `assets/` directory exists and is readable
- ✅ Verify server is serving static files correctly

### API Issues

**Issue:** "config.php not found"
- ✅ Ensure `config.php` exists in `api/` directory
- ✅ Check file permissions (should be readable by web server)

**Issue:** "Database connection failed"
- ✅ Verify MySQL is running
- ✅ Check DB credentials in `config.php`
- ✅ Ensure database `aiqz` exists

**Issue:** "AI API Key is not configured"
- ✅ Check `AI_API_KEY` is defined in `config.php`
- ✅ Verify key is valid (no extra spaces)
- ✅ Test key at provider's dashboard

**Issue:** "Quota exceeded"
- ✅ Check usage at provider dashboard
- ✅ Wait for quota reset (usually daily)
- ✅ Switch to different provider
- ✅ Upgrade to paid tier

---

## 🎯 Production Checklist

- [ ] Build completed successfully (`npm run build`)
- [ ] Frontend files copied to server
- [ ] Backend API files copied to server
- [ ] `config.php` created and configured
- [ ] Database connection working
- [ ] AI API key configured and tested
- [ ] File permissions set correctly
- [ ] `.htaccess` configured (if using Apache)
- [ ] Uploads directory writable
- [ ] Test question generation
- [ ] Test admin login
- [ ] Test quiz functionality
- [ ] Set up monitoring/logging
- [ ] Configure backups
- [ ] SSL certificate installed (HTTPS)

---

## 🔐 Security Reminders

1. ✅ **Never commit `config.php`** - Already in `.gitignore`
2. ✅ **Use strong JWT secret** - Random 64+ character string
3. ✅ **Secure file permissions** - `config.php` should be 600
4. ✅ **Enable HTTPS** - Use SSL certificate
5. ✅ **Monitor API usage** - Set up billing alerts
6. ✅ **Regular backups** - Database and uploaded files
7. ✅ **Keep dependencies updated** - Run `npm audit` regularly

---

## 📚 Additional Documentation

- **AI Provider Setup:** `AI_PROVIDER_SETUP_GUIDE.md`
- **Database Schema:** `schema.sql`
- **API Documentation:** `README_API.md`
- **Import/Export Guide:** `IMPORT_EXPORT_GUIDE.txt`

---

## 🆘 Support

If you encounter issues:

1. Check the troubleshooting section above
2. Review error logs (PHP and JavaScript console)
3. Verify all configuration settings
4. Test API endpoints individually
5. Check provider dashboard for API issues

---

## 🎉 Deployment Complete!

Your production build is ready with:
- ✅ Enhanced AI question generation (7 providers)
- ✅ Optimized frontend bundle (gzipped assets)
- ✅ Secure API configuration
- ✅ Complete admin features
- ✅ Import/Export functionality
- ✅ Bundle management

**Production URL:** `https://yourdomain.com/aiq3/`

Happy deploying! 🚀
