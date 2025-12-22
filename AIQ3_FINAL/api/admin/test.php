<?php
/**
 * Simple API Test Endpoint
 * Access: /api/admin/test.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

echo json_encode([
    'status' => 'ok',
    'message' => 'Admin API endpoint is accessible',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'script_path' => __FILE__,
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'UNKNOWN',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
    'files_in_directory' => scandir(__DIR__)
]);
?>
