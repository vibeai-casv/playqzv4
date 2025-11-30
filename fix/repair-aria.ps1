# Aria Table Repair Script for MySQL/MariaDB
# Fixes error #1030 - Got error 176 "Read page with wrong checksum" from storage engine Aria

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Aria Table Repair Tool" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$MYSQL_PATH = "T:\server\mysql"
$MYSQL_BIN = "$MYSQL_PATH\bin"
$MYSQL_DATA = "$MYSQL_PATH\data\mysql"

Write-Host "[INFO] MySQL Location: $MYSQL_PATH" -ForegroundColor Cyan
Write-Host "[INFO] Target: mysql.db table (Aria engine)" -ForegroundColor Cyan
Write-Host ""

# Check if aria_chk exists
$ariaChk = "$MYSQL_BIN\aria_chk.exe"

if (-not (Test-Path $ariaChk)) {
    Write-Host "[ERROR] aria_chk.exe not found at: $ariaChk" -ForegroundColor Red
    Write-Host "[INFO] Trying mariadb-check instead..." -ForegroundColor Yellow
    $ariaChk = "$MYSQL_BIN\mariadb-check.exe"
}

if (-not (Test-Path $ariaChk)) {
    Write-Host "[ERROR] Aria repair tool not found!" -ForegroundColor Red
    Write-Host "[INFO] Please use SQL method instead (see below)" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "SQL Method:" -ForegroundColor Cyan
    Write-Host "  1. Start MySQL/MariaDB" -ForegroundColor White
    Write-Host "  2. Run: USE mysql;" -ForegroundColor White
    Write-Host "  3. Run: REPAIR TABLE db USE_FRM;" -ForegroundColor White
    Write-Host "  4. Run: FLUSH PRIVILEGES;" -ForegroundColor White
    Write-Host ""
    exit 1
}

Write-Host "[OK] Found Aria repair tool: $ariaChk" -ForegroundColor Green
Write-Host ""

# Step 1: Ensure MySQL is stopped
Write-Host "[INFO] Step 1: Checking MySQL status..." -ForegroundColor Cyan

$mysqlService = Get-Service -Name "*mysql*","*mariadb*" -ErrorAction SilentlyContinue | Where-Object { $_.Status -eq 'Running' }

if ($mysqlService) {
    Write-Host "[WARNING] MySQL/MariaDB is running: $($mysqlService.Name)" -ForegroundColor Yellow
    Write-Host "[INFO] Aria repair requires MySQL to be stopped." -ForegroundColor Yellow
    
    $stop = Read-Host "Stop MySQL service now? (y/n)"
    if ($stop -eq 'y') {
        Stop-Service -Name $mysqlService.Name -Force
        Start-Sleep -Seconds 3
        Write-Host "[OK] MySQL service stopped" -ForegroundColor Green
    } else {
        Write-Host "[INFO] Please stop MySQL manually and run this script again." -ForegroundColor Yellow
        Write-Host "[INFO] Or use SQL method: REPAIR TABLE mysql.db USE_FRM;" -ForegroundColor Cyan
        exit 0
    }
} else {
    Write-Host "[OK] MySQL is not running" -ForegroundColor Green
}

Write-Host ""

# Step 2: Create backup
Write-Host "[INFO] Step 2: Creating backup..." -ForegroundColor Cyan

$backupDir = "$MYSQL_PATH\backup_aria_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

# Backup db table files
$dbFiles = Get-ChildItem -Path $MYSQL_DATA -Filter "db.*"
foreach ($file in $dbFiles) {
    Copy-Item -Path $file.FullName -Destination $backupDir -Force
    Write-Host "[INFO] Backed up: $($file.Name)" -ForegroundColor Cyan
}

Write-Host "[OK] Backup created at: $backupDir" -ForegroundColor Green
Write-Host ""

# Step 3: Repair Aria table
Write-Host "[INFO] Step 3: Repairing Aria table..." -ForegroundColor Cyan

$dbTablePath = "$MYSQL_DATA\db"

if (Test-Path "$dbTablePath.MAI") {
    Write-Host "[INFO] Found Aria table files:" -ForegroundColor Cyan
    Write-Host "  - db.MAI (index)" -ForegroundColor White
    Write-Host "  - db.MAD (data)" -ForegroundColor White
    Write-Host "  - db.frm (structure)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "[INFO] Running aria_chk --repair..." -ForegroundColor Yellow
    
    # Repair the table
    & $ariaChk --repair --force $dbTablePath
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[OK] Table repaired successfully!" -ForegroundColor Green
    } else {
        Write-Host "[WARNING] Repair completed with warnings. Trying recovery..." -ForegroundColor Yellow
        
        # Try recovery mode
        & $ariaChk --recover --force $dbTablePath
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "[OK] Table recovered successfully!" -ForegroundColor Green
        } else {
            Write-Host "[WARNING] Recovery had issues. Trying safe recovery..." -ForegroundColor Yellow
            
            # Try safe recovery
            & $ariaChk --safe-recover --force $dbTablePath
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host "[OK] Safe recovery successful!" -ForegroundColor Green
            } else {
                Write-Host "[ERROR] All repair attempts failed." -ForegroundColor Red
                Write-Host "[INFO] You may need to restore from backup or recreate the table." -ForegroundColor Yellow
            }
        }
    }
} else {
    Write-Host "[ERROR] Aria table files not found at: $dbTablePath" -ForegroundColor Red
    Write-Host "[INFO] Looking for table files..." -ForegroundColor Cyan
    
    Get-ChildItem -Path $MYSQL_DATA -Filter "db.*" | ForEach-Object {
        Write-Host "  Found: $($_.Name)" -ForegroundColor Yellow
    }
}

Write-Host ""

# Step 4: Check all Aria tables
Write-Host "[INFO] Step 4: Checking all Aria tables in mysql database..." -ForegroundColor Cyan

Get-ChildItem -Path $MYSQL_DATA -Filter "*.MAI" | ForEach-Object {
    $tableName = $_.BaseName
    $tablePath = "$MYSQL_DATA\$tableName"
    
    Write-Host "[INFO] Checking table: $tableName" -ForegroundColor Cyan
    
    & $ariaChk --check $tablePath
    
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[WARNING] Table $tableName has issues. Repairing..." -ForegroundColor Yellow
        & $ariaChk --repair --force $tablePath
    }
}

Write-Host "[OK] All Aria tables checked" -ForegroundColor Green
Write-Host ""

# Step 5: Start MySQL
Write-Host "[INFO] Step 5: Starting MySQL service..." -ForegroundColor Cyan

if ($mysqlService) {
    Start-Service -Name $mysqlService.Name
    Start-Sleep -Seconds 5
    
    $status = (Get-Service -Name $mysqlService.Name).Status
    if ($status -eq 'Running') {
        Write-Host "[OK] MySQL service started successfully" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] Failed to start MySQL service" -ForegroundColor Red
        Write-Host "[INFO] Please start manually" -ForegroundColor Yellow
    }
} else {
    Write-Host "[INFO] Please start MySQL manually" -ForegroundColor Yellow
}

Write-Host ""

# Summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Repair Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Backup location: $backupDir" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "  1. Start MySQL if not already running" -ForegroundColor White
Write-Host "  2. Connect to MySQL" -ForegroundColor White
Write-Host "  3. Run: USE mysql;" -ForegroundColor White
Write-Host "  4. Run: CHECK TABLE db;" -ForegroundColor White
Write-Host "  5. Run: SELECT COUNT(*) FROM db;" -ForegroundColor White
Write-Host ""
Write-Host "If issues persist, run these SQL commands:" -ForegroundColor Yellow
Write-Host "  USE mysql;" -ForegroundColor White
Write-Host "  REPAIR TABLE db USE_FRM;" -ForegroundColor White
Write-Host "  FLUSH PRIVILEGES;" -ForegroundColor White
Write-Host ""
Write-Host "Alternative: Restore from backup" -ForegroundColor Yellow
Write-Host "  Copy files from: $backupDir" -ForegroundColor White
Write-Host "  To: $MYSQL_DATA" -ForegroundColor White
Write-Host ""
