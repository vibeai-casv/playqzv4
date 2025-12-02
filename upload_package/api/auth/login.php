<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

try {
    $input = getJsonInput();
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (!$email || !$password) {
        jsonResponse(['error' => 'Email and password are required'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Clean up expired sessions for this user
    if ($user) {
        $cleanupStmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ? AND expires_at < NOW()");
        $cleanupStmt->execute([$user['id']]);
    }

    if ($user && password_verify($password, $user['password_hash'])) {
        // Generate Token
        $token = bin2hex(random_bytes(32));
        $sessionId = generateUuid();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

        $stmt = $pdo->prepare("INSERT INTO sessions (id, user_id, token, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sessionId, $user['id'], $token, $expiresAt]);

        // Get Profile
        $stmt = $pdo->prepare("SELECT * FROM profiles WHERE id = ?");
        $stmt->execute([$user['id']]);
        $profile = $stmt->fetch();

        // Merge profile data into user object
        $userData = array_merge($profile ? $profile : [], [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $profile['role'] ?? 'user'
        ]);

        jsonResponse([
            'token' => $token,
            'user' => $userData
        ]);
    } else {
        jsonResponse(['error' => 'Invalid credentials'], 401);
    }
} catch (Throwable $e) {
    error_log("Login Error: " . $e->getMessage());
    jsonResponse(['error' => 'Login failed: ' . $e->getMessage()], 500);
}
?>
