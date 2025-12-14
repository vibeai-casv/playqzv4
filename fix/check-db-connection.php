<?php
/**
 * Database Connection Diagnostic Tool
 * Upload this to your server to diagnose database connection issues
 * 
 * Access: https://aiquiz.vibeai.cv/aiq2/fix/check-db-connection.php
 */

header('Content-Type: application/json');

// Try to find the config file
$possibleConfigPaths = [
    __DIR__ . '/../api/config.php',
    dirname(__DIR__) . '/api/config.php',
    '/var/www/aiquiz.vibeai.cv/api/config.php',
    '/var/www/aiquiz.vibeai.cv/aiq2/api/config.php',
];

$configFound = false;
foreach ($possibleConfigPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $configFound = true;
        $configPath = $path;
        break;
    }
}

if (!$configFound) {
    echo json_encode([
        'error' => 'Config file not found',
        'searched_paths' => $possibleConfigPaths,
        'current_dir' => __DIR__,
        'suggestion' => 'Upload config.php to the correct location'
    ], JSON_PRETTY_PRINT);
    exit;
}

// Check if constants are defined
$dbConfig = [
    'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NOT DEFINED',
    'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NOT DEFINED',
    'DB_USER' => defined('DB_USER') ? DB_USER : 'NOT DEFINED',
    'DB_PASS' => defined('DB_PASS') ? (DB_PASS === 'YOUR_PASSWORD_HERE' ? '⚠️ PLACEHOLDER PASSWORD NOT CHANGED!' : '*** (set)') : 'NOT DEFINED',
];

$response = [
    'config_file_path' => $configPath,
    'config_loaded' => true,
    'database_config' => $dbConfig,
    'connection_test' => null,
    'error' => null,
];

// Try to connect
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    $response['connection_test'] = 'SUCCESS ✓';
    
    // Get database version
    $stmt = $pdo->query('SELECT VERSION() as version');
    $version = $stmt->fetch();
    $response['mysql_version'] = $version['version'];
    
    // List tables
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $response['tables_found'] = count($tables);
    $response['tables'] = $tables;
    
    $response['status'] = 'OK - Database connection successful!';
    
} catch (PDOException $e) {
    $response['connection_test'] = 'FAILED ✗';
    $response['error'] = $e->getMessage();
    $response['error_code'] = $e->getCode();
    
    // Provide specific help based on error
    if (strpos($e->getMessage(), '1045') !== false) {
        $response['help'] = [
            'issue' => 'Access denied - Invalid username or password',
            'solutions' => [
                '1. Check your database username and password in hosting control panel',
                '2. Update api/config.php with correct credentials',
                '3. Make sure the database user has permissions on the database',
                '4. If password contains special characters, make sure they are properly escaped'
            ]
        ];
    } elseif (strpos($e->getMessage(), '1049') !== false) {
        $response['help'] = [
            'issue' => 'Unknown database - Database does not exist',
            'solutions' => [
                '1. Create the database in your hosting control panel (cPanel/Plesk)',
                '2. Update DB_NAME in api/config.php to match the actual database name',
                '3. Import schema.sql after creating the database'
            ]
        ];
    } elseif (strpos($e->getMessage(), '2002') !== false) {
        $response['help'] = [
            'issue' => 'Cannot connect to MySQL server',
            'solutions' => [
                '1. Check if MySQL is running on your server',
                '2. Verify DB_HOST is correct (usually "localhost")',
                '3. Contact your hosting provider if problem persists'
            ]
        ];
    }
    
    $response['status'] = 'ERROR - Cannot connect to database';
}

// Check PHP version
$response['php_version'] = phpversion();
$response['pdo_available'] = extension_loaded('pdo');
$response['pdo_mysql_available'] = extension_loaded('pdo_mysql');

echo json_encode($response, JSON_PRETTY_PRINT);
?>
