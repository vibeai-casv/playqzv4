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
$id = $input['id'] ?? null;

if (!$id) {
    jsonResponse(['error' => 'ID is required'], 400);
}

// Build update query dynamically
$allowedFields = [
    'question_text', 'question_type', 'options', 'correct_answer', 
    'explanation', 'difficulty', 'category', 'points', 'media_id', 
    'status', 'is_active', 'is_verified'
];

$updates = [];
$params = [];

foreach ($input as $key => $value) {
    if (in_array($key, $allowedFields)) {
        $updates[] = "$key = ?";
        if (in_array($key, ['options', 'tags'])) {
            $params[] = is_array($value) ? json_encode($value) : $value;
        } else {
            $params[] = $value;
        }
    }
}

if (empty($updates)) {
    jsonResponse(['message' => 'No fields to update']);
}

$params[] = $id;
$sql = "UPDATE questions SET " . implode(', ', $updates) . " WHERE id = ?";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Fetch updated question
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $question = $stmt->fetch();
    
    // Parse JSON
    $question['options'] = json_decode($question['options']);
    $question['tags'] = json_decode($question['tags']);

    jsonResponse(['data' => $question]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
