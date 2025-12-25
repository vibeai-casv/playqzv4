<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();
$session = authenticate($pdo);

if ($session['role'] !== 'admin' && $session['role'] !== 'super_admin') {
    jsonResponse(['error' => 'Unauthorized'], 403);
}

$input = getJsonInput();
$type = $input['type'] ?? 'all';

try {
    if ($type === 'all') {
        $stmt = $pdo->prepare("UPDATE questions SET is_active = 1 WHERE is_active = 0");
        $stmt->execute();
    } else {
        // Expand types for personality
        $typesToActivate = [$type];
        if ($type === 'image_identify_person') {
            $typesToActivate[] = 'personality';
        }
        
        $placeholders = implode(',', array_fill(0, count($typesToActivate), '?'));
        $stmt = $pdo->prepare("UPDATE questions SET is_active = 1 WHERE question_type IN ($placeholders) AND is_active = 0");
        $stmt->execute($typesToActivate);
    }

    $count = $stmt->rowCount();
    jsonResponse([
        'success' => true,
        'message' => "Activated $count questions of type '$type'.",
        'activated_count' => $count
    ]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
