<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CHECKING MISSING IMAGE REFERENCES ===\n\n";
    
    // Check for the specific missing images
    $stmt = $db->query("
        SELECT id, question_text, correct_answer, image_url, type 
        FROM questions 
        WHERE image_url LIKE '%69474d%' OR image_url LIKE '%69468%'
    ");
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($results) > 0) {
        foreach ($results as $q) {
            echo "Question ID: {$q['id']}\n";
            echo "Type: {$q['type']}\n";
            echo "Question: {$q['question_text']}\n";
            echo "Correct Answer: {$q['correct_answer']}\n";
            echo "Current Image URL: {$q['image_url']}\n";
            echo str_repeat('-', 80) . "\n\n";
        }
    } else {
        echo "No questions found with those image URLs.\n";
    }
    
    // Also check all personality questions
    echo "\n=== ALL IDENTIFY_PERSONALITY QUESTIONS ===\n\n";
    $stmt = $db->query("SELECT id, correct_answer, image_url FROM questions WHERE type = 'identify_personality'");
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($all as $q) {
        $imagePath = 'E:/projects/playqzv4' . $q['image_url'];
        $exists = file_exists($imagePath) ? '✓' : '✗';
        echo "{$exists} {$q['correct_answer']} - {$q['image_url']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
