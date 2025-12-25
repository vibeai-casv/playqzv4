<?php
require_once 'api/config.php';
require_once 'api/db.php';

try {
    $stmt = $pdo->query("SELECT question_type, COUNT(*) as count FROM questions WHERE is_active = 1 GROUP BY question_type");
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($stats, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    if ($e->getCode() == 2002) {
         echo "DB_CONNECTION_FAILED";
    } else {
         echo "Error: " . $e->getMessage();
    }
}
