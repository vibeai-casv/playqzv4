<?php
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

if ($session['role'] !== 'admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$userId = $_GET['userId'] ?? null;

if (!$userId) {
    jsonResponse(['error' => 'Missing userId'], 400);
}

try {
    // 1. Activity Logs
    $stmt = $pdo->prepare("
        SELECT * FROM user_activity_logs 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 50
    ");
    $stmt->execute([$userId]);
    $activity = $stmt->fetchAll();

    // 2. Quiz Attempts
    $stmt = $pdo->prepare("
        SELECT * FROM quiz_attempts 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 20
    ");
    $stmt->execute([$userId]);
    $attempts = $stmt->fetchAll();

    // 3. Login History (from activity logs for now, or sessions if we tracked history there)
    $stmt = $pdo->prepare("
        SELECT * FROM user_activity_logs 
        WHERE user_id = ? AND activity_type = 'login'
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$userId]);
    $logins = $stmt->fetchAll();

    jsonResponse([
        'activity' => $activity,
        'attempts' => $attempts,
        'logins' => $logins
    ]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
