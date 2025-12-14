<?php
/**
 * Test JSON Import Diagnostic
 */

header('Content-Type: application/json');
require_once '../config.php';
require_once '../db.php';

// Test data
$testQuestion = [
    "type" => "text_mcq",
    "category" => "Test",
    "difficulty" => "easy",
    "question" => "Test question " . time(),
    "options" => ["A", "B", "C", "D"],
    "correct_answer" => "A"
];

echo json_encode([
    'test' => 'JSON Import Diagnostic',
    'database_connected' => isset($pdo),
    'test_question' => $testQuestion,
    'test_json' => json_encode([$testQuestion]),
    'config' => [
        'DB_NAME' => DB_NAME,
        'ALLOWED_ORIGIN' => ALLOWED_ORIGIN
    ]
], JSON_PRETTY_PRINT);
?>
