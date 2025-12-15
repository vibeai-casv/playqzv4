<?php
require_once __DIR__ . '/api/config.php';
require_once __DIR__ . '/api/db.php';

echo "=== Password Test ===\n\n";

// Test accounts
$accounts = [
    ['email' => 'admin@example.com', 'password' => 'admin123'],
    ['email' => 'testadmin@local.test', 'password' => 'testadmin123'],
];

foreach ($accounts as $account) {
    echo "Testing: {$account['email']}\n";
    
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = ?");
    $stmt->execute([$account['email']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "  ❌ User not found!\n\n";
        continue;
    }
    
    $verified = password_verify($account['password'], $user['password_hash']);
    
    if ($verified) {
        echo "  ✅ Password CORRECT - Login should work!\n";
    } else {
        echo "  ❌ Password WRONG - Needs reset!\n";
        $newHash = password_hash($account['password'], PASSWORD_DEFAULT);
        echo "  Fix: UPDATE users SET password_hash = '$newHash' WHERE email = '{$account['email']}';\n";
    }
    echo "\n";
}
?>
