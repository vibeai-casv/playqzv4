<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

// Optional: Authenticate if you want only logged-in users to see questions
// $session = authenticate($pdo);

$category = $_GET['category'] ?? null;
$difficulty = $_GET['difficulty'] ?? null;
$limit = $_GET['limit'] ?? 50;
$offset = $_GET['offset'] ?? 0;

$search = $_GET['search'] ?? null;
$type = $_GET['type'] ?? null;
$status = $_GET['status'] ?? null;
$ai_generated = $_GET['ai_generated'] ?? null;

$sql = "SELECT q.*, m.url as media_url 
        FROM questions q 
        LEFT JOIN media_library m ON q.media_id = m.id 
        WHERE 1=1";
$params = [];

if ($category) {
    $sql .= " AND q.category = ?";
    $params[] = $category;
}

if ($difficulty) {
    $sql .= " AND q.difficulty = ?";
    $params[] = $difficulty;
}

if ($type) {
    $sql .= " AND q.question_type = ?";
    $params[] = $type;
}

if ($status) {
    if ($status === 'active') {
        $sql .= " AND q.is_active = 1";
    } elseif ($status === 'inactive') {
        $sql .= " AND q.is_active = 0";
    }
    // 'draft' is treated as inactive for now, or ignored if not mapped
}

if ($ai_generated !== null) {
    $sql .= " AND q.ai_generated = ?";
    $params[] = $ai_generated === 'true' ? 1 : 0;
}

if ($search) {
    $sql .= " AND q.question_text LIKE ?";
    $params[] = "%$search%";
}

$sql .= " ORDER BY q.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$questions = $stmt->fetchAll();

// Parse JSON fields
foreach ($questions as &$q) {
    $q['options'] = json_decode($q['options']);
    $q['tags'] = json_decode($q['tags']);
}

jsonResponse(['data' => $questions]);
?>
