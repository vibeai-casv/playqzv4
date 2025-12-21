<?php
// Update database to use properly named image files
$db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== UPDATING IMAGE URLS TO USE PROPER NAMES ===\n\n";

// Mapping of common name variations to standardized filenames
$nameMap = [
    'Sam Altman' => 'SamAltman.png',
    'Geoffrey Hinton' => 'GeoffreyHinton.png',
    'Yann LeCun' => 'YannLeCun.png',
    'Andrew Ng' => 'AndrewNg.png',
    'Demis Hassabis' => 'DemisHassabis.png',
    'Ilya Sutskever' => 'IlyaSutskever.png',
    'Fei-Fei Li' => 'FeiFeiLi.png',
    'Yoshua Bengio' => 'YoshuaBengio.png',
    'Ian Goodfellow' => 'IanGoodfellow.png',
    'Andrej Karpathy' => 'AndrejKarpathy.png',
    'Jürgen Schmidhuber' => 'JurgenSchmidhuber.png',
    'Timnit Gebru' => 'TimnitGebru.png',
    'Sundar Pichai' => 'SundarPichai.png',
    'Satya Nadella' => 'SatyaNadella.png',
    'Elon Musk' => 'ElonMusk.png',
];

// Get all personality identification questions
$stmt = $db->query("
    SELECT id, options, image_url 
    FROM questions 
    WHERE question_type IN ('image_identify_person', 'identify_person')
");

$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$updated = 0;
$skipped = 0;

foreach ($questions as $q) {
    $options = json_decode($q['options'], true);
    
    if (!$options || !isset($options['correct'])) {
        echo "⚠ Skipped question {$q['id']}: No correct answer found\n";
        $skipped++;
        continue;
    }
    
    $personName = $options['correct'];
    
    // Check if we have a mapping for this person
    if (isset($nameMap[$personName])) {
        $newImageUrl = '/uploads/personality/' . $nameMap[$personName];
        
        // Check if file exists
        $filePath = 'E:/projects/playqzv4' . $newImageUrl;
        
        if (file_exists($filePath)) {
            // Update the database
            $updateStmt = $db->prepare("UPDATE questions SET image_url = ? WHERE id = ?");
            $updateStmt->execute([$newImageUrl, $q['id']]);
            
            echo "✓ Updated: {$personName} → {$nameMap[$personName]}\n";
            $updated++;
        } else {
            echo "✗ File not found: {$nameMap[$personName]} (Person: {$personName})\n";
            echo "  Please upload: $filePath\n";
            $skipped++;
        }
    } else {
        echo "⚠ No mapping for: {$personName}\n";
        echo "  Current image: {$q['image_url']}\n";
        $skipped++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total questions: " . count($questions) . "\n";
echo "Updated: $updated\n";
echo "Skipped: $skipped\n\n";

if ($updated > 0) {
    echo "✓ Database updated successfully!\n";
    echo "  Refresh your browser to see the changes.\n\n";
}

if ($skipped > 0) {
    echo "⚠ Some files are missing or need manual mapping.\n";
    echo "  Upload the required images and run this script again.\n";
}
?>
