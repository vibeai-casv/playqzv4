<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CHECKING MISSING IMAGE REFERENCES ===\n\n";
    
    // Check for the specific missing images
    $stmt = $db->query("
        SELECT id, question_text, options, image_url, question_type 
        FROM questions 
        WHERE image_url LIKE '%69474d%' OR image_url LIKE '%69468%'
    ");
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($results) > 0) {
        foreach ($results as $q) {
            // Parse options to get correct answer
            $options = json_decode($q['options'], true);
            $correctAnswer = 'N/A';
            if ($options && isset($options['correct'])) {
                $correctAnswer = $options['correct'];
            }
            
            echo "Question ID: {$q['id']}\n";
            echo "Type: {$q['question_type']}\n";
            echo "Question: " . substr($q['question_text'], 0, 80) . "...\n";
            echo "Correct Answer: {$correctAnswer}\n";
            echo "Missing Image URL: {$q['image_url']}\n";
            echo str_repeat('=', 80) . "\n\n";
        }
        
        echo "Found " . count($results) . " question(s) with missing images.\n\n";
    } else {
        echo "No questions found with those image URLs.\n";
    }
    
    // Also check all identify_personality questions
    echo "\n=== ALL identify_person QUESTIONS - IMAGE STATUS ===\n\n";
    $stmt = $db->query("
        SELECT id, question_text, image_url, options 
        FROM questions 
        WHERE question_type = 'identify_person'
    ");
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $missing = [];
    foreach ($all as $q) {
        $imagePath = 'E:/projects/playqzv4' . $q['image_url'];
        $exists = file_exists($imagePath);
        $symbol = $exists ? '✓' : '✗ MISSING';
        
        $options = json_decode($q['options'], true);
        $correctAnswer = $options && isset($options['correct']) ? $options['correct'] : 'Unknown';
        
        echo "{$symbol} - {$correctAnswer} - {$q['image_url']}\n";
        
        if (!$exists) {
            $missing[] = [
                'id' => $q['id'],
                'name' => $correctAnswer,
                'url' => $q['image_url'],
                'filename' => basename($q['image_url'])
            ];
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "Total identify_person questions: " . count($all) . "\n";
    echo "Missing images: " . count($missing) . "\n\n";
    
    if (count($missing) > 0) {
        echo "=== IMAGES NEEDED ===\n";
        foreach ($missing as $m) {
            echo "- {$m['name']}: {$m['filename']}\n";
            echo "  Upload to: E:\\projects\\playqzv4" . dirname($m['url']) . "\\\n\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
