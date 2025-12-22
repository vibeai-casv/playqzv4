<?php
require_once 'config.php';
require_once 'db.php';
require_once 'utils.php';

$userId = '96ff41e7-c671-451f-9a9d-352270cb0fcd';

$files = [
    'E:/projects/playqzv4/qbank/Old-set/ai_personalities.json',
    'E:/projects/playqzv4/qbank/Questions/mcq/gem1.json',
    'E:/projects/playqzv4/qbank/Old-set/questionU9.json'
];

$importedCount = 0;
$skippedCount = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }

    $data = json_decode(file_get_content_with_error_reporting($file), true);
    if (!$data) {
        echo "Failed to parse JSON: $file\n";
        continue;
    }

    echo "Processing " . count($data) . " questions from $file...\n";

    foreach ($data as $item) {
        // Handle different field names
        $text = $item['question_text'] ?? $item['question'] ?? '';
        $type = $item['question_type'] ?? $item['type'] ?? 'text_mcq';
        $optionsArr = $item['options'] ?? [];
        $correct = $item['correct_answer'] ?? '';
        $image = $item['image_url'] ?? null;
        $explanation = $item['explanation'] ?? '';
        $difficulty = strtolower($item['difficulty'] ?? 'medium');
        $category = $item['category'] ?? 'General';
        $subcategory = $item['subcategory'] ?? null;
        $tagsArr = $item['tags'] ?? [];

        if (!$text || !$correct) continue;

        // Check for duplicate
        $stmtCheck = $pdo->prepare("SELECT id FROM questions WHERE question_text = ?");
        $stmtCheck->execute([$text]);
        if ($stmtCheck->fetch()) {
            $skippedCount++;
            continue;
        }

        // Prepare insert
        $stmtInsert = $pdo->prepare("INSERT INTO questions (
            id, question_text, question_type, options, correct_answer, 
            image_url, explanation, difficulty, category, subcategory, 
            tags, created_by, is_active
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");

        $id = generateUuid();
        $options = json_encode($optionsArr);
        $tags = json_encode($tagsArr);

        // Map difficulty to enum
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            $difficulty = 'medium';
        }

        try {
            $stmtInsert->execute([
                $id, $text, $type, $options, $correct,
                $image, $explanation, $difficulty, $category, $subcategory,
                $tags, $userId
            ]);
            $importedCount++;
        } catch (PDOException $e) {
            echo "Error inserting question: " . $e->getMessage() . "\n";
        }
    }
}

function file_get_content_with_error_reporting($file) {
    $content = file_get_contents($file);
    if ($content === false) return null;
    return $content;
}

echo "\nImport completed!\n";
echo "Imported: $importedCount\n";
echo "Skipped (Duplicates): $skippedCount\n";
?>
