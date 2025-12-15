<?php
require_once __DIR__ . '/api/config.php';
require_once __DIR__ . '/api/db.php';

echo "=== Fixing Admin Password ===\n\n";

// The correct hash for 'admin123'
$correctHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

// Update the password
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
$result = $stmt->execute([$correctHash, 'admin@example.com']);

if ($result) {
    echo "✅ Password hash updated for admin@example.com\n\n";
    
    // Verify it works
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = ?");
    $stmt->execute(['admin@example.com']);
    $user = $stmt->fetch();
    
    $verified = password_verify('admin123', $user['password_hash']);
    
    if ($verified) {
        echo "✅ Verification: Password 'admin123' now works!\n";
        echo "\nYou can now login with:\n";
        echo "  Email: admin@example.com\n";
        echo "  Password: admin123\n";
    } else {
        echo "❌ Verification failed - something went wrong\n";
    }
} else {
    echo "❌ Failed to update password\n";
}

echo "\n=== Complete ===\n";
?>
