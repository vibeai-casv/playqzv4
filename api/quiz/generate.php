<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);
$input = getJsonInput();

$numQuestions = $input['numQuestions'] ?? 10;
$difficulty = $input['difficulty'] ?? 'medium';
$categories = $input['categories'] ?? []; // Array of strings
$timeLimit = $input['timeLimit'] ?? 60;

// Build query to select random questions
$sql = "SELECT id FROM questions WHERE is_active = 1";
$params = [];

if ($difficulty !== 'mixed') {
    $sql .= " AND difficulty = ?";
    $params[] = $difficulty;
}

if (!empty($categories)) {
    $placeholders = implode(',', array_fill(0, count($categories), '?'));
    $sql .= " AND category IN ($placeholders)";
    $params = array_merge($params, $categories);
}

$types = $input['types'] ?? []; // Array of strings e.g. ['image_identify_person']
if (!empty($types)) {
    $placeholders = implode(',', array_fill(0, count($types), '?'));
    $sql .= " AND question_type IN ($placeholders)";
    $params = array_merge($params, $types);
}

$sql .= " ORDER BY RAND() LIMIT ?";
$params[] = (int)$numQuestions;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$questionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($questionIds)) {
    jsonResponse(['error' => 'No questions found matching criteria'], 404);
}

// Create Attempt
$attemptId = generateUuid();
$config = json_encode([
    'numQuestions' => $numQuestions,
    'difficulty' => $difficulty,
    'categories' => $categories,
    'types' => $types,
    'timeLimit' => $timeLimit
]);
$questionIdsJson = json_encode($questionIds);

try {
    $stmt = $pdo->prepare("INSERT INTO quiz_attempts (
        id, user_id, config, question_ids, total_questions, status
    ) VALUES (?, ?, ?, ?, ?, 'in_progress')");
    
    $stmt->execute([
        $attemptId, $session['user_id'], $config, $questionIdsJson, count($questionIds)
    ]);

    // Fetch full questions
    $placeholders = implode(',', array_fill(0, count($questionIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id IN ($placeholders)");
    $stmt->execute($questionIds);
    $questions = $stmt->fetchAll();

    // Parse JSON fields in questions
    foreach ($questions as &$q) {
        $q['options'] = json_decode($q['options']);
        $q['tags'] = json_decode($q['tags']);
    }

    // Fetch the created attempt
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE id = ?");
    $stmt->execute([$attemptId]);
    $attempt = $stmt->fetch();
    $attempt['config'] = json_decode($attempt['config']);
    $attempt['question_ids'] = json_decode($attempt['question_ids']);

    jsonResponse([
        'attempt' => $attempt,
        'questions' => $questions
    ]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
