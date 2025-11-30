<?php
/**
 * Database Connection Test
 * Tests if database is accessible and configured correctly
 */

header('Content-Type: application/json');

$result = array(
    'timestamp' => date('Y-m-d H:i:s'),
    'test_name' => 'Database Connection Test'
);

// Try to load config
try {
    $configPath = dirname(dirname(__FILE__)) . '/api/config.php';
    
    if (!file_exists($configPath)) {
        $result['error'] = 'Config file not found at: ' . $configPath;
        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
    }
    
    require_once $configPath;
    
    $result['config'] = array(
        'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'not defined',
        'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'not defined',
        'DB_USER' => defined('DB_USER') ? DB_USER : 'not defined',
        'DB_PASS' => defined('DB_PASS') ? '***' : 'not defined'
    );
    
    // Try to connect
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $result['connection'] = 'SUCCESS';
    $result['server_info'] = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    
    // Test query
    $stmt = $pdo->query("SELECT DATABASE()");
    $result['current_database'] = $stmt->fetchColumn();
    
    // List tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $result['tables'] = $tables;
    $result['table_count'] = count($tables);
    
    // Check specific tables
    $requiredTables = array('users', 'questions', 'quizzes', 'quiz_attempts', 'user_answers');
    $result['required_tables'] = array();
    
    foreach ($requiredTables as $table) {
        $result['required_tables'][$table] = in_array($table, $tables);
    }
    
    // If questions table exists, get info
    if (in_array('questions', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM questions");
        $result['questions_count'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("DESCRIBE questions");
        $result['questions_structure'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    $result['connection'] = 'FAILED';
    $result['error'] = $e->getMessage();
    $result['error_code'] = $e->getCode();
} catch (Exception $e) {
    $result['error'] = $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
