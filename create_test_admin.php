<?php
/**
 * Create Test Admin User for Local Development
 * Run this file directly: http://projects/playqzv4/create_test_admin.php
 */

require_once 'api/config.php';
require_once 'api/db.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Create Test Admin User</h1>";

try {
    $email = 'testadmin@local.test';
    $password = 'testadmin123';
    $name = 'Test Admin (Local)';
    
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        echo "<p style='color: orange;'>⚠️ User already exists. Updating password...</p>";
        
        // Update password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->execute([$passwordHash, $email]);
        
        echo "<p style='color: green;'>✅ Password updated!</p>";
    } else {
        echo "<p>Creating new test admin user...</p>";
        
        // Generate UUID
        $userId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        // Create user
        $stmt = $pdo->prepare("INSERT INTO users (id, email, password_hash, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId, $email, $passwordHash]);
        
        // Create profile
        $stmt = $pdo->prepare("INSERT INTO profiles (id, email, name, role, created_at) VALUES (?, ?, ?, 'admin', NOW())");
        $stmt->execute([$userId, $email, $name]);
        
        echo "<p style='color: green;'>✅ Test admin user created!</p>";
    }
    
    echo "<hr>";
    echo "<h2>Login Credentials:</h2>";
    echo "<p><strong>Email:</strong> <code>$email</code></p>";
    echo "<p><strong>Password:</strong> <code>$password</code></p>";
    echo "<hr>";
    echo "<p><a href='http://localhost:5173/login' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    
    // Verify
    $stmt = $pdo->prepare("
        SELECT u.id, u.email, p.name, p.role 
        FROM users u 
        JOIN profiles p ON u.id = p.id 
        WHERE u.email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>User Details:</h3>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
