<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

try {
    // Get the token from the Authorization header directly
    $headers = apache_request_headers();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        
        // Delete the specific session
        $stmt = $pdo->prepare("DELETE FROM sessions WHERE token = ?");
        $stmt->execute([$token]);
    }

    // Always return success even if token wasn't found (idempotent)
    jsonResponse(['message' => 'Logged out successfully']);

} catch (Throwable $e) {
    error_log("Logout Error: " . $e->getMessage());
    jsonResponse(['error' => 'Logout failed'], 500);
}
?>
