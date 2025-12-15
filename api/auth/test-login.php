<?php
/**
 * Test Login API Endpoint
 * This will help diagnose the 401 error
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/utils.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

cors();

header('Content-Type: application/json');

echo json_encode([
    'test' => 'Login API is reachable',
    'method' => $_SERVER['REQUEST_METHOD'],
    'headers' => getallheaders(),
    'php_input' => file_get_contents('php://input'),
    'post_data' => $_POST,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT);
?>
