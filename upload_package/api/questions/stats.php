<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

try {
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM questions WHERE is_active = 1 GROUP BY category");
    $stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Returns ['Category' => count, ...]

    jsonResponse(['stats' => $stats]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
