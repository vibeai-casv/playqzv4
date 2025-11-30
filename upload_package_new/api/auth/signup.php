<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

try {
    $input = getJsonInput();
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $name = $input['name'] ?? 'User';
    $phone = $input['phone'] ?? null;
    $category = $input['category'] ?? 'student';
    $district = $input['district'] ?? null;
    $institution = $input['institution_name'] ?? null;
    $course = $input['course_of_study'] ?? null;
    $classLevel = $input['class_level'] ?? null;

    if (!$email || !$password) {
        jsonResponse(['error' => 'Email and password are required'], 400);
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(['error' => 'User already exists'], 409);
    }

    $pdo->beginTransaction();

    $userId = generateUuid();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert User
    $stmt = $pdo->prepare("INSERT INTO users (id, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $email, $passwordHash]);

    // Insert Profile
    $stmt = $pdo->prepare("INSERT INTO profiles (id, email, name, phone, category, district, institution, course_of_study, class_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $email, $name, $phone, $category, $district, $institution, $course, $classLevel]);

    // Create Session
    $token = bin2hex(random_bytes(32));
    $sessionId = generateUuid();
    $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

    $stmt = $pdo->prepare("INSERT INTO sessions (id, user_id, token, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$sessionId, $userId, $token, $expiresAt]);

    $pdo->commit();

    // Get Profile
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();

    jsonResponse([
        'token' => $token,
        'user' => [
            'id' => $userId,
            'email' => $email,
            'role' => 'user',
            'profile' => $profile
        ]
    ]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Signup Error: " . $e->getMessage());
    jsonResponse(['error' => 'Registration failed: ' . $e->getMessage()], 500);
}
?>
