<?php
$db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Finding questions with missing images...\n\n";

$stmt = $db->prepare("SELECT id, question, correct_answer, image_url, type FROM questions WHERE image_url LIKE ? OR image_url LIKE ?");
$stmt->execute(['%69474d339860d1%', '%69468996ef6726%']);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $q) {
    echo "Question ID: {$q['id']}\n";
    echo "Type: {$q['type']}\n";
    echo "Question: {$q['question']}\n";
    echo "Correct Answer (Personality Name): {$q['correct_answer']}\n";
    echo "Image URL: {$q['image_url']}\n";
    echo "Image filename: " . basename($q['image_url']) . "\n";
    echo str_repeat('-', 80) . "\n\n";
}

echo "Total questions with missing images: " . count($results) . "\n";
?>
