<?php
// Automatically rename hashed image files and update database
$db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== RENAMING IMAGE FILES AND UPDATING DATABASE ===\n\n";

// Get all personality questions
$stmt = $db->query("
    SELECT id, options, image_url 
    FROM questions 
    WHERE question_type IN ('image_identify_person', 'identify_person')
      AND image_url LIKE '/uploads/personality/%'
");

$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$renamed = 0;
$errors = 0;

foreach ($questions as $q) {
    $options = json_decode($q['options'], true);
    
    if (!$options || !isset($options['correct'])) {
        echo "⚠ Skipped: No correct answer for question {$q['id']}\n";
        continue;
    }
    
    $personName = $options['correct'];
    
    // Convert person name to filename format (remove spaces, use CamelCase)
    $filename = str_replace(' ', '', $personName) . '.png';
    
    // Get current image path
    $currentUrl = $q['image_url'];
    $currentPath = 'E:/projects/playqzv4' . $currentUrl;
    
    // New image path
    $newUrl = '/uploads/personality/' . $filename;
    $newPath = 'E:/projects/playqzv4' . $newUrl;
    
    // Check if current file exists
    if (!file_exists($currentPath)) {
        echo "✗ File not found: $currentUrl (Person: {$personName})\n";
        $errors++;
        continue;
    }
    
    // Check if it's already properly named
    if ($currentUrl === $newUrl) {
        echo "✓ Already correct: {$personName} → {$filename}\n";
        continue;
    }
    
    // Rename the file
    if (file_exists($newPath)) {
        echo "⚠ Target file already exists: {$filename}\n";
        echo "  Overwriting...\n";
        unlink($newPath);
    }
    
    if (rename($currentPath, $newPath)) {
        // Update database
        $updateStmt = $db->prepare("UPDATE questions SET image_url = ? WHERE id = ?");
        $updateStmt->execute([$newUrl, $q['id']]);
        
        echo "✓ Renamed: {$personName}\n";
        echo "  From: " . basename($currentUrl) . "\n";
        echo "  To:   {$filename}\n";
        $renamed++;
    } else {
        echo "✗ Failed to rename file for: {$personName}\n";
        $errors++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total questions: " . count($questions) . "\n";
echo "Files renamed: $renamed\n";
echo "Errors: $errors\n\n";

if ($renamed > 0) {
    echo "✓ SUCCESS! Files renamed and database updated.\n";
    echo "  Refresh your browser to see the changes.\n\n";
    
    echo "New files in personality folder:\n";
    $files = glob('E:/projects/playqzv4/uploads/personality/*.png');
    foreach ($files as $file) {
        echo "  - " . basename($file) . "\n";
    }
}
?>
