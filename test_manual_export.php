<?php
// Direct test of bundle export API
require_once 'api/config.php';
require_once 'api/db.php';

// Get first few question IDs from database
$stmt = $pdo->query("SELECT id FROM questions LIMIT 3");
$questionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($questionIds)) {
    die("No questions found in database\n");
}

echo "Testing bundle export with " . count($questionIds) . " questions: " . implode(', ', $questionIds) . "\n\n";

// Simulate the export process
echo "1. Fetching questions...\n";
$placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id IN ($placeholders)");
$stmt->execute($questionIds);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "   Found " . count($questions) . " questions\n";

// Check for media
echo "\n2. Checking for media files...\n";
$mediaCount = 0;
foreach ($questions as $q) {
    if (!empty($q['image_url'])) {
        $mediaCount++;
        echo "   - " . $q['question_text'] . " has image: " . $q['image_url'] . "\n";
    }
}
echo "   Total: $mediaCount questions with images\n";

// Create ZIP
echo "\n3. Creating ZIP archive...\n";
if (!class_exists('ZipArchive')) {
    die("   ERROR: ZipArchive not available!\n");
}

$zip = new ZipArchive();
$outputFile = __DIR__ . '/qbank/test_manual_export.zip';

if (file_exists($outputFile)) {
    unlink($outputFile);
}

if ($zip->open($outputFile, ZipArchive::CREATE) !== TRUE) {
    die("   ERROR: Cannot create ZIP file\n");
}

// Add manifest
$manifest = [
    'version' => '1.0',
    'exported_at' => date('c'),
    'questions' => $questions
];
$zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
echo "   Added manifest.json\n";

// Add media files
$addedFiles = 0;
foreach ($questions as $q) {
    if (!empty($q['image_url']) && strpos($q['image_url'], '/uploads/') !== false) {
        $relativePath = $q['image_url'];
        $localPath = __DIR__ . '/' . ltrim($relativePath, '/');
        
        if (file_exists($localPath)) {
            $zipPath = 'media' . $relativePath;
            $zip->addFile($localPath, $zipPath);
            $addedFiles++;
            echo "   Added: $zipPath\n";
        } else {
            echo "   Missing: $localPath\n";
        }
    }
}

$zip->close();

$fileSize = filesize($outputFile);
echo "\n4. ZIP file created!\n";
echo "   Location: $outputFile\n";
echo "   Size: $fileSize bytes\n";

if ($fileSize > 200) {
    echo "\n✅ SUCCESS! Bundle export is working correctly!\n";
    echo "\nYou can extract this file to verify:\n";
    echo "   Expand-Archive -Path '$outputFile' -DestinationPath 'e:\\projects\\playqzv4\\qbank\\test_extract' -Force\n";
} else {
    echo "\n❌ ERROR: ZIP file is too small, something went wrong\n";
}
?>
