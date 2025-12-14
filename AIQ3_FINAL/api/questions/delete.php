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

try {
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    
    jsonResponse(['message' => 'Question deleted']);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
