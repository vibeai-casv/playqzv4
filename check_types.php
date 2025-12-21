<?php
$db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');

echo "Checking question types with personality images...\n\n";

$stmt = $db->query("SELECT DISTINCT question_type FROM questions WHERE image_url LIKE '%personality%'");
$types = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Question types found:\n";
foreach ($types as $type) {
    echo "- $type\n";
}

echo "\n\nAll questions with personality images:\n\n";
$stmt = $db->query("SELECT id, question_type, image_url FROM questions WHERE image_url LIKE '%personality%' LIMIT 20");
while ($q = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$q['id']}\n";
    echo "Type: {$q['question_type']}\n";
    echo "Image: {$q['image_url']}\n";
    echo "---\n";
}
?>
