<?php
/**
 * Verify Password Hashes and Test Login
 */

require_once __DIR__ . '/api/config.php';
require_once __DIR__ . '/api/db.php';

echo "=== Password Hash Verification ===\n\n";

// The expected password hashes from SQL files
$expectedHashes = [
    'admin@example.com' => [
        'password' => 'admin123',
        'hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ],
    'testadmin@local.test' => [
        'password' => 'testadmin123',
        'hash' => '$2y$10$rZ8qH5vGx5vGx5vGx5vGxOXKqH5vGx5vGx5vGx5vGx5vGx5vGx5vG'
    ]
];

// Get all users from database
$stmt = $pdo->query("SELECT u.email, u.password_hash, p.role FROM users u LEFT JOIN profiles p ON u.id = p.id");
$users = $stmt->fetchAll();

echo "Users in database:\n";
echo str_repeat('-', 80) . "\n";
printf("%-30s %-15s %-50s\n", "Email", "Role", "Password Hash (first 40 chars)");
echo str_repeat('-', 80) . "\n";

foreach ($users as $user) {
    $hashPreview = substr($user['password_hash'], 0, 40) . '...';
    printf("%-30s %-15s %-50s\n", $user['email'], $user['role'] ?? 'N/A', $hashPreview);
}

echo "\n\n=== Testing Password Verification ===\n\n";

foreach ($expectedHashes as $email => $data) {
    echo "Testing: $email\n";
    echo "Password: {$data['password']}\n";
    
    // Get user from database
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "❌ User NOT found in database!\n";
        echo "Action: Run create_admin_user.sql in phpMyAdmin\n\n";
        continue;
    }
    
    $dbHash = $user['password_hash'];
    echo "DB Hash: " . substr($dbHash, 0, 50) . "...\n";
    
    // Test password verification
    $verified = password_verify($data['password'], $dbHash);
    
    if ($verified) {
        echo "✅ Password verification: SUCCESS\n";
        echo "   This account should be able to login!\n";
    } else {
        echo "❌ Password verification: FAILED\n";
        echo "   The password hash in database doesn't match expected password!\n";
        echo "\n";
        echo "   FIX: Run this SQL in phpMyAdmin:\n";
        echo "   UPDATE users SET password_hash = '{$data['hash']}' WHERE email = '$email';\n";
    }
    
    echo "\n" . str_repeat('-', 80) . "\n\n";
}

// Generate new hash for admin123
echo "=== Password Hash Generator ===\n\n";
echo "If you need to reset a password, use these hashes:\n\n";

$passwords = ['admin123', 'testadmin123', 'password123'];
foreach ($passwords as $pwd) {
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    echo "Password: $pwd\n";
    echo "Hash: $hash\n\n";
}

echo "\n=== Quick Fix SQL ===\n\n";
echo "If passwords aren't working, run this SQL:\n\n";
echo "-- Reset admin@example.com password to 'admin123'\n";
$newHash = password_hash('admin123', PASSWORD_DEFAULT);
echo "UPDATE users SET password_hash = '$newHash' WHERE email = 'admin@example.com';\n\n";

echo "-- Reset testadmin@local.test password to 'testadmin123'\n";
$newHash = password_hash('testadmin123', PASSWORD_DEFAULT);
echo "UPDATE users SET password_hash = '$newHash' WHERE email = 'testadmin@local.test';\n\n";

echo "=== End of Verification ===\n";
?>
