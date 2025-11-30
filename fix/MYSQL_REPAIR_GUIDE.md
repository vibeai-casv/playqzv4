# 🔧 MySQL Corruption Fix Guide

## Error: #1034 - Index for table 'db' is corrupt

This guide will help you repair the corrupted MySQL system table.

---

## ⚡ Quick Fix (Recommended)

### Option 1: Using PowerShell Script (Automated)

```powershell
cd e:\projects\playqzv4\fix
.\repair-mysql.ps1
```

This script will:
1. ✅ Stop MySQL service
2. ✅ Create backup
3. ✅ Repair corrupted tables using myisamchk
4. ✅ Restart MySQL service

---

### Option 2: Using SQL Commands (If MySQL is running)

1. **Open MySQL Command Line** or **phpMyAdmin**

2. **Run these commands**:
```sql
USE mysql;
REPAIR TABLE db;
FLUSH PRIVILEGES;
```

3. **Verify the repair**:
```sql
CHECK TABLE db;
```

---

## 🛠️ Manual Repair Steps

### Method 1: Using myisamchk (MySQL Stopped)

1. **Stop MySQL Service**:
```powershell
# Find MySQL service
Get-Service -Name "MySQL*"

# Stop it
Stop-Service -Name "MySQL" -Force
```

2. **Navigate to MySQL bin directory**:
```powershell
cd T:\server\mysql\bin
```

3. **Repair the table**:
```powershell
.\myisamchk.exe -r -f T:\server\mysql\data\mysql\db.MYI
```

4. **If that doesn't work, try recovery**:
```powershell
.\myisamchk.exe --recover --force T:\server\mysql\data\mysql\db.MYI
```

5. **Start MySQL Service**:
```powershell
Start-Service -Name "MySQL"
```

---

### Method 2: Using MySQL REPAIR TABLE (MySQL Running)

1. **Connect to MySQL**:
```powershell
cd T:\server\mysql\bin
.\mysql.exe -u root -p
```

2. **Run repair commands**:
```sql
USE mysql;
REPAIR TABLE db;
CHECK TABLE db;
FLUSH PRIVILEGES;
```

3. **Exit**:
```sql
EXIT;
```

---

## 📋 Troubleshooting

### Issue: "Table is marked as crashed"

**Solution**:
```sql
USE mysql;
REPAIR TABLE db USE_FRM;
FLUSH PRIVILEGES;
```

### Issue: "Can't open file"

**Solution**:
1. Check file permissions
2. Ensure MySQL service is stopped
3. Run myisamchk as administrator

### Issue: Repair fails

**Solution 1 - Recreate from backup**:
```sql
-- If you have a backup
DROP TABLE db;
CREATE TABLE db LIKE db_backup;
INSERT INTO db SELECT * FROM db_backup;
```

**Solution 2 - Reinstall system tables**:
```powershell
cd T:\server\mysql\bin
.\mysql_install_db.exe
```

---

## 🔍 Verification

After repair, verify everything works:

```sql
-- Check table status
SHOW TABLE STATUS FROM mysql WHERE Name = 'db';

-- Count records
SELECT COUNT(*) FROM mysql.db;

-- Show all tables
SHOW TABLES FROM mysql;

-- Test privileges
SHOW GRANTS;
```

---

## 💾 Backup Before Repair

**Always backup before repairing**:

```powershell
# Create backup directory
$backupDir = "T:\server\mysql\backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Path $backupDir

# Copy mysql database
Copy-Item -Path "T:\server\mysql\data\mysql" -Destination "$backupDir\mysql" -Recurse
```

---

## 🚀 Quick Commands Reference

### Stop MySQL
```powershell
Stop-Service -Name "MySQL" -Force
```

### Repair Table (MySQL stopped)
```powershell
cd T:\server\mysql\bin
.\myisamchk.exe -r -f ..\data\mysql\db.MYI
```

### Repair Table (MySQL running)
```sql
USE mysql;
REPAIR TABLE db;
FLUSH PRIVILEGES;
```

### Start MySQL
```powershell
Start-Service -Name "MySQL"
```

### Check Table
```sql
CHECK TABLE mysql.db;
```

---

## 📁 File Locations

- **MySQL Root**: `T:\server\mysql`
- **MySQL Bin**: `T:\server\mysql\bin`
- **MySQL Data**: `T:\server\mysql\data`
- **Corrupted Table**: `T:\server\mysql\data\mysql\db.MYI`
- **Repair Script**: `e:\projects\playqzv4\fix\repair-mysql.ps1`
- **SQL Script**: `e:\projects\playqzv4\fix\repair-mysql.sql`

---

## ⚠️ Important Notes

1. **Always backup** before attempting repairs
2. **Stop MySQL** before using myisamchk
3. **Don't use myisamchk** while MySQL is running
4. **Use REPAIR TABLE** if MySQL is running
5. **Flush privileges** after repair

---

## 🎯 Recommended Approach

1. ✅ **Try SQL REPAIR first** (if MySQL is running)
2. ✅ **Use PowerShell script** (automated)
3. ✅ **Manual myisamchk** (if above fails)
4. ✅ **Restore from backup** (last resort)

---

## 📞 Common Errors

### Error: "Can't find file"
- Check path: `T:\server\mysql\data\mysql\db.MYI`
- Verify MySQL installation

### Error: "Permission denied"
- Run as Administrator
- Check file permissions

### Error: "Table is still marked as crashed"
- Use `REPAIR TABLE db USE_FRM;`
- Try `--recover --force` with myisamchk

---

## ✅ Success Indicators

After successful repair:
- ✅ No errors when accessing mysql.db table
- ✅ `CHECK TABLE db;` returns "OK"
- ✅ Can view database privileges
- ✅ MySQL starts without errors

---

**Ready to fix? Run the PowerShell script or use the SQL commands above!**
