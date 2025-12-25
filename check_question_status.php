<?php
require_once 'api/config.php';
require_once 'api/db.php';

try {
    $stmt = $pdo->query("
        SELECT 
            question_type, 
            is_active, 
            COUNT(*) as count 
        FROM questions 
        WHERE question_type IN ('image_identify_person', 'personality', 'image_identify_logo', 'text_mcq')
        GROUP BY question_type, is_active
        ORDER BY question_type, is_active
    ");
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($stats, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
