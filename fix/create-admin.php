<?php
/**
 * Create Admin User Script
 * Upload to: /fix/create-admin.php
 * Access: https://aiquiz.vibeai.cv/fix/create-admin.php
 * 
 * This script creates the default admin user
 */

header('Content-Type: application/json');

$apiDir = dirname(__DIR__) . '/api';

require_once $apiDir . '/config.php';
require_once $apiDir . '/db.php';

try {
    // Generate UUID
    $user_id = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    $email = 'vibeaicasv@gmail.com';
    $password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password123
    
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo json_encode([
            'status' => 'info',
            'message' => 'Admin user already exists',
            'email' => $email,
            'user_id' => $existing['id']
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert into users table
    $stmt = $pdo->prepare("
        INSERT INTO users (id, email, password_hash, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $email, $password_hash]);
    
    // Insert into profiles table
    $stmt = $pdo->prepare("
        INSERT INTO profiles (id, email, name, role, created_at)
        VALUES (?, ?, ?, 'admin', NOW())
    ");
    $stmt->execute([$user_id, $email, 'Admin User']);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Admin user created successfully!',
        'user_id' => $user_id,
        'email' => $email,
        'password' => 'password123',
        'warning' => 'CHANGE PASSWORD IMMEDIATELY AFTER FIRST LOGIN!'
    ], JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to create admin user',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
