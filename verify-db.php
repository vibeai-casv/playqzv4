<?php
require_once __DIR__ . '/api/config.php';

echo "=== Testing with api/config.php Settings ===\n\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_PASS: " . (DB_PASS === '' ? '(empty)' : '***') . "\n\n";

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ CONNECTION SUCCESSFUL!\n\n";
    
    // Get user count
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
    $userCount = $stmt->fetch()['count'];
    
    // Get admin users
    $stmt = $pdo->query('SELECT u.email, p.role FROM users u LEFT JOIN profiles p ON u.id = p.id WHERE p.role LIKE "%admin%"');
    $admins = $stmt->fetchAll();
    
    echo "Total Users: $userCount\n";
    echo "Admin Users: " . count($admins) . "\n\n";
    
    echo "Admin Accounts:\n";
    foreach ($admins as $admin) {
        echo "  - {$admin['email']} ({$admin['role']})\n";
    }
    
    echo "\n✅ Database is ready! You can now login to the application.\n";
    
} catch (PDOException $e) {
    echo "❌ CONNECTION FAILED!\n\n";
    echo "Error: " . $e->getMessage() . "\n";
}
?>
