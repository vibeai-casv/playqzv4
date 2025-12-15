<?php
/**
 * Simple Database Connection Test
 */

echo "=== Database Connection Test ===\n\n";

// Database credentials
$host = 'localhost';
$dbname = 'aiqz';
$username = 'root';
$password = 'Aiquiz@mpm';

echo "Testing connection to:\n";
echo "  Host: $host\n";
echo "  Database: $dbname\n";
echo "  Username: $username\n";
echo "  Password: " . (empty($password) ? '(empty)' : '***') . "\n\n";

// Check PHP extensions
echo "PHP Version: " . phpversion() . "\n";
echo "PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "\n";
echo "PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "\n\n";

// Try to connect
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✓ CONNECTION SUCCESSFUL!\n\n";
    
    // Get MySQL version
    $stmt = $pdo->query('SELECT VERSION() as version');
    $version = $stmt->fetch();
    echo "MySQL Version: " . $version['version'] . "\n\n";
    
    // List all tables
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables found: " . count($tables) . "\n";
    if (count($tables) > 0) {
        echo "Table list:\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    } else {
        echo "⚠ No tables found! You may need to import schema.sql\n";
    }
    
    echo "\n";
    
    // Check for users table
    if (in_array('users', $tables)) {
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
        $result = $stmt->fetch();
        echo "Users in database: " . $result['count'] . "\n";
        
        if ($result['count'] > 0) {
            $stmt = $pdo->query('SELECT email, created_at FROM users LIMIT 5');
            $users = $stmt->fetchAll();
            echo "Sample users:\n";
            foreach ($users as $user) {
                echo "  - " . $user['email'] . " (created: " . $user['created_at'] . ")\n";
            }
        }
    }
    
    echo "\n✅ Database is ready to use!\n";
    
} catch (PDOException $e) {
    echo "✗ CONNECTION FAILED!\n\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n\n";
    
    // Provide specific help
    if (strpos($e->getMessage(), '1045') !== false) {
        echo "Issue: Access denied - Invalid username or password\n";
        echo "Solutions:\n";
        echo "  1. Check that XAMPP MySQL is running\n";
        echo "  2. Verify the password in api/config.php\n";
        echo "  3. Default XAMPP password is usually empty\n";
    } elseif (strpos($e->getMessage(), '1049') !== false) {
        echo "Issue: Database '$dbname' does not exist\n";
        echo "Solutions:\n";
        echo "  1. Create database in phpMyAdmin: http://localhost/phpmyadmin\n";
        echo "  2. Run: CREATE DATABASE aiqz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
        echo "  3. Import schema from api/schema.sql\n";
    } elseif (strpos($e->getMessage(), '2002') !== false) {
        echo "Issue: Cannot connect to MySQL server\n";
        echo "Solutions:\n";
        echo "  1. Start XAMPP Control Panel and start MySQL\n";
        echo "  2. Check if MySQL is running on port 3306\n";
    }
}

echo "\n=== Test Complete ===\n";
?>
