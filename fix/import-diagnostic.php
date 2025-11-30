<?php
/**
 * JSON Import Diagnostic Tool
 * Tests import functionality and server configuration
 */

header('Content-Type: application/json');

$diagnostics = array(
    'timestamp' => date('Y-m-d H:i:s'),
    'test_name' => 'JSON Import Diagnostic'
);

// Check PHP version
$diagnostics['php_version'] = phpversion();
$diagnostics['php_version_ok'] = version_compare(PHP_VERSION, '7.0.0', '>=');

// Check if files exist
$apiPath = dirname(dirname(__FILE__)) . '/api';
$diagnostics['api_path'] = $apiPath;
$diagnostics['config_exists'] = file_exists($apiPath . '/config.php');
$diagnostics['db_exists'] = file_exists($apiPath . '/db.php');
$diagnostics['utils_exists'] = file_exists($apiPath . '/utils.php');
$diagnostics['import_exists'] = file_exists($apiPath . '/questions/import.php');

// Try to include config
try {
    if ($diagnostics['config_exists']) {
        require_once $apiPath . '/config.php';
        $diagnostics['config_loaded'] = true;
        $diagnostics['db_name'] = defined('DB_NAME') ? DB_NAME : 'not defined';
        $diagnostics['db_host'] = defined('DB_HOST') ? DB_HOST : 'not defined';
        $diagnostics['allowed_origin'] = defined('ALLOWED_ORIGIN') ? ALLOWED_ORIGIN : 'not defined';
    } else {
        $diagnostics['config_loaded'] = false;
        $diagnostics['config_error'] = 'Config file not found';
    }
} catch (Exception $e) {
    $diagnostics['config_error'] = $e->getMessage();
}

// Try to connect to database
try {
    if ($diagnostics['db_exists'] && $diagnostics['config_loaded']) {
        require_once $apiPath . '/db.php';
        $diagnostics['db_connected'] = isset($pdo);
        
        if (isset($pdo)) {
            $diagnostics['db_type'] = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            // Check if questions table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'questions'");
            $diagnostics['questions_table_exists'] = $stmt->rowCount() > 0;
            
            if ($diagnostics['questions_table_exists']) {
                // Get table structure
                $stmt = $pdo->query("DESCRIBE questions");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $diagnostics['questions_columns'] = $columns;
                
                // Count questions
                $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
                $diagnostics['total_questions'] = $stmt->fetchColumn();
            }
        }
    }
} catch (Exception $e) {
    $diagnostics['db_error'] = $e->getMessage();
}

// Check if utils.php loads
try {
    if ($diagnostics['utils_exists']) {
        require_once $apiPath . '/utils.php';
        $diagnostics['utils_loaded'] = true;
        $diagnostics['verifyAuth_exists'] = function_exists('verifyAuth');
    }
} catch (Exception $e) {
    $diagnostics['utils_error'] = $e->getMessage();
}

// Check permissions
$diagnostics['api_writable'] = is_writable($apiPath);
$diagnostics['questions_writable'] = is_writable($apiPath . '/questions');

// Check PHP extensions
$diagnostics['extensions'] = array(
    'pdo' => extension_loaded('pdo'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'json' => extension_loaded('json'),
    'mbstring' => extension_loaded('mbstring')
);

// Check JSON functions
$diagnostics['json_encode_exists'] = function_exists('json_encode');
$diagnostics['json_decode_exists'] = function_exists('json_decode');

// Test JSON encoding
$testData = array('test' => 'data', 'number' => 123);
$diagnostics['json_test'] = json_encode($testData);

// Check error reporting
$diagnostics['error_reporting'] = array(
    'display_errors' => ini_get('display_errors'),
    'log_errors' => ini_get('log_errors'),
    'error_log' => ini_get('error_log')
);

// Check session
$diagnostics['session_available'] = function_exists('session_start');
if ($diagnostics['session_available']) {
    $diagnostics['session_save_path'] = session_save_path();
}

// Overall status
$diagnostics['ready_for_import'] = 
    $diagnostics['php_version_ok'] &&
    $diagnostics['config_loaded'] &&
    isset($diagnostics['db_connected']) && $diagnostics['db_connected'] &&
    isset($diagnostics['questions_table_exists']) && $diagnostics['questions_table_exists'] &&
    $diagnostics['verifyAuth_exists'];

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
?>
