<?php
require_once 'config.php';
require_once 'db.php';
require_once 'utils.php';

try {
    echo "Setting up test admin...\n";
    $email = 'testadmin@example.com';
    $password = 'password123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $id = generateUuid();

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        $id = $existing['id'];
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hash, $id]);
        echo "User updated.\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (id, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$id, $email, $hash]);
        echo "User created.\n";
    }

    // Upsert Profile
    $stmt = $pdo->prepare("SELECT id FROM profiles WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE profiles SET role = 'admin' WHERE id = ?");
        $stmt->execute([$id]);
        echo "Profile updated to admin.\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO profiles (id, email, name, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$id, $email, 'Test Admin']);
        echo "Profile created as admin.\n";
    }
    
    // Create a dummy question if none exists
    $stmt = $pdo->query("SELECT count(*) FROM questions");
    if ($stmt->fetchColumn() == 0) {
        echo "Creating dummy question...\n";
        $qId = generateUuid();
        $stmt = $pdo->prepare("INSERT INTO questions (id, question_text, question_type, options, correct_answer, difficulty, category, created_by, is_active, is_demo) VALUES (?, ?, 'text_mcq', ?, ?, 'easy', 'general', ?, 1, 0)");
        $options = json_encode(['A', 'B', 'C', 'D']);
        $stmt->execute([$qId, 'Test Question 1', $options, 'A', $id]);
        echo "Dummy question created.\n";
    } else {
        echo "Questions already exist.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
