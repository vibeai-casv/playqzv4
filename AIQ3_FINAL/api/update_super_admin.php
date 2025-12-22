<?php
require_once 'config.php';
require_once 'db.php';

try {
    // 1. Alter the ENUM column to include 'super_admin'
    echo "Altering profiles table...\n";
    $pdo->exec("ALTER TABLE profiles MODIFY COLUMN role ENUM('user', 'admin', 'super_admin') DEFAULT 'user' NOT NULL");
    echo "Table altered successfully.\n";

    // 2. Update the specific user to be super_admin
    $email = 'vibeaicasv@gmail.com';
    echo "Updating user $email...\n";
    
    $stmt = $pdo->prepare("UPDATE profiles SET role = 'super_admin' WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        echo "User $email promoted to super_admin successfully.\n";
    } else {
        echo "User $email not found or already super_admin.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
