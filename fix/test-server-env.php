<?php
/**
 * Server Environment Test
 * Checks PHP configuration and server environment
 */

header('Content-Type: application/json');

$result = array(
    'timestamp' => date('Y-m-d H:i:s'),
    'test_name' => 'Server Environment Test'
);

// PHP Info
$result['php'] = array(
    'version' => phpversion(),
    'sapi' => php_sapi_name(),
    'os' => PHP_OS,
    'architecture' => PHP_INT_SIZE * 8 . '-bit'
);

// PHP Extensions
$result['extensions'] = array(
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'mysqli' => extension_loaded('mysqli'),
    'json' => extension_loaded('json'),
    'mbstring' => extension_loaded('mbstring'),
    'curl' => extension_loaded('curl'),
    'openssl' => extension_loaded('openssl'),
    'session' => extension_loaded('session')
);

// PHP Settings
$result['settings'] = array(
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'display_errors' => ini_get('display_errors'),
    'error_reporting' => ini_get('error_reporting'),
    'log_errors' => ini_get('log_errors'),
    'error_log' => ini_get('error_log'),
    'default_charset' => ini_get('default_charset'),
    'date_timezone' => ini_get('date.timezone')
);

// Server Variables
$result['server'] = array(
    'software' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'unknown',
    'protocol' => isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'unknown',
    'document_root' => isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'unknown',
    'script_filename' => isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : 'unknown'
);

// Paths
$result['paths'] = array(
    'current_dir' => __DIR__,
    'parent_dir' => dirname(__DIR__),
    'api_dir' => dirname(__DIR__) . '/api',
    'fix_dir' => __DIR__
);

// Permissions
$result['permissions'] = array(
    'current_dir_writable' => is_writable(__DIR__),
    'parent_dir_writable' => is_writable(dirname(__DIR__)),
    'api_dir_exists' => is_dir(dirname(__DIR__) . '/api'),
    'api_dir_writable' => is_writable(dirname(__DIR__) . '/api')
);

// Functions availability
$result['functions'] = array(
    'json_encode' => function_exists('json_encode'),
    'json_decode' => function_exists('json_decode'),
    'file_get_contents' => function_exists('file_get_contents'),
    'file_put_contents' => function_exists('file_put_contents'),
    'session_start' => function_exists('session_start'),
    'header' => function_exists('header'),
    'http_response_code' => function_exists('http_response_code')
);

// Memory
$result['memory'] = array(
    'current_usage' => memory_get_usage(true),
    'current_usage_formatted' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
    'peak_usage' => memory_get_peak_usage(true),
    'peak_usage_formatted' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB'
);

// Overall health check
$result['health_check'] = array(
    'php_version_ok' => version_compare(PHP_VERSION, '7.0.0', '>='),
    'pdo_available' => extension_loaded('pdo') && extension_loaded('pdo_mysql'),
    'json_available' => extension_loaded('json'),
    'api_dir_accessible' => is_dir(dirname(__DIR__) . '/api') && is_readable(dirname(__DIR__) . '/api')
);

$result['overall_status'] = 
    $result['health_check']['php_version_ok'] &&
    $result['health_check']['pdo_available'] &&
    $result['health_check']['json_available'] &&
    $result['health_check']['api_dir_accessible'] ? 'HEALTHY' : 'ISSUES_FOUND';

echo json_encode($result, JSON_PRETTY_PRINT);
?>
