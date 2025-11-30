<?php
/**
 * Test Login Endpoint
 * Upload to: /fix/test-login.php
 * Access: https://aiquiz.vibeai.cv/fix/test-login.php
 * 
 * Tests the login functionality
 */

header('Content-Type: application/json');

// Simulate a login request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'email' => 'vibeaicasv@gmail.com',
    'password' => 'password123'
];

// Capture output
ob_start();

$apiDir = dirname(__DIR__) . '/api';
$loginFile = $apiDir . '/auth/login.php';

try {
    // Include the login script
    if (file_exists($loginFile)) {
        include $loginFile;
        $output = ob_get_clean();
        
        echo json_encode([
            'status' => 'test_completed',
            'message' => 'Login endpoint executed',
            'output' => $output,
            'output_decoded' => json_decode($output, true)
        ], JSON_PRETTY_PRINT);
    } else {
        ob_end_clean();
        echo json_encode([
            'status' => 'error',
            'message' => 'login.php not found',
            'path_checked' => $loginFile
        ], JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'Exception occurred',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
?>
