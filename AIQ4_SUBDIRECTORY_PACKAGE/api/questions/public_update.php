<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

// Allow ALL origins
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: *");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // BYPASS AUTHENTICATION
    // Mock session with admin role to allow updates
    $session = ['role' => 'admin'];

    $input = getJsonInput();
    // Allow ID from GET or Body
    $id = $input['id'] ?? $_GET['id'] ?? null;

    if (!$id) {
        jsonResponse(['error' => 'ID is required'], 400);
    }

    // Fetch current question
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $currentQuestion = $stmt->fetch();

    if (!$currentQuestion) {
        jsonResponse(['error' => 'Question not found'], 404);
    }

    // [Logic from update.php regarding validation omitted for brevity if not strictly needed, 
    // but good to keep core logic]
    
    // Map 'status' to 'is_active' if provided
    if (isset($input['status'])) {
        $input['is_active'] = ($input['status'] === 'active') ? 1 : 0;
    }

    $allowedFields = [
        'question_text', 'question_type', 'options', 'correct_answer', 
        'explanation', 'difficulty', 'category', 'points', 'media_id', 'image_url', 
        'is_active', 'is_verified', 'is_demo'
    ];

    $updates = [];
    $params = [];

    // If image_url is updated, clear media_id
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

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Return updated data
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $question = $stmt->fetch();
    
    if ($question) {
        $question['options'] = json_decode($question['options'] ?? '[]');
        $question['tags'] = json_decode($question['tags'] ?? '[]');
        $question['status'] = $question['is_active'] ? 'active' : 'draft';
    }

    jsonResponse(['data' => $question]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
