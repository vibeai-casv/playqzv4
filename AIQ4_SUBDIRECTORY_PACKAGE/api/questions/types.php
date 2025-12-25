<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

// Get unique question types from the database
try {
    $stmt = $pdo->query("
        SELECT DISTINCT question_type 
        FROM questions 
        WHERE question_type IS NOT NULL 
        ORDER BY question_type
    ");
    
    $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    jsonResponse(['types' => $types]);
    
} catch (PDOException $e) {
    jsonResponse(['error' => 'Failed to fetch question types: ' . $e->getMessage()], 500);
}
?>
