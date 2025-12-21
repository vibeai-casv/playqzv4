<?php
$db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');

echo "=== ALL QUESTIONS WITH IMAGE URLs ===\n\n";

$stmt = $db->query("
    SELECT id, question_type, image_url, options 
    FROM questions 
    WHERE image_url IS NOT NULL AND image_url != ''
    ORDER BY question_type
");

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$missing = [];

foreach ($results as $q) {
    $imagePath = 'E:/projects/playqzv4' . $q['image_url'];
    $exists = file_exists($imagePath);
    
    if (!$exists) {
        $options = json_decode($q['options'], true);
        $answer = $options && isset($options['correct']) ? $options['correct'] : 'Unknown';
        
        echo "✗ MISSING:\n";
        echo "  ID: {$q['id']}\n";
        echo "  Type: {$q['question_type']}\n";
        echo "  Answer: {$answer}\n";
        echo "  Image: {$q['image_url']}\n";
        echo "  Filename: " . basename($q['image_url']) . "\n\n";
        
        $missing[] = [
            'id' => $q['id'],
            'type' => $q['question_type'],
            'answer' => $answer,
            'filename' => basename($q['image_url']),
            'path' => $q['image_url']
        ];
    }
}

echo "=== SUMMARY ===\n";
echo "Total questions with images: " . count($results) . "\n";
echo "Missing images: " . count($missing) . "\n\n";

if (count($missing) > 0) {
    echo "=== ACTION REQUIRED ===\n";
    echo "You need to create/upload these image files:\n\n";
    foreach ($missing as $m) {
        echo "Person/Entity: {$m['answer']}\n";
        echo "Filename: {$m['filename']}\n";
        echo "Upload to: E:\\projects\\playqzv4" . dirname($m['path']) . "\\\n";
        echo str_repeat('-', 70) . "\n";
    }
}
?>
