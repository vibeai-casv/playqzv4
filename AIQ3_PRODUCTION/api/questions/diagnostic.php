<?php
/**
 * Import Diagnostic - Check Server Configuration
 */

header('Content-Type: application/json');

$diagnostics = array();

// Check PHP version
$diagnostics['php_version'] = phpversion();
$diagnostics['php_version_ok'] = version_compare(PHP_VERSION, '7.0.0', '>=');

// Check if files exist
$diagnostics['config_exists'] = file_exists('../config.php');
$diagnostics['db_exists'] = file_exists('../db.php');
$diagnostics['utils_exists'] = file_exists('../utils.php');

// Try to include config
try {
    if ($diagnostics['config_exists']) {
        require_once '../config.php';
        $diagnostics['config_loaded'] = true;
        $diagnostics['db_name'] = defined('DB_NAME') ? DB_NAME : 'not defined';
        $diagnostics['allowed_origin'] = defined('ALLOWED_ORIGIN') ? ALLOWED_ORIGIN : 'not defined';
    } else {
        $diagnostics['config_loaded'] = false;
    }
} catch (Exception $e) {
    $diagnostics['config_error'] = $e->getMessage();
}

// Try to connect to database
try {
    if ($diagnostics['db_exists']) {
        require_once '../db.php';
        $diagnostics['db_connected'] = isset($pdo);
        if (isset($pdo)) {
            $diagnostics['db_type'] = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        }
    }
} catch (Exception $e) {
    $diagnostics['db_error'] = $e->getMessage();
}

// Check if utils.php loads
try {
    if ($diagnostics['utils_exists']) {
        require_once '../utils.php';
        $diagnostics['utils_loaded'] = true;
        $diagnostics['verifyAuth_exists'] = function_exists('verifyAuth');
    }
} catch (Exception $e) {
    $diagnostics['utils_error'] = $e->getMessage();
}

// Check write permissions
$diagnostics['can_write'] = is_writable(dirname(__FILE__));

// Check error log
$diagnostics['error_log_path'] = ini_get('error_log');
$diagnostics['display_errors'] = ini_get('display_errors');
$diagnostics['log_errors'] = ini_get('log_errors');

// Check JSON functions
$diagnostics['json_encode_exists'] = function_exists('json_encode');
$diagnostics['json_decode_exists'] = function_exists('json_decode');

// Check PDO
$diagnostics['pdo_available'] = class_exists('PDO');
$diagnostics['pdo_drivers'] = PDO::getAvailableDrivers();

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
?>
