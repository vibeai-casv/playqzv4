<?php
/**
 * Database Schema Update API Endpoint
 * 
 * This endpoint updates the database schema with any missing fields or indexes.
 * Safe to run multiple times - it only adds what's missing.
 * 
 * Access: POST /api/admin/update-schema.php
 */

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Debug logging function
function debugLog($message) {
    error_log("[Schema Update] " . $message);
}

debugLog("Script started - " . date('Y-m-d H:i:s'));
debugLog("Request method: " . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
debugLog("Script path: " . __FILE__);
debugLog("Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'UNKNOWN'));

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    debugLog("OPTIONS request - sending CORS headers");
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debugLog("Invalid method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'error' => 'Method not allowed',
        'debug' => [
            'method_received' => $_SERVER['REQUEST_METHOD'],
            'allowed_methods' => ['POST'],
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
    exit;
}

debugLog("Attempting to locate project root...");
$projectRoot = dirname(dirname(__DIR__));
debugLog("Project root: " . $projectRoot);

$configPath = $projectRoot . '/api/config.php';
$dbPath = $projectRoot . '/api/db.php';

debugLog("Config path: " . $configPath);
debugLog("DB path: " . $dbPath);
debugLog("Config exists: " . (file_exists($configPath) ? 'YES' : 'NO'));
debugLog("DB exists: " . (file_exists($dbPath) ? 'YES' : 'NO'));

if (!file_exists($configPath)) {
    debugLog("ERROR: config.php not found");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Configuration file not found',
        'debug' => [
            'searched_path' => $configPath,
            'current_dir' => __DIR__,
            'project_root' => $projectRoot
        ]
    ]);
    exit;
}

require_once $configPath;
debugLog("Config loaded successfully");

if (!file_exists($dbPath)) {
    debugLog("ERROR: db.php not found");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection file not found',
        'debug' => [
            'searched_path' => $dbPath
        ]
    ]);
    exit;
}

require_once $dbPath;
debugLog("DB file loaded successfully");

// Track all changes
$allChanges = [];
$tablesChecked = 0;
$fieldsChecked = 0;
$fieldsAdded = 0;
$indexesAdded = 0;

// Define required schema
$requiredSchema = [
    'questions' => [
        'is_demo' => [
            'type' => 'TINYINT(1)',
            'default' => '0',
            'after' => 'ai_prompt',
            'description' => 'Flag for demo questions'
        ]
    ],
    // Add more required fields here as needed
];

debugLog("Starting schema update process...");

try {
    debugLog("PDO object status: " . (isset($pdo) ? 'EXISTS' : 'NOT SET'));
    
    // Check and update each table
    foreach ($requiredSchema as $tableName => $requiredFields) {
        $tablesChecked++;
        debugLog("Checking table: $tableName");
        
        // Get current table structure
        $stmt = $pdo->query("SHOW COLUMNS FROM $tableName");
        $currentColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $currentColumnNames = array_column($currentColumns, 'Field');
        debugLog("  Current columns in $tableName: " . count($currentColumnNames));
        
        // Check each required field
        foreach ($requiredFields as $fieldName => $fieldConfig) {
            $fieldsChecked++;
            debugLog("  Checking field: $fieldName");
            
            if (!in_array($fieldName, $currentColumnNames)) {
                // Field missing - add it
                debugLog("  Field $fieldName is missing - adding...");
                $sql = "ALTER TABLE $tableName ADD COLUMN $fieldName {$fieldConfig['type']} DEFAULT {$fieldConfig['default']}";
                if (isset($fieldConfig['after'])) {
                    $sql .= " AFTER {$fieldConfig['after']}";
                }
                
                debugLog("  SQL: $sql");
                $pdo->exec($sql);
                $fieldsAdded++;
                
                $change = "Added field '$fieldName' to table '$tableName'";
                $allChanges[] = $change;
                debugLog("  SUCCESS: $change");
            } else {
                debugLog("  Field $fieldName already exists");
            }
        }
    }
    
    // Add required indexes
    $requiredIndexes = [
        'questions' => [
            'idx_is_demo' => ['columns' => ['is_demo'], 'description' => 'Demo questions filter']
        ]
    ];
    
    debugLog("Checking indexes...");
    foreach ($requiredIndexes as $tableName => $indexes) {
        foreach ($indexes as $indexName => $indexConfig) {
            debugLog("  Checking index: $indexName on $tableName");
            $stmt = $pdo->query("SHOW INDEX FROM $tableName WHERE Key_name = '$indexName'");
            $indexExists = $stmt->fetch();
            
            if (!$indexExists) {
                debugLog("  Index $indexName missing - adding...");
                $columns = implode(', ', $indexConfig['columns']);
                $sql = "ALTER TABLE $tableName ADD INDEX $indexName ($columns)";
                debugLog("  SQL: $sql");
                $pdo->exec($sql);
                
                $indexesAdded++;
                $change = "Added index '$indexName' on $tableName ({$indexConfig['description']})";
                $allChanges[] = $change;
                debugLog("  SUCCESS: $change");
            } else {
                debugLog("  Index $indexName already exists");
            }
        }
    }
    
    debugLog("Schema update completed successfully");
    
    // Return success with stats
    echo json_encode([
        'success' => true,
        'changes' => $allChanges,
        'stats' => [
            'tablesChecked' => $tablesChecked,
            'fieldsChecked' => $fieldsChecked,
            'fieldsAdded' => $fieldsAdded,
            'indexesAdded' => $indexesAdded
        ],
        'message' => empty($allChanges) 
            ? 'Schema is up to date - no changes needed' 
            : 'Schema updated successfully',
        'debug' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => phpversion(),
            'script_path' => __FILE__
        ]
    ]);
    
} catch (PDOException $e) {
    debugLog("PDO Exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'debug' => [
            'error_type' => 'PDOException',
            'error_code' => $e->getCode(),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    debugLog("General Exception: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'error_type' => get_class($e),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
}
?>
