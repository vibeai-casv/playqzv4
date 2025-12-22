<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);
$userId = $session['user_id'];

try {
    // Calculate stats
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_attempts,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_attempts,
            AVG(CASE WHEN status = 'completed' THEN score ELSE NULL END) as average_score,
            MAX(CASE WHEN status = 'completed' THEN score ELSE 0 END) as best_score,
            SUM(time_spent_seconds) as total_time_spent
        FROM quiz_attempts 
        WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate completion rate
    $stats['completion_rate'] = $stats['total_attempts'] > 0 
        ? ($stats['completed_attempts'] / $stats['total_attempts']) * 100 
        : 0;

    // Fetch recent attempts
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$userId]);
    $recentAttempts = $stmt->fetchAll();

    foreach ($recentAttempts as &$attempt) {
        $attempt['config'] = json_decode($attempt['config']);
        $attempt['question_ids'] = json_decode($attempt['question_ids']);
    }

    jsonResponse(['stats' => $stats, 'recentAttempts' => $recentAttempts]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
