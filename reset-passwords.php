<?php
require_once __DIR__ . '/api/config.php';
require_once __DIR__ . '/api/db.php';

echo "=== Resetting Admin Passwords ===\n\n";

// Generate FRESH hashes
$admin_hash = password_hash('admin123', PASSWORD_BCRYPT);
$testadmin_hash = password_hash('testadmin123', PASSWORD_BCRYPT);

echo "New hashes generated:\n";
echo "admin123 => $admin_hash\n";
echo "testadmin123 => $testadmin_hash\n\n";

// Update both accounts
echo "Updating admin@example.com...\n";
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
$stmt->execute([$admin_hash, 'admin@example.com']);
echo "✅ Updated\n\n";

echo "Updating testadmin@local.test...\n";
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
$stmt->execute([$testadmin_hash, 'testadmin@local.test']);
echo "✅ Updated\n\n";

// Verify both
echo "=== Verification ===\n\n";

$accounts = [
    ['email' => 'admin@example.com', 'password' => 'admin123'],
    ['email' => 'testadmin@local.test', 'password' => 'testadmin123'],
];

foreach ($accounts as $account) {
    echo "Testing {$account['email']}...\n";
    
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = ?");
    $stmt->execute([$account['email']]);
    $user = $stmt->fetch();
    
    if (password_verify($account['password'], $user['password_hash'])) {
        echo "  ✅ Password '{$account['password']}' works!\n";
    } else {
        echo "  ❌ Password verification failed\n";
    }
    echo "\n";
}

echo "=== Ready to Login ===\n\n";
echo "You can now login with:\n\n";
echo "Account 1:\n";
echo "  Email: admin@example.com\n";
echo "  Password: admin123\n\n";
echo "Account 2:\n";
echo "  Email: testadmin@local.test\n";
echo "  Password: testadmin123\n";
?>
