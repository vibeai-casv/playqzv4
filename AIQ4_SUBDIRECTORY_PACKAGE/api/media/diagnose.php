<?php
// Diagnostic script for media API
// Upload this to: /var/www/aiquiz.vibeai.cv/aiq3/api/media/diagnose.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$diagnostics = [
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Check 1: PHP version
$diagnostics['checks']['php_version'] = [
    'status' => 'ok',
    'value' => PHP_VERSION
];

// Check 2: Config file exists
$configPath = dirname(__DIR__) . '/config.php';
$diagnostics['checks']['config_exists'] = [
    'status' => file_exists($configPath) ? 'ok' : 'error',
    'path' => $configPath,
    'exists' => file_exists($configPath)
];

// Check 3: Load config and connect to database
try {
    require_once $configPath;
    $diagnostics['checks']['config_loaded'] = [
        'status' => 'ok',
        'db_host' => defined('DB_HOST') ? DB_HOST : 'not defined',
        'db_name' => defined('DB_NAME') ? DB_NAME : 'not defined',
        'db_user' => defined('DB_USER') ? DB_USER : 'not defined'
    ];
} catch (Exception $e) {
    $diagnostics['checks']['config_loaded'] = [
        'status' => 'error',
        'error' => $e->getMessage()
    ];
    echo json_encode($diagnostics, JSON_PRETTY_PRINT);
    exit;
}

// Check 4: Database connection
try {
    require_once dirname(__DIR__) . '/db.php';
    $diagnostics['checks']['db_connection'] = [
        'status' => 'ok',
        'message' => 'Connected successfully'
    ];
} catch (Exception $e) {
    $diagnostics['checks']['db_connection'] = [
        'status' => 'error',
        'error' => $e->getMessage()
    ];
    echo json_encode($diagnostics, JSON_PRETTY_PRINT);
    exit;
}

// Check 5: media_library table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'media_library'");
    $tableExists = $stmt->rowCount() > 0;
    
    $diagnostics['checks']['media_library_table'] = [
        'status' => $tableExists ? 'ok' : 'error',
        'exists' => $tableExists,
        'message' => $tableExists ? 'Table exists' : 'Table does NOT exist'
    ];
} catch (Exception $e) {
    $diagnostics['checks']['media_library_table'] = [
        'status' => 'error',
        'error' => $e->getMessage()
    ];
}

// Check 6: Table structure (if exists)
if ($tableExists) {
    try {
        $stmt = $pdo->query("DESCRIBE media_library");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $diagnostics['checks']['table_structure'] = [
            'status' => 'ok',
            'columns' => $columns
        ];
    } catch (Exception $e) {
        $diagnostics['checks']['table_structure'] = [
            'status' => 'error',
            'error' => $e->getMessage()
        ];
    }
    
    // Check 7: Row count
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM media_library");
        $count = $stmt->fetchColumn();
        
        $diagnostics['checks']['media_count'] = [
            'status' => 'ok',
            'total_rows' => (int)$count,
            'active_rows' => $count
        ];
        
        // Get active count
        $stmt = $pdo->query("SELECT COUNT(*) FROM media_library WHERE is_active = 1");
        $activeCount = $stmt->fetchColumn();
        $diagnostics['checks']['media_count']['active_rows'] = (int)$activeCount;
        
    } catch (Exception $e) {
        $diagnostics['checks']['media_count'] = [
            'status' => 'error',
            'error' => $e->getMessage()
        ];
    }
}

// Check 8: List all tables in database
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $diagnostics['checks']['all_tables'] = [
        'status' => 'ok',
        'tables' => $tables,
        'count' => count($tables)
    ];
} catch (Exception $e) {
    $diagnostics['checks']['all_tables'] = [
        'status' => 'error',
        'error' => $e->getMessage()
    ];
}

// Check 9: Utils file exists
$utilsPath = dirname(__DIR__) . '/utils.php';
$diagnostics['checks']['utils_exists'] = [
    'status' => file_exists($utilsPath) ? 'ok' : 'error',
    'path' => $utilsPath,
    'exists' => file_exists($utilsPath)
];

// Summary
$errorCount = 0;
foreach ($diagnostics['checks'] as $check) {
    if ($check['status'] === 'error') {
        $errorCount++;
    }
}

$diagnostics['summary'] = [
    'total_checks' => count($diagnostics['checks']),
    'errors' => $errorCount,
    'overall_status' => $errorCount === 0 ? 'HEALTHY' : 'ISSUES FOUND'
];

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
?>
