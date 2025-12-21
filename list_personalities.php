<?php
$db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');

echo "=== CURRENT PERSONALITY QUESTIONS ===\n\n";

$stmt = $db->query("
    SELECT id, options, image_url 
    FROM questions 
    WHERE question_type = 'image_identify_person' OR question_type = 'identify_person'
    ORDER BY id
");

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($results) . " personality questions\n\n";

foreach ($results as $q) {
    $options = json_decode($q['options'], true);
    $person = $options && isset($options['correct']) ? $options['correct'] : 'Unknown';
    
    echo "Person: {$person}\n";
    echo "Current Image: {$q['image_url']}\n";
    echo "ID: {$q['id']}\n";
    echo str_repeat('-', 70) . "\n";
}
?>
