<?php
/**
 * Direct Login Test
 * Tests login with actual credentials
 */

header('Content-Type: application/json');

$apiDir = dirname(__DIR__) . '/api';

require_once $apiDir . '/config.php';
require_once $apiDir . '/db.php';

$email = 'vibeaicasv@gmail.com';
$password = 'password123';

try {
    // Get user
    $stmt = $pdo->prepare("
        SELECT u.id, u.email, u.password_hash, p.name, p.role 
        FROM users u 
        JOIN profiles p ON u.id = p.id 
        WHERE u.email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode([
            'status' => 'error',
            'step' => 'user_lookup',
            'message' => 'User not found',
            'email' => $email
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    // Verify password
    $passwordValid = password_verify($password, $user['password_hash']);
    
    if (!$passwordValid) {
        echo json_encode([
            'status' => 'error',
            'step' => 'password_verification',
            'message' => 'Password does not match',
            'email' => $email,
            'hash_length' => strlen($user['password_hash']),
            'hash_prefix' => substr($user['password_hash'], 0, 7),
            'password_tested' => $password,
            'suggestion' => 'Run fix-admin-password.php to update the password hash'
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    // Success!
    echo json_encode([
        'status' => 'success',
        'message' => 'Login credentials are valid!',
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role']
        ],
        'next_step' => 'Try logging in through the website now'
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'step' => 'exception',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
