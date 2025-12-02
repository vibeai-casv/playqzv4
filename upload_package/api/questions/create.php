<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

// Check if admin
if ($session['role'] !== 'admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$input = getJsonInput();

// Basic validation
if (empty($input['question_text']) || empty($input['correct_answer'])) {
    jsonResponse(['error' => 'Missing required fields'], 400);
}

$id = generateUuid();
$questionText = $input['question_text'];
$type = $input['question_type'] ?? 'text_mcq';
$options = isset($input['options']) ? json_encode($input['options']) : null;
$correctAnswer = $input['correct_answer'];
$explanation = $input['explanation'] ?? null;
$difficulty = $input['difficulty'] ?? 'medium';
$category = $input['category'] ?? 'General';
$points = $input['points'] ?? 10;
$mediaId = $input['media_id'] ?? null;

try {
    $stmt = $pdo->prepare("INSERT INTO questions (
        id, question_text, question_type, options, correct_answer, 
        explanation, difficulty, category, points, media_id, created_by
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $id, $questionText, $type, $options, $correctAnswer,
        $explanation, $difficulty, $category, $points, $mediaId, $session['user_id']
    ]);

    jsonResponse(['message' => 'Question created', 'id' => $id], 201);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
