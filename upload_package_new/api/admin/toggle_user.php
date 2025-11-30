<?php
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

if ($session['role'] !== 'admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$input = getJsonInput();
$userId = $input['userId'] ?? null;
$disabled = $input['disabled'] ?? null;

if (!$userId || is_null($disabled)) {
    jsonResponse(['error' => 'Missing userId or disabled status'], 400);
}

try {
    $stmt = $pdo->prepare("UPDATE profiles SET disabled = ? WHERE id = ?");
    $stmt->execute([$disabled ? 1 : 0, $userId]);

    // Log the action
    $logStmt = $pdo->prepare("INSERT INTO user_activity_logs (id, user_id, activity_type, description, related_entity_id, success) VALUES (?, ?, ?, ?, ?, 1)");
    $logStmt->execute([
        generateUuid(), 
        $session['user_id'], 
        $disabled ? 'account_disabled' : 'account_enabled', 
        "User " . ($disabled ? "disabled" : "enabled") . " by admin", 
        $userId
    ]);

    jsonResponse(['message' => 'User status updated successfully']);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
