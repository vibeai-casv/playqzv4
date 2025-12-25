<?php
require_once 'api/config.php';
require_once 'api/db.php';

try {
    $stmt = $pdo->query("SELECT question_type, COUNT(*) as count FROM questions GROUP BY question_type");
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($stats, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
