<?php
/**
 * Check API Config
 * Shows what's in config.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Config Checker</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; }
        .success { background: #d4edda; padding: 15px; border-radius: 4px; margin: 10px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 4px; margin: 10px 0; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 4px; margin: 10px 0; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 API Configuration Check</h1>
    
    <?php
    $apiDir = dirname(__DIR__) . '/api';
    $configFile = $apiDir . '/config.php';
    
    echo "<div class='info'>";
    echo "<strong>Checking:</strong> <code>$configFile</code>";
    echo "</div>";
    
    if (file_exists($configFile)) {
        echo "<div class='success'>✓ config.php file exists!</div>";
        
        // Include it
        require_once $configFile;
        
        echo "<h2>Configuration Values:</h2>";
        echo "<table>";
        echo "<tr><th>Constant</th><th>Value</th><th>Status</th></tr>";
        
        $configs = [
            'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NOT DEFINED',
            'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NOT DEFINED',
            'DB_USER' => defined('DB_USER') ? DB_USER : 'NOT DEFINED',
            'DB_PASS' => defined('DB_PASS') ? (DB_PASS ? '***SET***' : 'EMPTY') : 'NOT DEFINED',
            'DB_CHARSET' => defined('DB_CHARSET') ? DB_CHARSET : 'NOT DEFINED',
            'ALLOWED_ORIGIN' => defined('ALLOWED_ORIGIN') ? ALLOWED_ORIGIN : 'NOT DEFINED'
        ];
        
        foreach ($configs as $key => $value) {
            $status = ($value === 'NOT DEFINED' || $value === 'EMPTY') ? '❌' : '✅';
            echo "<tr><td><strong>$key</strong></td><td><code>$value</code></td><td>$status</td></tr>";
        }
        echo "</table>";
        
        // Try database connection
        echo "<h2>Database Connection Test:</h2>";
        
        if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                
                echo "<div class='success'>";
                echo "✓ <strong>Database connection successful!</strong><br>";
                
                $stmt = $pdo->query("SELECT DATABASE() as db");
                $result = $stmt->fetch();
                echo "Connected to: <code>{$result['db']}</code>";
                echo "</div>";
                
                // Check tables
                echo "<h3>Tables in database:</h3>";
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (count($tables) > 0) {
                    echo "<div class='success'>";
                    echo "Found " . count($tables) . " tables:<br>";
                    echo "<code>" . implode(', ', $tables) . "</code>";
                    echo "</div>";
                } else {
                    echo "<div class='error'>";
                    echo "❌ No tables found! You need to import schema.sql";
                    echo "</div>";
                }
                
            } catch (PDOException $e) {
                echo "<div class='error'>";
                echo "❌ <strong>Database connection FAILED!</strong><br>";
                echo "Error: " . htmlspecialchars($e->getMessage());
                echo "</div>";
                
                echo "<div class='info'>";
                echo "<strong>Common fixes:</strong><br>";
                echo "1. Check database name is correct<br>";
                echo "2. Check database user has correct password<br>";
                echo "3. Check database exists in cPanel<br>";
                echo "4. Check user has permissions on database";
                echo "</div>";
            }
        } else {
            echo "<div class='error'>Cannot test - missing configuration values</div>";
        }
        
    } else {
        echo "<div class='error'>❌ config.php file NOT found at: <code>$configFile</code></div>";
    }
    ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 4px;">
        <strong>Next steps:</strong><br>
        1. If database connection fails, update config.php with correct credentials<br>
        2. If no tables found, import schema.sql via phpMyAdmin<br>
        3. Then try logging in again
    </div>
</div>
</body>
</html>
