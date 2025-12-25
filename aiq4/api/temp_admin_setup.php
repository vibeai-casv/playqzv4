<?php
require_once 'config.php';
require_once 'db.php';

$email = 'admin@test.com';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
$id = 'temp-admin-id';

// 1. Users table
$sql = "INSERT INTO users (id, email, password_hash) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE password_hash = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id, $email, $hash, $hash]);

// 2. Profiles table
$sql = "INSERT INTO profiles (id, name, email, role) VALUES (?, 'Test Admin', ?, 'admin') ON DUPLICATE KEY UPDATE role = 'admin'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id, $email]);

echo "Admin setup complete. Login with $email / $password";
?>
