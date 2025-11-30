<?php
/**
 * API Endpoint Test
 * Tests all API endpoints to verify they're accessible
 */

header('Content-Type: application/json');

$apiPath = dirname(dirname(__FILE__)) . '/api';
$result = array(
    'timestamp' => date('Y-m-d H:i:s'),
    'test_name' => 'API Endpoint Test',
    'api_path' => $apiPath
);

// List of API endpoints to check
$endpoints = array(
    'config.php',
    'db.php',
    'utils.php',
    'auth/login.php',
    'auth/register.php',
    'auth/logout.php',
    'quiz/generate.php',
    'quiz/submit.php',
    'quiz/history.php',
    'questions/list.php',
    'questions/import.php',
    'admin/analytics.php',
    'admin/users.php'
);

$result['endpoints'] = array();

foreach ($endpoints as $endpoint) {
    $fullPath = $apiPath . '/' . $endpoint;
    $result['endpoints'][$endpoint] = array(
        'exists' => file_exists($fullPath),
        'readable' => is_readable($fullPath),
        'path' => $fullPath
    );
    
    if (file_exists($fullPath)) {
        $result['endpoints'][$endpoint]['size'] = filesize($fullPath);
        $result['endpoints'][$endpoint]['modified'] = date('Y-m-d H:i:s', filemtime($fullPath));
    }
}

// Check permissions
$result['permissions'] = array(
    'api_readable' => is_readable($apiPath),
    'api_writable' => is_writable($apiPath),
    'questions_dir_exists' => is_dir($apiPath . '/questions'),
    'questions_dir_writable' => is_writable($apiPath . '/questions')
);

// Count missing endpoints
$missing = array_filter($result['endpoints'], function($ep) {
    return !$ep['exists'];
});

$result['summary'] = array(
    'total_endpoints' => count($endpoints),
    'existing' => count($endpoints) - count($missing),
    'missing' => count($missing),
    'missing_list' => array_keys($missing)
);

echo json_encode($result, JSON_PRETTY_PRINT);
?>
