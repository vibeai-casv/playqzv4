<?php
/**
 * Simple Database Connection Test
 * Upload to: /fix/test-db.php
 * Access: https://aiquiz.vibeai.cv/fix/test-db.php
 */

header('Content-Type: application/json');

// Try to load config from /api/ directory
$configPath = dirname(__DIR__) . '/api/config.php';

if (!file_exists($configPath)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'config.php not found',
        'path_checked' => $configPath,
        'parent_dir' => dirname(__DIR__)
    ]);
    exit;
}

require_once $configPath;

// Check if constants are defined
$config_check = [
    'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NOT DEFINED',
    'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NOT DEFINED',
    'DB_USER' => defined('DB_USER') ? DB_USER : 'NOT DEFINED',
    'DB_PASS' => defined('DB_PASS') ? (DB_PASS ? '***SET***' : 'EMPTY') : 'NOT DEFINED'
];

// Try to connect
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Get database name
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    
    // Count tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection successful!',
        'config' => $config_check,
        'connected_to' => $result['db_name'],
        'table_count' => count($tables),
        'tables' => $tables
    ], JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed',
        'error' => $e->getMessage(),
        'config' => $config_check
    ], JSON_PRETTY_PRINT);
}
?>
