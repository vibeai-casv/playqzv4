<?php
/**
 * Test API Database Connection
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config.php';

$response = [
    'timestamp' => date('Y-m-d H:i:s'),
    'mysql_status' => 'unknown',
    'database' => DB_NAME,
    'connection' => null,
    'error' => null
];

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5
        ]
    );
    
    $response['mysql_status'] = 'running';
    $response['connection'] = 'success';
    
    // Get server info
    $stmt = $pdo->query('SELECT VERSION() as version');
    $response['mysql_version'] = $stmt->fetch()['version'];
    
    // Get uptime
    $stmt = $pdo->query("SHOW STATUS WHERE Variable_name = 'Uptime'");
    $uptime = $stmt->fetch()['Value'];
    $response['server_uptime'] = gmdate("H:i:s", $uptime);
    
    // Test query on users table
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
    $response['user_count'] = $stmt->fetch()['count'];
    
    // Test query on questions table
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM questions');
    $response['question_count'] = $stmt->fetch()['count'];
    
    $response['status'] = 'ok';
    $response['message'] = 'Database connection is working perfectly!';
    
} catch (PDOException $e) {
    $response['mysql_status'] = 'error';
    $response['connection'] = 'failed';
    $response['error'] = $e->getMessage();
    $response['error_code'] = $e->getCode();
    $response['status'] = 'error';
    
    if ($e->getCode() == 2002) {
        $response['message'] = 'MySQL server is not running. Start it in XAMPP Control Panel.';
        $response['solution'] = 'Open XAMPP Control Panel and click Start next to MySQL';
    } elseif ($e->getCode() == 2006) {
        $response['message'] = 'MySQL server has gone away. Try restarting MySQL in XAMPP.';
        $response['solution'] = 'Stop and Start MySQL in XAMPP Control Panel';
    } elseif ($e->getCode() == 1049) {
        $response['message'] = 'Database "aiqz" does not exist.';
        $response['solution'] = 'Create the database in phpMyAdmin';
    } else {
        $response['message'] = 'Unknown database error occurred.';
        $response['solution'] = 'Check XAMPP logs at C:\\xampp\\mysql\\data\\mysql_error.log';
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
