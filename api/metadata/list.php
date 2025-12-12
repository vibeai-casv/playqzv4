<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

try {
    // Get unique categories
    $stmtCategory = $pdo->query("SELECT DISTINCT category FROM questions WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
    $categories = $stmtCategory->fetchAll(PDO::FETCH_COLUMN);

    // Get unique types
    // Note: types are ENUMs in schema but useful to know what's actually used
    $stmtType = $pdo->query("SELECT DISTINCT question_type FROM questions WHERE question_type IS NOT NULL AND question_type != ''");
    $types = $stmtType->fetchAll(PDO::FETCH_COLUMN);
    
    // Always include standard types if not present
    $standardTypes = ['text_mcq', 'image_identify_logo', 'image_identify_person', 'true_false', 'short_answer'];
    $types = array_unique(array_merge($types, $standardTypes));

    // Get unique difficulties
    $stmtDiff = $pdo->query("SELECT DISTINCT difficulty FROM questions WHERE difficulty IS NOT NULL AND difficulty != ''");
    $difficulties = $stmtDiff->fetchAll(PDO::FETCH_COLUMN);
    
    // Always include standard difficulties
    $standardDifficulties = ['easy', 'medium', 'hard'];
    $difficulties = array_unique(array_merge($difficulties, $standardDifficulties));
    
    // Get unique tags
    // Tags are stored as JSON array ["tag1", "tag2"]
    // We need to fetch all, then flatten
    $stmtTags = $pdo->query("SELECT tags FROM questions WHERE tags IS NOT NULL");
    $rows = $stmtTags->fetchAll(PDO::FETCH_COLUMN);
    
    $allTags = [];
    foreach ($rows as $row) {
        $tags = json_decode($row, true);
        if (is_array($tags)) {
            $allTags = array_merge($allTags, $tags);
        }
    }
    $uniqueTags = array_values(array_unique($allTags));
    sort($uniqueTags);

    jsonResponse([
        'categories' => $categories,
        'types' => array_values($types),
        'difficulties' => array_values($difficulties),
        'tags' => $uniqueTags
    ]);

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
