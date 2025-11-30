# 🔧 Diagnostic Tools Directory

This directory contains comprehensive diagnostic and testing tools for the PlayQz AI Quiz Platform.

---

## 📋 Available Tools

### 1. **Import Diagnostic** (`import-diagnostic.php`)
Tests JSON import functionality including:
- PHP version compatibility
- Database connection
- Questions table structure
- Required dependencies
- File permissions

**Usage**: `https://aiquiz.vibeai.cv/fix/import-diagnostic.php`

---

### 2. **Database Connection Test** (`test-database.php`)
Verifies database connectivity and structure:
- Connection status
- Database credentials
- Table listing
- Required tables check
- Questions count

**Usage**: `https://aiquiz.vibeai.cv/fix/test-database.php`

---

### 3. **Server Environment Test** (`test-server-env.php`)
Checks server configuration:
- PHP version and settings
- Installed extensions
- Memory limits
- File permissions
- Path information

**Usage**: `https://aiquiz.vibeai.cv/fix/test-server-env.php`

---

### 4. **API Endpoints Test** (`test-api-endpoints.php`)
Tests all API endpoints:
- File existence
- Readability
- Permissions
- Last modified dates

**Usage**: `https://aiquiz.vibeai.cv/fix/test-api-endpoints.php`

---

### 5. **Login Test** (`test-login.php`)
Tests authentication system:
- Session management
- Login functionality
- User verification

**Usage**: `https://aiquiz.vibeai.cv/fix/test-login.php`

---

### 6. **General Diagnostic** (`diagnostic.php`)
Overall system health check:
- Configuration status
- Database connectivity
- File structure

**Usage**: `https://aiquiz.vibeai.cv/fix/diagnostic.php`

---

## 🚀 Quick Start

### Local Testing

1. **Start dev server**:
   ```bash
   npm run dev
   ```

2. **Visit diagnostic dashboard**:
   ```
   http://localhost/fix/
   ```

3. **Run individual tests**:
   ```
   http://localhost/fix/import-diagnostic.php
   http://localhost/fix/test-database.php
   http://localhost/fix/test-server-env.php
   ```

---

### Production Testing

1. **Upload fix directory** to server via FTP:
   ```
   Local:  e:\projects\playqzv4\fix\*
   Remote: /public_html/fix/
   ```

2. **Visit diagnostic dashboard**:
   ```
   https://aiquiz.vibeai.cv/fix/
   ```

3. **Run tests** and check results

4. **Remove or restrict access** after troubleshooting

---

## 🔍 Interpreting Results

### Success Indicators

All tests should show:
- ✅ `"php_version_ok": true`
- ✅ `"config_loaded": true`
- ✅ `"db_connected": true`
- ✅ `"questions_table_exists": true`
- ✅ `"overall_status": "HEALTHY"`

### Common Issues

#### PHP Version Too Old
```json
{
  "php_version_ok": false,
  "php_version": "5.6.40"
}
```
**Fix**: Upgrade PHP to 7.0 or higher

#### Database Connection Failed
```json
{
  "db_connected": false,
  "db_error": "Access denied for user..."
}
```
**Fix**: Check `config.php` credentials

#### Missing Tables
```json
{
  "questions_table_exists": false
}
```
**Fix**: Import `schema.sql`

#### Missing Extensions
```json
{
  "extensions": {
    "pdo_mysql": false
  }
}
```
**Fix**: Enable PDO MySQL extension

---

## 🛡️ Security Notes

### ⚠️ Important

These diagnostic tools expose system information and should be:

1. **Removed** from production after troubleshooting
2. **Password protected** if kept on server
3. **Not indexed** by search engines

### Protect with .htaccess

Create `/fix/.htaccess`:
```apache
AuthType Basic
AuthName "Diagnostic Tools"
AuthUserFile /path/to/.htpasswd
Require valid-user
```

Or restrict by IP:
```apache
Order Deny,Allow
Deny from all
Allow from YOUR.IP.ADDRESS
```

---

## 📊 Output Format

All diagnostic tools return JSON for easy parsing:

```json
{
  "timestamp": "2025-11-30 09:00:00",
  "test_name": "Import Diagnostic",
  "php_version": "7.4.33",
  "php_version_ok": true,
  "config_loaded": true,
  "db_connected": true,
  "ready_for_import": true
}
```

---

## 🔧 Troubleshooting Workflow

### For Import Issues:

1. **Run Import Diagnostic**
   ```
   /fix/import-diagnostic.php
   ```

2. **Check specific failures**:
   - If `config_loaded: false` → Check `api/config.php`
   - If `db_connected: false` → Run Database Test
   - If `questions_table_exists: false` → Import schema
   - If `verifyAuth_exists: false` → Check `api/utils.php`

3. **Run Server Environment Test** if PHP issues

4. **Run API Endpoints Test** if file not found errors

---

## 📁 File Structure

```
fix/
├── index.html                  # Diagnostic dashboard
├── import-diagnostic.php       # Import functionality test
├── test-database.php          # Database connection test
├── test-server-env.php        # Server environment test
├── test-api-endpoints.php     # API endpoints test
├── test-login.php             # Authentication test
├── diagnostic.php             # General diagnostic
├── repair-mysql.ps1           # MySQL repair script
├── repair-aria.ps1            # Aria table repair script
├── repair-mysql.sql           # SQL repair commands
├── repair-aria.sql            # Aria SQL repair
└── README.md                  # This file
```

---

## 🆘 Getting Help

If diagnostic tools show errors:

1. **Copy the JSON output**
2. **Check the specific error message**
3. **Refer to troubleshooting guides**:
   - `FIX_500_ERROR.md`
   - `JSON_IMPORT_DEBUG.md`
   - `MYSQL_REPAIR_GUIDE.md`
   - `ARIA_REPAIR_GUIDE.md`

---

## 🎯 Quick Commands

### Upload to Production
```bash
# Via FTP
Local:  e:\projects\playqzv4\fix\*
Remote: /public_html/fix/
```

### Test Locally
```bash
http://localhost/fix/
```

### Test Production
```bash
https://aiquiz.vibeai.cv/fix/
```

### Remove from Production
```bash
# Delete via FTP or cPanel File Manager
/public_html/fix/
```

---

**Use these tools to quickly diagnose and fix issues!** 🚀
