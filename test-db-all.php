<?php
/**
 * Test Multiple Database Configurations
 */

echo "=== Testing Multiple Database Configurations ===\n\n";

$configurations = [
    [
        'name' => 'XAMPP Default (empty password)',
        'host' => 'localhost',
        'dbname' => 'aiqz',
        'username' => 'root',
        'password' => ''
    ],
    [
        'name' => 'Custom Password (Aiquiz@mpm)',
        'host' => 'localhost',
        'dbname' => 'aiqz',
        'username' => 'root',
        'password' => 'Aiquiz@mpm'
    ],
];

$successfulConfig = null;

foreach ($configurations as $config) {
    echo "Testing: {$config['name']}\n";
    echo "  Host: {$config['host']}\n";
    echo "  Database: {$config['dbname']}\n";
    echo "  Username: {$config['username']}\n";
    echo "  Password: " . (empty($config['password']) ? '(empty)' : '***') . "\n";
    
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
        echo "  ✓ CONNECTION SUCCESSFUL!\n\n";
        $successfulConfig = $config;
        break; // Stop on first successful connection
        
    } catch (PDOException $e) {
        echo "  ✗ Failed: " . $e->getMessage() . "\n\n";
    }
}

if ($successfulConfig) {
    echo "========================================\n";
    echo "✅ WORKING CONFIGURATION FOUND!\n";
    echo "========================================\n\n";
    
    echo "Database: {$successfulConfig['dbname']}\n";
    echo "Username: {$successfulConfig['username']}\n";
    echo "Password: " . (empty($successfulConfig['password']) ? '(empty)' : $successfulConfig['password']) . "\n\n";
    
    // Get more details
    $dsn = "mysql:host={$successfulConfig['host']};dbname={$successfulConfig['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $successfulConfig['username'], $successfulConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // MySQL version
    $stmt = $pdo->query('SELECT VERSION() as version');
    $version = $stmt->fetch();
    echo "MySQL Version: {$version['version']}\n\n";
    
    // List tables
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . count($tables) . " found\n";
    
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            echo "  - $table\n";
        }
    } else {
        echo "  ⚠ No tables! Need to import schema.sql\n";
    }
    
    // Check for admin users
    if (in_array('users', $tables)) {
        echo "\n";
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
        $count = $stmt->fetch()['count'];
        echo "Users in database: $count\n";
        
        if ($count > 0) {
            $stmt = $pdo->query('SELECT u.email, p.role FROM users u LEFT JOIN profiles p ON u.id = p.id');
            $users = $stmt->fetchAll();
            echo "User list:\n";
            foreach ($users as $user) {
                echo "  - {$user['email']} ({$user['role']})\n";
            }
        } else {
            echo "  ⚠ No users found! Run create_admin_user.sql\n";
        }
    }
    
    echo "\n";
    echo "Update api/config.php with these settings:\n";
    echo "  define('DB_HOST', 'localhost');\n";
    echo "  define('DB_NAME', 'aiqz');\n";
    echo "  define('DB_USER', 'root');\n";
    echo "  define('DB_PASS', '" . $successfulConfig['password'] . "');\n";
    
} else {
    echo "========================================\n";
    echo "❌ NO WORKING CONFIGURATION FOUND!\n";
    echo "========================================\n\n";
    echo "Possible issues:\n";
    echo "  1. XAMPP MySQL is not running\n";
    echo "  2. Database 'aiqz' does not exist\n";
    echo "  3. Root password has been changed\n\n";
    echo "To fix:\n";
    echo "  1. Open XAMPP Control Panel\n";
    echo "  2. Start MySQL service\n";
    echo "  3. Open phpMyAdmin: http://localhost/phpmyadmin\n";
    echo "  4. Create database: CREATE DATABASE aiqz;\n";
}

echo "\n=== Test Complete ===\n";
?>
