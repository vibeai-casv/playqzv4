<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

authenticate($pdo); // Only admins

try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM questions WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    jsonResponse(['success' => true, 'categories' => $categories]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
