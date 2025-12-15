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

// Fetch current question to validate and get text if needed
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$id]);
$currentQuestion = $stmt->fetch();

if (!$currentQuestion) {
    jsonResponse(['error' => 'Question not found'], 404);
}

// Map 'status' to 'is_active' if provided
if (isset($input['status'])) {
    $input['is_active'] = ($input['status'] === 'active') ? 1 : 0;
}

// Determine new state
$newIsActive = isset($input['is_active']) ? (int)$input['is_active'] : (int)$currentQuestion['is_active'];
$newText = isset($input['question_text']) ? $input['question_text'] : $currentQuestion['question_text'];

// Check for image requirement on activation
$typeToCheck = isset($input['question_type']) ? $input['question_type'] : $currentQuestion['question_type'];
$imageToCheck = isset($input['image_url']) ? $input['image_url'] : $currentQuestion['image_url'];

if ($newIsActive === 1 && in_array($typeToCheck, ['image_identify_logo', 'image_identify_person'])) {
    if (empty($imageToCheck)) {
        jsonResponse(['error' => 'Active image identification questions must have an image.'], 400);
    }
}

// Check for duplicates if the question is (or becoming) active
// Only check if we are changing status to active OR changing text of an active question
$statusChangingToActive = ($newIsActive === 1 && $currentQuestion['is_active'] == 0);
$textChangingOfActive = ($newIsActive === 1 && isset($input['question_text']) && $input['question_text'] !== $currentQuestion['question_text']);

if ($statusChangingToActive || $textChangingOfActive) {
    $stmt = $pdo->prepare("SELECT id FROM questions WHERE question_text = ? AND is_active = 1 AND id != ?");
    $stmt->execute([$newText, $id]);
    if ($stmt->fetch()) {
        jsonResponse(['error' => 'An active question with this text already exists.'], 409);
    }
}

// Build update query dynamically
// Removed 'status' from allowed fields as it maps to is_active
$allowedFields = [
    'question_text', 'question_type', 'options', 'correct_answer', 
    'explanation', 'difficulty', 'category', 'points', 'media_id', 'image_url', 
    'is_active', 'is_verified', 'is_demo'
];

$updates = [];
$params = [];

// If image_url is updated, clear media_id to ensure we use the explicit URL
if (array_key_exists('image_url', $input) && !array_key_exists('media_id', $input)) {
    $input['media_id'] = null;
}

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

    // Add virtual status field for frontend compatibility
    $question['status'] = $question['is_active'] ? 'active' : 'draft';

    jsonResponse(['data' => $question]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
