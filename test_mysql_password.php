<?php
// Test different MySQL passwords
$passwords = ['', 'root', 'Aiquiz@mpm', 'password', 'admin'];

foreach ($passwords as $pass) {
    echo "\nTesting password: " . ($pass === '' ? '(empty)' : $pass) . "\n";
    
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=aiqz;charset=utf8mb4",
            "root",
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "✅ SUCCESS! Password is: " . ($pass === '' ? '(empty)' : $pass) . "\n";
        echo "Database connected successfully!\n";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT DATABASE() as db");
        $result = $stmt->fetch();
        echo "Current database: " . $result['db'] . "\n";
        exit(0);
    } catch (PDOException $e) {
        echo "❌ Failed: " . $e->getMessage() . "\n";
    }
}

echo "\nNone of the common passwords worked. You may need to reset the MySQL root password.\n";
?>
