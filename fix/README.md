# Fix Directory - Schema Update Tools

## 📁 Files in This Directory

### `update_schema.php` ⭐
**Automatic schema update script**

- Adds `is_demo` column to questions table
- Creates performance indexes
- Validates database structure
- Safe to run multiple times

**Usage:**
```bash
# Via browser
http://localhost:8000/fix/update_schema.php

# Via CLI
php fix/update_schema.php
```

### `SCHEMA_UPDATE_GUIDE.md` 📖
**Complete usage documentation**

- Detailed instructions
- Expected output examples
- Troubleshooting guide
- Security best practices

---

## 🚀 Quick Start

### For Existing Database (Update)

1. Run the update script:
   ```
   https://aiquiz.vibeai.cv/fix/update_schema.php
   ```

2. Verify changes were applied (look for ✓ success messages)

3. **Delete this directory** for security after update

### For Fresh Installation

**No action needed!** Import `schema.sql` instead.

---

## ⚠️ Security Warning

**DELETE THIS DIRECTORY AFTER USE!**

This directory contains database update scripts that should not remain on a production server after deployment.

**After successful update:**
```bash
rm -rf fix/
```

---

## 📊 What Changes Are Made?

```sql
ALTER TABLE questions ADD COLUMN is_demo TINYINT(1) DEFAULT 0;
ALTER TABLE questions ADD INDEX idx_is_demo (is_demo);
```

---

See `SCHEMA_UPDATE_GUIDE.md` for complete documentation.
