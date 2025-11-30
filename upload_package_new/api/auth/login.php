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

        jsonResponse([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $profile['role'] ?? 'user',
                'profile' => $profile
            ]
        ]);
    } else {
        jsonResponse(['error' => 'Invalid credentials'], 401);
    }
} catch (Throwable $e) {
    error_log("Login Error: " . $e->getMessage());
    jsonResponse(['error' => 'Login failed: ' . $e->getMessage()], 500);
}
?>
