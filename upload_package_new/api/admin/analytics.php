<?php
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

if ($session['role'] !== 'admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

try {
    // 1. Total Users
    $stmt = $pdo->query("SELECT COUNT(*) FROM profiles");
    $totalUsers = $stmt->fetchColumn();

    // 2. Active Users (logged in last 30 days)
    $stmt = $pdo->query("SELECT COUNT(*) FROM profiles WHERE last_login_at > DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $activeUsers = $stmt->fetchColumn();

    // 3. Total Quizzes (Attempts)
    $stmt = $pdo->query("SELECT COUNT(*) FROM quiz_attempts");
    $totalAttempts = $stmt->fetchColumn();

    // 4. Completed Attempts
    $stmt = $pdo->query("SELECT COUNT(*) FROM quiz_attempts WHERE status = 'completed'");
    $completedAttempts = $stmt->fetchColumn();

    // 5. Average Score (of completed attempts)
    $stmt = $pdo->query("SELECT AVG(score) FROM quiz_attempts WHERE status = 'completed'");
    $averageScore = $stmt->fetchColumn();

    // 6. Category Stats
    $stmt = $pdo->query("
        SELECT 
            JSON_UNQUOTE(JSON_EXTRACT(config, '$.categories[0]')) as category, 
            COUNT(*) as attempts,
            AVG(score) as averageScore
        FROM quiz_attempts 
        WHERE status = 'completed'
        GROUP BY category
    ");
    $categoryStats = $stmt->fetchAll();

    // 7. User Growth (Last 7 days)
    $stmt = $pdo->query("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM profiles 
        WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $userGrowth = $stmt->fetchAll();

    jsonResponse([
        'totalUsers' => (int)$totalUsers,
        'activeUsers' => (int)$activeUsers,
        'totalAttempts' => (int)$totalAttempts,
        'completedAttempts' => (int)$completedAttempts,
        'averageScore' => round((float)$averageScore, 2),
        'categoryStats' => $categoryStats,
        'userGrowth' => $userGrowth
    ]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
