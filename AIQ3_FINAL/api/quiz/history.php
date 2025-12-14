<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

try {
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$session['user_id']]);
    $attempts = $stmt->fetchAll();

    // Parse JSON config
    foreach ($attempts as &$attempt) {
        $attempt['config'] = json_decode($attempt['config']);
        $attempt['question_ids'] = json_decode($attempt['question_ids']);
    }

    jsonResponse(['attempts' => $attempts]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
