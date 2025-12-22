<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

$id = $_GET['id'] ?? null;

if (!$id) {
    jsonResponse(['error' => 'Attempt ID is required'], 400);
}

try {
    // Fetch attempt
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $session['user_id']]);
    $attempt = $stmt->fetch();

    if (!$attempt) {
        jsonResponse(['error' => 'Attempt not found'], 404);
    }

    // Parse JSON
    $attempt['config'] = json_decode($attempt['config']);
    $questionIds = json_decode($attempt['question_ids']);

    // Fetch questions
    if (!empty($questionIds)) {
        $placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE id IN ($placeholders)");
        $stmt->execute($questionIds);
        $questions = $stmt->fetchAll();

        // Parse JSON in questions
        foreach ($questions as &$q) {
            $q['options'] = json_decode($q['options']);
            $q['tags'] = json_decode($q['tags']);
        }
    } else {
        $questions = [];
    }

    jsonResponse(['attempt' => $attempt, 'questions' => $questions]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
