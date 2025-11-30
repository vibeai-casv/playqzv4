# 🔧 Aria Table Corruption Fix

## Error: #1030 - Got error 176 "Read page with wrong checksum" from storage engine Aria

Your MySQL/MariaDB is using **Aria storage engine**, not MyISAM. This requires different repair tools.

---

## ⚡ Quick Fix Options

### Option 1: SQL Repair (Easiest - MySQL Running)

1. **Start MySQL** (if not running)
2. **Open MySQL command line or phpMyAdmin**
3. **Run these commands**:

```sql
USE mysql;
REPAIR TABLE db USE_FRM;
FLUSH PRIVILEGES;
```

The `USE_FRM` option forces repair even with checksum errors.

---

### Option 2: Aria Repair Tool (MySQL Stopped)

```powershell
cd e:\projects\playqzv4\fix
.\repair-aria.ps1
```

This will:
- Stop MySQL
- Backup the table
- Use `aria_chk` to repair
- Restart MySQL

---

## 🛠️ Manual Repair Steps

### Method 1: Using aria_chk (Recommended for Aria)

1. **Stop MySQL**:
```powershell
Stop-Service -Name "MySQL*" -Force
```

2. **Navigate to MySQL bin**:
```powershell
cd T:\server\mysql\bin
```

3. **Repair the table**:
```powershell
.\aria_chk.exe --repair --force T:\server\mysql\data\mysql\db
```

4. **If that fails, try safe recovery**:
```powershell
.\aria_chk.exe --safe-recover --force T:\server\mysql\data\mysql\db
```

5. **Start MySQL**:
```powershell
Start-Service -Name "MySQL*"
```

---

### Method 2: SQL REPAIR TABLE USE_FRM

This is the **easiest method** if MySQL is running:

```sql
USE mysql;
REPAIR TABLE db USE_FRM;
CHECK TABLE db;
FLUSH PRIVILEGES;
```

---

## 📋 Understanding Aria Tables

Your table files:
- **db.MAI** - Aria index file (not .MYI)
- **db.MAD** - Aria data file (not .MYD)
- **db.frm** - Table structure

Aria is MariaDB's improved version of MyISAM, so you need `aria_chk` instead of `myisamchk`.

---

## 🔍 Verification

After repair:

```sql
-- Check table
CHECK TABLE mysql.db;

-- Should return: OK

-- Verify data
SELECT COUNT(*) FROM mysql.db;

-- Check table status
SHOW TABLE STATUS FROM mysql WHERE Name = 'db';

-- Test privileges
FLUSH PRIVILEGES;
SHOW GRANTS;
```

---

## 🆘 If Repair Fails

### Option A: Recreate from system defaults

```sql
-- This will recreate the db table with default structure
USE mysql;
DROP TABLE IF EXISTS db;
-- Then restart MySQL - it will recreate the table
```

### Option B: Restore from backup

```powershell
# Stop MySQL
Stop-Service -Name "MySQL*"

# Copy backup files
Copy-Item T:\server\mysql\backup_*\db.* T:\server\mysql\data\mysql\

# Start MySQL
Start-Service -Name "MySQL*"
```

---

## ⚠️ Important Notes

1. **USE_FRM is key** - This option bypasses checksum validation
2. **Aria ≠ MyISAM** - Use `aria_chk`, not `myisamchk`
3. **Stop MySQL** before using aria_chk
4. **Always backup** before repair

---

## 🎯 Recommended Steps (In Order)

1. ✅ **Try SQL first** (easiest):
   ```sql
   USE mysql;
   REPAIR TABLE db USE_FRM;
   FLUSH PRIVILEGES;
   ```

2. ✅ **If SQL fails**, run PowerShell script:
   ```powershell
   .\repair-aria.ps1
   ```

3. ✅ **If both fail**, manual aria_chk:
   ```powershell
   cd T:\server\mysql\bin
   .\aria_chk.exe --safe-recover --force ..\data\mysql\db
   ```

4. ✅ **Last resort**, restore from backup

---

## 📁 Files Created

- **repair-aria.ps1** - Automated Aria repair script
- **repair-aria.sql** - SQL repair commands
- **ARIA_REPAIR_GUIDE.md** - This guide

---

## 🚀 Quick Command

**Fastest fix** (if MySQL is running):

```sql
USE mysql; REPAIR TABLE db USE_FRM; FLUSH PRIVILEGES;
```

---

**Try the SQL method first - it's the easiest!** 🎯
