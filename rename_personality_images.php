<?php
// Rename personality images from hashed names to proper person names
$db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== RENAMING PERSONALITY IMAGES ===\n\n";

// Get ALL questions with images in personality folder
$stmt = $db->query("
    SELECT id, options, image_url, question_type
    FROM questions 
    WHERE image_url LIKE '/uploads/personality/%'
");

$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($questions) == 0) {
    echo "No questions found with personality images.\n";
    exit;
}

echo "Found " . count($questions) . " question(s) with personality images\n\n";

$renamed = 0;
$skipped = 0;

foreach ($questions as $q) {
    $options = json_decode($q['options'], true);
    
    // Get person name from correct answer
    $personName = null;
    if ($options && isset($options['correct'])) {
        $personName = $options['correct'];
    }
    
    if (!$personName || $personName === 'Unknown') {
        echo "⚠ Skipped: No valid person name for question {$q['id']}\n";
        $skipped++;
        continue;
    }
    
    // Create proper filename from person name
    $properFilename = str_replace(' ', '', $personName) . '.png';
    
    // Current and new paths
    $currentUrl = $q['image_url'];
    $newUrl = '/uploads/personality/' . $properFilename;
    
    // Check if already using proper name
    if ($currentUrl === $newUrl) {
        echo "✓ Already correct: {$personName}\n";
        continue;
    }
    
    $currentPath = 'E:/projects/playqzv4' . $currentUrl;
    $newPath = 'E:/projects/playqzv4' . $newUrl;
    
    // Check if current file exists
    if (!file_exists($currentPath)) {
        echo "✗ File not found: {$currentUrl}\n";
        echo "  Person: {$personName}\n";
        $skipped++;
        continue;
    }
    
    // Rename the file
    if (file_exists($newPath)) {
        echo "  Note: {$properFilename} already exists, deleting old version...\n";
        unlink($newPath);
    }
    
    if (rename($currentPath, $newPath)) {
        // Update database
        $updateStmt = $db->prepare("UPDATE questions SET image_url = ? WHERE id = ?");
        $updateStmt->execute([$newUrl, $q['id']]);
        
        echo "✓ Renamed: {$personName}\n";
        echo "  Old: " . basename($currentUrl) . "\n";
        echo "  New: {$properFilename}\n\n";
        $renamed++;
    } else {
        echo "✗ Failed to rename: {$personName}\n\n";
        $skipped++;
    }
}

echo "=== SUMMARY ===\n";
echo "Total questions: " . count($questions) . "\n";
echo "Renamed: $renamed\n";
echo "Skipped: $skipped\n\n";

if ($renamed > 0) {
    echo "✓ SUCCESS!\n";
    echo "Files have been renamed to proper person names.\n";
    echo "Database has been updated.\n\n";
    
    echo "New files:\n";
    $files = scandir('E:/projects/playqzv4/uploads/personality');
    foreach ($files as $file) {
        if (substr($file, -4) === '.png') {
            echo "  - $file\n";
        }
    }
    
    echo "\nRefresh your browser to see the changes!\n";
}
?>
