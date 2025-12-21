<?php
// Fix missing images - standalone version
$db = new PDO('mysql:host=localhost;dbname=aiqz', 'root', '');

echo "=== CHECKING FOR QUESTIONS WITH MISSING IMAGES ===\n\n";

// Find all questions with images
$stmt = $db->query("SELECT id, question, image_url, type FROM questions WHERE image_url IS NOT NULL AND image_url != ''");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$missing = [];
$found = 0;

foreach ($questions as $q) {
    // Check if file exists
    $imagePath = 'e:/projects/playqzv4' . $q['image_url'];
    
    if (!file_exists($imagePath)) {
        $missing[] = $q;
        echo "❌ MISSING: {$q['image_url']}\n";
        echo "   Question ID: {$q['id']}\n";
        echo "   Type: {$q['type']}\n";
        echo "   Question: " . substr($q['question'], 0, 60) . "...\n\n";
    } else {
        $found++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total questions with images: " . count($questions) . "\n";
echo "Images found: $found\n";
echo "Images missing: " . count($missing) . "\n\n";

if (count($missing) > 0) {
    echo "=== SQL TO DELETE QUESTIONS WITH MISSING IMAGES ===\n";
    foreach ($missing as $q) {
        echo "DELETE FROM questions WHERE id = {$q['id']}; -- {$q['image_url']}\n";
    }
    
    echo "\n=== OR RUN THIS TO DELETE ALL AT ONCE ===\n";
    $ids = array_map(function($q) { return $q['id']; }, $missing);
    echo "DELETE FROM questions WHERE id IN (" . implode(',', $ids) . ");\n";
}
?>
