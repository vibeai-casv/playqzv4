<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

// Get fresh profile data
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE id = ?");
$stmt->execute([$session['user_id']]);
$profile = $stmt->fetch();

jsonResponse([
    'user' => [
        'id' => $session['user_id'],
        'email' => $session['email'],
        'role' => $profile['role'] ?? 'user',
        'profile' => $profile
    ]
]);
?>
