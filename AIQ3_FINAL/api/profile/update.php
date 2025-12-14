<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);
$input = getJsonInput();

$allowedFields = ['name', 'phone', 'institution', 'category', 'bio', 'district', 'course_of_study', 'class_level'];
$updates = [];
$params = [];

foreach ($input as $key => $value) {
    if (in_array($key, $allowedFields)) {
        $updates[] = "$key = ?";
        $params[] = $value;
    }
}

if (empty($updates)) {
    jsonResponse(['message' => 'No fields to update']);
}

$params[] = $session['user_id'];
$sql = "UPDATE profiles SET " . implode(', ', $updates) . " WHERE id = ?";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Fetch updated profile
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE id = ?");
    $stmt->execute([$session['user_id']]);
    $profile = $stmt->fetch();
    
    // Parse JSON fields
    $profile['preferences'] = json_decode($profile['preferences']);
    $profile['stats'] = json_decode($profile['stats']);

    jsonResponse(['profile' => $profile]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
