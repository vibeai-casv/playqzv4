<?php
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

// Allow both admin and super_admin
if (!in_array($session['role'], ['admin', 'super_admin'])) {
    jsonResponse(['error' => 'Forbidden'], 403);
}

try {
    $input = getJsonInput();
    $userId = $input['user_id'] ?? null;
    $role = $input['role'] ?? null;

    if (!$userId || !$role) {
        jsonResponse(['error' => 'Missing user_id or role'], 400);
    }

    // Validate role
    if (!in_array($role, ['user', 'admin'])) {
        jsonResponse(['error' => 'Invalid role. Must be "user" or "admin"'], 400);
    }

    // Fetch target user to check their current role
    $stmt = $pdo->prepare("SELECT id, email, role FROM profiles WHERE id = ?");
    $stmt->execute([$userId]);
    $targetUser = $stmt->fetch();

    if (!$targetUser) {
        jsonResponse(['error' => 'User not found'], 404);
    }

    // PROTECTION RULES:

    // 1. Nobody can change the role of the Super Admin (vibeaicasv@gmail.com)
    if ($targetUser['email'] === 'vibeaicasv@gmail.com' || $targetUser['role'] === 'super_admin') {
        jsonResponse(['error' => 'Cannot change role of Super Admin'], 403);
    }

    // 2. Regular Admins cannot change the role of other Admins
    if ($session['role'] === 'admin' && $targetUser['role'] === 'admin') {
        jsonResponse(['error' => 'Admins cannot modify other Admins. Contact Super Admin.'], 403);
    }

    // 3. Only Super Admin can promote someone to Admin (Optional, but good practice)
    // For now, we'll allow admins to promote users to admins, but not demote/edit other admins.
    
    // Update role in profiles table
    $stmt = $pdo->prepare("UPDATE profiles SET role = ? WHERE id = ?");
    $stmt->execute([$role, $userId]);

    jsonResponse(['success' => true, 'message' => "User role updated to $role"]);

} catch (PDOException $e) {
    error_log("Update role error: " . $e->getMessage());
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
