<?php
/**
 * PlayQzV4 Diagnostic Script
 * This script collects information about your server setup
 * Upload this to /api/diagnostic.php and visit it in your browser
 */

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html>
<head>
    <title>PlayQzV4 Diagnostic Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 10px; border-radius: 4px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        .code { background: #f4f4f4; padding: 15px; border-radius: 4px; font-family: monospace; overflow-x: auto; margin: 10px 0; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .copy-btn { background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        .copy-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 PlayQzV4 Diagnostic Report</h1>
    <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    <p><strong>Server:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>

    <?php
    $diagnostics = [];
    $errors = [];
    $warnings = [];
    
    // ============================================
    // 1. PHP VERSION CHECK
    // ============================================
    echo "<h2>1. PHP Environment</h2>";
    
    $phpVersion = phpversion();
    $diagnostics['PHP Version'] = $phpVersion;
    
    if (version_compare($phpVersion, '7.4.0', '>=')) {
        echo "<div class='success'>✓ PHP Version: <strong>$phpVersion</strong> (Good)</div>";
    } else {
        echo "<div class='error'>✗ PHP Version: <strong>$phpVersion</strong> (Requires 7.4+)</div>";
        $errors[] = "PHP version too old. Need 7.4 or higher.";
    }
    
    // ============================================
    // 2. REQUIRED PHP EXTENSIONS
    // ============================================
    echo "<h2>2. PHP Extensions</h2>";
    echo "<table>";
    echo "<tr><th>Extension</th><th>Status</th><th>Required</th></tr>";
    
    $requiredExtensions = [
        'pdo' => 'Yes',
        'pdo_mysql' => 'Yes',
        'json' => 'Yes',
        'mbstring' => 'Yes',
        'openssl' => 'Recommended',
        'curl' => 'Recommended'
    ];
    
    foreach ($requiredExtensions as $ext => $required) {
        $loaded = extension_loaded($ext);
        $status = $loaded ? "<span class='badge badge-success'>Loaded</span>" : "<span class='badge badge-error'>Missing</span>";
        echo "<tr><td>$ext</td><td>$status</td><td>$required</td></tr>";
        
        $diagnostics["Extension: $ext"] = $loaded ? 'Loaded' : 'Missing';
        
        if (!$loaded && $required === 'Yes') {
            $errors[] = "Missing required PHP extension: $ext";
        }
    }
    echo "</table>";
    
    // ============================================
    // 3. FILE STRUCTURE CHECK
    // ============================================
    echo "<h2>3. File Structure</h2>";
    
    $apiDir = dirname(__DIR__) . '/api';
    
    $requiredFiles = [
        'config.php' => 'Configuration file',
        'db.php' => 'Database connection',
        'utils.php' => 'Utility functions',
        'index.php' => 'API entry point',
        'auth/login.php' => 'Login endpoint',
        'auth/signup.php' => 'Signup endpoint',
        'auth/me.php' => 'User info endpoint',
        'quiz/generate.php' => 'Quiz generation',
        'admin/users.php' => 'Admin users endpoint'
    ];
    
    echo "<table>";
    echo "<tr><th>File</th><th>Status</th><th>Purpose</th></tr>";
    
    $missingFiles = [];
    foreach ($requiredFiles as $file => $purpose) {
        $exists = file_exists($apiDir . '/' . $file);
        $status = $exists ? "<span class='badge badge-success'>Found</span>" : "<span class='badge badge-error'>Missing</span>";
        echo "<tr><td>$file</td><td>$status</td><td>$purpose</td></tr>";
        
        $diagnostics["File: $file"] = $exists ? 'Found' : 'Missing';
        
        if (!$exists) {
            $missingFiles[] = $file;
            $errors[] = "Missing file: $file";
        }
    }
    echo "</table>";
    
    if (count($missingFiles) > 0) {
        echo "<div class='error'><strong>Missing files:</strong> " . implode(', ', $missingFiles) . "</div>";
    }
    
    // ============================================
    // 3B. ACTUAL DIRECTORY LISTING
    // ============================================
    echo "<h2>3B. Actual Files in Parent Directory</h2>";
    echo "<p><strong>Scanning:</strong> <code>" . dirname(__DIR__) . "</code></p>";
    
    function scanDirectory($dir, $prefix = '') {
        $files = [];
        if (!is_dir($dir)) {
            return $files;
        }
        
        $items = @scandir($dir);
        if ($items === false) {
            return $files;
        }
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $path = $dir . '/' . $item;
            $relativePath = $prefix . $item;
            
            if (is_dir($path)) {
                $files[] = [
                    'type' => 'dir',
                    'path' => $relativePath,
                    'size' => '-'
                ];
                // Recursively scan subdirectories (limit depth)
                if (substr_count($relativePath, '/') < 3) {
                    $subFiles = scanDirectory($path, $relativePath . '/');
                    $files = array_merge($files, $subFiles);
                }
            } else {
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                $files[] = [
                    'type' => 'file',
                    'path' => $relativePath,
                    'size' => filesize($path),
                    'ext' => $ext
                ];
            }
        }
        return $files;
    }
    
    $parentDir = dirname(__DIR__);
    $allFiles = scanDirectory($parentDir);
    
    // Separate by type
    $phpFiles = [];
    $directories = [];
    $otherFiles = [];
    
    foreach ($allFiles as $file) {
        if ($file['type'] === 'dir') {
            $directories[] = $file;
        } elseif (isset($file['ext']) && $file['ext'] === 'php') {
            $phpFiles[] = $file;
        } else {
            $otherFiles[] = $file;
        }
    }
    
    echo "<div class='info'>";
    echo "<strong>Found:</strong> ";
    echo count($phpFiles) . " PHP files, ";
    echo count($directories) . " directories, ";
    echo count($otherFiles) . " other files";
    echo "</div>";
    
    // Show PHP files
    if (count($phpFiles) > 0) {
        echo "<h3>PHP Files Found:</h3>";
        echo "<table>";
        echo "<tr><th>File Path</th><th>Size</th></tr>";
        foreach ($phpFiles as $file) {
            $sizeKB = round($file['size'] / 1024, 2);
            echo "<tr><td><code>{$file['path']}</code></td><td>{$sizeKB} KB</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='warning'>⚠ No PHP files found in parent directory!</div>";
    }
    
    // Show directories
    if (count($directories) > 0) {
        echo "<h3>Directories Found:</h3>";
        echo "<div class='code'>";
        foreach ($directories as $dir) {
            echo $dir['path'] . "/\n";
        }
        echo "</div>";
    }
    
    // Check specifically for /api/ directory
    $apiDir = $parentDir . '/api';
    if (is_dir($apiDir)) {
        echo "<div class='success'>✓ /api/ directory exists at: <code>$apiDir</code></div>";
        
        // List contents of /api/
        echo "<h3>Contents of /api/ directory:</h3>";
        $apiFiles = scanDirectory($apiDir, 'api/');
        
        if (count($apiFiles) > 0) {
            echo "<table>";
            echo "<tr><th>Type</th><th>Path</th><th>Size</th></tr>";
            foreach ($apiFiles as $file) {
                $type = $file['type'] === 'dir' ? '📁 DIR' : '📄 FILE';
                $size = $file['type'] === 'dir' ? '-' : round($file['size'] / 1024, 2) . ' KB';
                echo "<tr><td>$type</td><td><code>{$file['path']}</code></td><td>$size</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='warning'>⚠ /api/ directory is empty!</div>";
        }
    } else {
        echo "<div class='error'>✗ /api/ directory NOT found at: <code>$apiDir</code></div>";
        echo "<div class='warning'>You need to create /api/ directory and upload API files there!</div>";
    }
    
    // ============================================
    // 4. CONFIG.PHP CHECK
    // ============================================
    echo "<h2>4. Configuration</h2>";
    
    $configFile = $apiDir . '/config.php';
    
    if (file_exists($configFile)) {
        require_once $configFile;
        
        echo "<table>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        
        $configItems = [
            'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NOT DEFINED',
            'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NOT DEFINED',
            'DB_USER' => defined('DB_USER') ? DB_USER : 'NOT DEFINED',
            'DB_PASS' => defined('DB_PASS') ? (DB_PASS ? '***SET***' : 'EMPTY') : 'NOT DEFINED',
            'DB_CHARSET' => defined('DB_CHARSET') ? DB_CHARSET : 'NOT DEFINED',
            'ALLOWED_ORIGIN' => defined('ALLOWED_ORIGIN') ? ALLOWED_ORIGIN : 'NOT DEFINED'
        ];
        
        foreach ($configItems as $key => $value) {
            echo "<tr><td>$key</td><td><code>$value</code></td></tr>";
            $diagnostics[$key] = $value;
        }
        echo "</table>";
        
        if (!defined('DB_NAME') || DB_NAME === 'NOT DEFINED') {
            $errors[] = "DB_NAME not defined in config.php";
        }
        if (!defined('DB_USER') || DB_USER === 'NOT DEFINED') {
            $errors[] = "DB_USER not defined in config.php";
        }
    } else {
        echo "<div class='error'>✗ config.php file not found!</div>";
        $errors[] = "config.php file missing";
    }
    
    // ============================================
    // 5. DATABASE CONNECTION TEST
    // ============================================
    echo "<h2>5. Database Connection</h2>";
    
    $dbConnected = false;
    $dbError = '';
    
    $dbFile = $apiDir . '/db.php';
    
    if (file_exists($dbFile)) {
        try {
            require_once $dbFile;
            
            if (isset($pdo)) {
                echo "<div class='success'>✓ Database connection successful!</div>";
                $dbConnected = true;
                $diagnostics['Database Connection'] = 'Success';
                
                // Get database info
                $stmt = $pdo->query("SELECT DATABASE() as db_name");
                $dbInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<div class='info'>Connected to database: <strong>" . $dbInfo['db_name'] . "</strong></div>";
                
            } else {
                echo "<div class='error'>✗ Database connection object not created</div>";
                $errors[] = "PDO object not created in db.php";
            }
        } catch (PDOException $e) {
            echo "<div class='error'>✗ Database connection failed!</div>";
            echo "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            $dbError = $e->getMessage();
            $errors[] = "Database connection failed: " . $e->getMessage();
            $diagnostics['Database Connection'] = 'Failed: ' . $e->getMessage();
        }
    } else {
        echo "<div class='error'>✗ db.php file not found!</div>";
        $errors[] = "db.php file missing";
    }
    
    // ============================================
    // 6. DATABASE TABLES CHECK
    // ============================================
    if ($dbConnected) {
        echo "<h2>6. Database Tables</h2>";
        
        $requiredTables = [
            'users',
            'sessions',
            'profiles',
            'questions',
            'quiz_attempts',
            'quiz_responses',
            'media_library',
            'user_activity_logs'
        ];
        
        echo "<table>";
        echo "<tr><th>Table</th><th>Status</th><th>Row Count</th></tr>";
        
        $missingTables = [];
        foreach ($requiredTables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $count = $result['count'];
                echo "<tr><td>$table</td><td><span class='badge badge-success'>Exists</span></td><td>$count rows</td></tr>";
                $diagnostics["Table: $table"] = "Exists ($count rows)";
            } catch (PDOException $e) {
                echo "<tr><td>$table</td><td><span class='badge badge-error'>Missing</span></td><td>-</td></tr>";
                $missingTables[] = $table;
                $errors[] = "Missing table: $table";
                $diagnostics["Table: $table"] = "Missing";
            }
        }
        echo "</table>";
        
        if (count($missingTables) > 0) {
            echo "<div class='error'><strong>Missing tables:</strong> " . implode(', ', $missingTables) . "</div>";
            echo "<div class='warning'>⚠ You need to import schema.sql via phpMyAdmin</div>";
        }
        
        // Check for admin user
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM profiles WHERE role = 'admin'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $adminCount = $result['count'];
            
            if ($adminCount > 0) {
                echo "<div class='success'>✓ Found $adminCount admin user(s)</div>";
                $diagnostics['Admin Users'] = $adminCount;
            } else {
                echo "<div class='warning'>⚠ No admin users found. You need to create one!</div>";
                $warnings[] = "No admin users in database";
                $diagnostics['Admin Users'] = 0;
            }
        } catch (PDOException $e) {
            // Table might not exist
        }
    }
    
    // ============================================
    // 7. SERVER INFORMATION
    // ============================================
    echo "<h2>7. Server Information</h2>";
    echo "<table>";
    echo "<tr><th>Setting</th><th>Value</th></tr>";
    
    $serverInfo = [
        'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'Script Filename' => __FILE__,
        'Current Directory' => __DIR__,
        'PHP SAPI' => php_sapi_name(),
        'Max Execution Time' => ini_get('max_execution_time') . ' seconds',
        'Memory Limit' => ini_get('memory_limit'),
        'Upload Max Filesize' => ini_get('upload_max_filesize'),
        'Post Max Size' => ini_get('post_max_size'),
        'Display Errors' => ini_get('display_errors') ? 'On' : 'Off',
        'Error Reporting' => ini_get('error_reporting')
    ];
    
    foreach ($serverInfo as $key => $value) {
        echo "<tr><td>$key</td><td><code>$value</code></td></tr>";
        $diagnostics[$key] = $value;
    }
    echo "</table>";
    
    // ============================================
    // 8. PERMISSIONS CHECK
    // ============================================
    echo "<h2>8. File Permissions</h2>";
    
    $checkPaths = [
        $apiDir => 'API Directory',
        $apiDir . '/config.php' => 'Config File',
        $apiDir . '/auth' => 'Auth Directory'
    ];
    
    echo "<table>";
    echo "<tr><th>Path</th><th>Readable</th><th>Writable</th><th>Permissions</th></tr>";
    
    foreach ($checkPaths as $path => $label) {
        if (file_exists($path)) {
            $readable = is_readable($path) ? '✓' : '✗';
            $writable = is_writable($path) ? '✓' : '✗';
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            echo "<tr><td>$label</td><td>$readable</td><td>$writable</td><td>$perms</td></tr>";
        }
    }
    echo "</table>";
    
    // ============================================
    // 9. SUMMARY
    // ============================================
    echo "<h2>9. Summary</h2>";
    
    if (count($errors) === 0) {
        echo "<div class='success'><strong>✓ All checks passed!</strong> Your server is properly configured.</div>";
    } else {
        echo "<div class='error'><strong>✗ Found " . count($errors) . " error(s):</strong></div>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
    
    if (count($warnings) > 0) {
        echo "<div class='warning'><strong>⚠ " . count($warnings) . " warning(s):</strong></div>";
        echo "<ul>";
        foreach ($warnings as $warning) {
            echo "<li>$warning</li>";
        }
        echo "</ul>";
    }
    
    // ============================================
    // 10. COPY REPORT
    // ============================================
    echo "<h2>10. Copy Report for Support</h2>";
    echo "<p>Copy the text below and share it for troubleshooting:</p>";
    
    $report = "=== PlayQzV4 Diagnostic Report ===\n";
    $report .= "Generated: " . date('Y-m-d H:i:s') . "\n";
    $report .= "Server: " . $_SERVER['HTTP_HOST'] . "\n\n";
    
    foreach ($diagnostics as $key => $value) {
        $report .= "$key: $value\n";
    }
    
    $report .= "\n=== Errors ===\n";
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            $report .= "- $error\n";
        }
    } else {
        $report .= "None\n";
    }
    
    $report .= "\n=== Warnings ===\n";
    if (count($warnings) > 0) {
        foreach ($warnings as $warning) {
            $report .= "- $warning\n";
        }
    } else {
        $report .= "None\n";
    }
    
    echo "<div class='code' id='reportText'>" . htmlspecialchars($report) . "</div>";
    echo "<button class='copy-btn' onclick='copyReport()'>📋 Copy Report</button>";
    ?>
    
    <script>
    function copyReport() {
        const text = document.getElementById('reportText').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('Report copied to clipboard!');
        });
    }
    </script>
    
    <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
        <p><strong>⚠️ IMPORTANT:</strong> After copying the report, delete this diagnostic.php file for security!</p>
        <p>To delete: Remove <code>/api/diagnostic.php</code> from your server via FTP or cPanel File Manager.</p>
    </div>
</div>
</body>
</html>
<?php
// Prevent any output after this
exit;
?>
