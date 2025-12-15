<?php
/**
 * MySQL Service Status Checker
 */

echo "=== MySQL Service Status Check ===\n\n";

// Method 1: Try to connect
echo "1. Testing MySQL Connection...\n";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aiqz', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3  // 3 second timeout
    ]);
    echo "   ✅ MySQL is RUNNING and accepting connections\n\n";
    
    // Get server status
    $stmt = $pdo->query("SHOW STATUS WHERE Variable_name IN ('Uptime', 'Threads_connected', 'Max_used_connections')");
    $status = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo "   Server Uptime: " . gmdate("H:i:s", $status['Uptime']) . "\n";
    echo "   Active Connections: " . $status['Threads_connected'] . "\n";
    echo "   Max Used Connections: " . $status['Max_used_connections'] . "\n\n";
    
    // Get important variables
    $stmt = $pdo->query("SHOW VARIABLES WHERE Variable_name IN ('max_allowed_packet', 'wait_timeout', 'interactive_timeout')");
    $vars = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo "   Max Packet Size: " . round($vars['max_allowed_packet'] / 1024 / 1024, 2) . " MB\n";
    echo "   Wait Timeout: " . $vars['wait_timeout'] . " seconds\n";
    echo "   Interactive Timeout: " . $vars['interactive_timeout'] . " seconds\n\n";
    
    echo "✅ MySQL is working properly!\n";
    
} catch (PDOException $e) {
    echo "   ❌ CANNOT CONNECT TO MYSQL\n\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   Error Code: " . $e->getCode() . "\n\n";
    
    if ($e->getCode() == 2002) {
        echo "   Issue: MySQL server is not running\n\n";
    } elseif ($e->getCode() == 2006) {
        echo "   Issue: MySQL server has gone away\n\n";
    }
    
    echo "   📋 Troubleshooting Steps:\n";
    echo "   1. Open XAMPP Control Panel\n";
    echo "   2. Check if MySQL service is running (green indicator)\n";
    echo "   3. If not running, click 'Start' button\n";
    echo "   4. If it fails to start, check XAMPP error logs\n";
    echo "   5. Try stopping and starting again\n\n";
}

// Method 2: Check if MySQL port is listening
echo "2. Checking MySQL Port (3306)...\n";
$connection = @fsockopen('localhost', 3306, $errno, $errstr, 2);
if ($connection) {
    echo "   ✅ Port 3306 is open and listening\n";
    fclose($connection);
} else {
    echo "   ❌ Port 3306 is NOT responding\n";
    echo "   This confirms MySQL is not running\n";
}

echo "\n=== End of Check ===\n";
?>
