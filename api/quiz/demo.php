<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

// No authentication required for demo

$numQuestions = 5;

// Build query to select random questions
try {
    // Try to get questions marked as demo first
    $sql = "SELECT * FROM questions WHERE is_demo = 1 AND is_active = 1 ORDER BY RAND() LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([(int)$numQuestions]);
    $questions = $stmt->fetchAll();

    // If no demo questions found, fallback to random active questions
    if (empty($questions)) {
        $sql = "SELECT * FROM questions WHERE is_active = 1 ORDER BY RAND() LIMIT ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$numQuestions]);
        $questions = $stmt->fetchAll();
    }

    if (empty($questions)) {
        jsonResponse(['error' => 'No questions found'], 404);
    }

    // Parse JSON fields in questions
    foreach ($questions as &$q) {
        $q['options'] = json_decode($q['options']);
        $q['tags'] = json_decode($q['tags']);
        // Remove correct answer from client-side response for security? 
        // For a demo, it's fine to send it if we validate on client, 
        // OR we can keep it hidden and validate on server. 
        // But since we want a standalone demo without session, client-side validation is easiest.
        // We'll send it but maybe the frontend won't peek.
    }

    jsonResponse([
        'questions' => $questions
    ]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
