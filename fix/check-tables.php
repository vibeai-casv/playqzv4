<?php
/**
 * Check Required Tables
 * Upload to: /fix/check-tables.php
 * Access: https://aiquiz.vibeai.cv/fix/check-tables.php
 */

header('Content-Type: application/json');

$apiDir = dirname(__DIR__) . '/api';

require_once $apiDir . '/config.php';
require_once $apiDir . '/db.php';

$required_tables = [
    'users',
    'sessions',
    'profiles',
    'questions',
    'quiz_attempts',
    'quiz_responses',
    'media_library',
    'user_activity_logs'
];

$results = [];
$missing = [];
$existing = [];

foreach ($required_tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $result = $stmt->fetch();
        $results[$table] = [
            'exists' => true,
            'row_count' => $result['count']
        ];
        $existing[] = $table;
    } catch (PDOException $e) {
        $results[$table] = [
            'exists' => false,
            'error' => $e->getMessage()
        ];
        $missing[] = $table;
    }
}

echo json_encode([
    'status' => count($missing) === 0 ? 'success' : 'error',
    'total_required' => count($required_tables),
    'existing_count' => count($existing),
    'missing_count' => count($missing),
    'missing_tables' => $missing,
    'tables' => $results
], JSON_PRETTY_PRINT);
?>
