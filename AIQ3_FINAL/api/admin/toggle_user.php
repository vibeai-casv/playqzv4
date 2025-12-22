<?php
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

// Allow both admin and super_admin
if (!in_array($session['role'], ['admin', 'super_admin'])) {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$input = getJsonInput();
$userId = $input['userId'] ?? null;
$disabled = $input['disabled'] ?? null;

if (!$userId || is_null($disabled)) {
    jsonResponse(['error' => 'Missing userId or disabled status'], 400);
}

try {
    // Fetch target user to check their role
    $stmt = $pdo->prepare("SELECT id, email, role FROM profiles WHERE id = ?");
    $stmt->execute([$userId]);
    $targetUser = $stmt->fetch();

    if (!$targetUser) {
        jsonResponse(['error' => 'User not found'], 404);
    }

    // PROTECTION RULES:

    // 1. Nobody can disable the Super Admin
    if ($targetUser['email'] === 'vibeaicasv@gmail.com' || $targetUser['role'] === 'super_admin') {
        jsonResponse(['error' => 'Cannot disable Super Admin'], 403);
    }

    // 2. Regular Admins cannot disable other Admins
    if ($session['role'] === 'admin' && $targetUser['role'] === 'admin') {
        jsonResponse(['error' => 'Admins cannot disable other Admins. Contact Super Admin.'], 403);
    }

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
