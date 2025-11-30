# MySQL Database Repair Script
# Fixes corruption error: #1034 - Index for table 'db' is corrupt

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "MySQL Database Repair Tool" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$MYSQL_PATH = "T:\server\mysql"
$MYSQL_BIN = "$MYSQL_PATH\bin"
$MYSQL_DATA = "$MYSQL_PATH\data"

Write-Host "[INFO] MySQL Location: $MYSQL_PATH" -ForegroundColor Cyan
Write-Host ""

# Check if MySQL path exists
if (-not (Test-Path $MYSQL_PATH)) {
    Write-Host "[ERROR] MySQL path not found: $MYSQL_PATH" -ForegroundColor Red
    exit 1
}

Write-Host "[OK] MySQL directory found" -ForegroundColor Green
Write-Host ""

# Step 1: Stop MySQL service
Write-Host "[INFO] Step 1: Stopping MySQL service..." -ForegroundColor Cyan

$mysqlService = Get-Service -Name "MySQL*" -ErrorAction SilentlyContinue

if ($mysqlService) {
    Write-Host "[INFO] Found MySQL service: $($mysqlService.Name)" -ForegroundColor Cyan
    
    if ($mysqlService.Status -eq 'Running') {
        Write-Host "[INFO] Stopping MySQL service..." -ForegroundColor Yellow
        Stop-Service -Name $mysqlService.Name -Force
        Start-Sleep -Seconds 3
        Write-Host "[OK] MySQL service stopped" -ForegroundColor Green
    } else {
        Write-Host "[INFO] MySQL service already stopped" -ForegroundColor Cyan
    }
} else {
    Write-Host "[WARNING] MySQL service not found. Assuming standalone installation." -ForegroundColor Yellow
    Write-Host "[INFO] Please ensure MySQL is not running before proceeding." -ForegroundColor Yellow
    $continue = Read-Host "Continue anyway? (y/n)"
    if ($continue -ne 'y') {
        exit 0
    }
}

Write-Host ""

# Step 2: Backup current database
Write-Host "[INFO] Step 2: Creating backup..." -ForegroundColor Cyan

$backupDir = "$MYSQL_PATH\backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

if (Test-Path "$MYSQL_DATA\mysql") {
    Write-Host "[INFO] Backing up mysql database..." -ForegroundColor Cyan
    Copy-Item -Path "$MYSQL_DATA\mysql" -Destination "$backupDir\mysql" -Recurse -Force
    Write-Host "[OK] Backup created at: $backupDir" -ForegroundColor Green
} else {
    Write-Host "[WARNING] mysql database directory not found at: $MYSQL_DATA\mysql" -ForegroundColor Yellow
}

Write-Host ""

# Step 3: Repair using myisamchk
Write-Host "[INFO] Step 3: Repairing corrupted tables..." -ForegroundColor Cyan

$myisamchk = "$MYSQL_BIN\myisamchk.exe"

if (Test-Path $myisamchk) {
    Write-Host "[INFO] Found myisamchk tool" -ForegroundColor Cyan
    
    # Repair the 'db' table
    $dbTablePath = "$MYSQL_DATA\mysql\db.MYI"
    
    if (Test-Path $dbTablePath) {
        Write-Host "[INFO] Repairing mysql.db table..." -ForegroundColor Yellow
        
        & $myisamchk -r -f "$dbTablePath"
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "[OK] Table repaired successfully!" -ForegroundColor Green
        } else {
            Write-Host "[WARNING] Repair completed with warnings. Trying recovery..." -ForegroundColor Yellow
            
            # Try recovery mode
            & $myisamchk --recover --force "$dbTablePath"
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host "[OK] Table recovered successfully!" -ForegroundColor Green
            } else {
                Write-Host "[ERROR] Recovery failed. Manual intervention may be required." -ForegroundColor Red
            }
        }
    } else {
        Write-Host "[ERROR] db.MYI file not found at: $dbTablePath" -ForegroundColor Red
        Write-Host "[INFO] Searching for db table files..." -ForegroundColor Cyan
        
        Get-ChildItem -Path "$MYSQL_DATA\mysql" -Filter "db.*" | ForEach-Object {
            Write-Host "  Found: $($_.Name)" -ForegroundColor Yellow
        }
    }
} else {
    Write-Host "[ERROR] myisamchk.exe not found at: $myisamchk" -ForegroundColor Red
    Write-Host "[INFO] Alternative: Use MySQL's built-in REPAIR TABLE command" -ForegroundColor Yellow
}

Write-Host ""

# Step 4: Check all tables in mysql database
Write-Host "[INFO] Step 4: Checking all mysql system tables..." -ForegroundColor Cyan

if (Test-Path $myisamchk) {
    Get-ChildItem -Path "$MYSQL_DATA\mysql" -Filter "*.MYI" | ForEach-Object {
        $tableName = $_.BaseName
        Write-Host "[INFO] Checking table: $tableName" -ForegroundColor Cyan
        
        & $myisamchk -c "$($_.FullName)"
        
        if ($LASTEXITCODE -ne 0) {
            Write-Host "[WARNING] Table $tableName has issues. Repairing..." -ForegroundColor Yellow
            & $myisamchk -r -f "$($_.FullName)"
        }
    }
    
    Write-Host "[OK] All tables checked" -ForegroundColor Green
}

Write-Host ""

# Step 5: Start MySQL service
Write-Host "[INFO] Step 5: Starting MySQL service..." -ForegroundColor Cyan

if ($mysqlService) {
    Start-Service -Name $mysqlService.Name
    Start-Sleep -Seconds 3
    
    $status = (Get-Service -Name $mysqlService.Name).Status
    if ($status -eq 'Running') {
        Write-Host "[OK] MySQL service started successfully" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] Failed to start MySQL service" -ForegroundColor Red
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
Write-Host "  1. Test MySQL connection"
Write-Host "  2. Run: SHOW TABLES FROM mysql;"
Write-Host "  3. Run: CHECK TABLE mysql.db;"
Write-Host "  4. If issues persist, run: REPAIR TABLE mysql.db;"
Write-Host ""
Write-Host "Alternative SQL repair commands:" -ForegroundColor Yellow
Write-Host "  USE mysql;"
Write-Host "  REPAIR TABLE db;"
Write-Host "  FLUSH PRIVILEGES;"
Write-Host ""
