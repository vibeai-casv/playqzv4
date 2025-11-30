<?php
/**
 * Fix Admin User Password
 * This script verifies and fixes the admin user password
 */

header('Content-Type: application/json');

$apiDir = dirname(__DIR__) . '/api';

require_once $apiDir . '/config.php';
require_once $apiDir . '/db.php';

try {
    $email = 'vibeaicasv@gmail.com';
    $plainPassword = 'password123';
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT u.id, u.email, u.password_hash, p.role FROM users u JOIN profiles p ON u.id = p.id WHERE u.email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Admin user not found',
            'email' => $email
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    // Show current state
    $currentHashWorks = password_verify($plainPassword, $user['password_hash']);
    
    $result = [
        'status' => 'info',
        'user_found' => true,
        'email' => $user['email'],
        'role' => $user['role'],
        'current_hash' => substr($user['password_hash'], 0, 20) . '...',
        'password_verify_result' => $currentHashWorks ? 'WORKS' : 'FAILS'
    ];
    
    // If password doesn't work, update it
    if (!$currentHashWorks) {
        $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->execute([$newHash, $email]);
        
        // Verify the new hash works
        $verifyNew = password_verify($plainPassword, $newHash);
        
        $result['status'] = 'success';
        $result['message'] = 'Password hash updated!';
        $result['new_hash'] = substr($newHash, 0, 20) . '...';
        $result['new_hash_works'] = $verifyNew ? 'YES' : 'NO';
        $result['action'] = 'Password updated - try logging in again';
    } else {
        $result['message'] = 'Password hash is correct - issue might be elsewhere';
        $result['suggestion'] = 'Check browser console for exact error';
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
