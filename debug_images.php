<?php
require 'api/config.php';
require 'api/db.php';

try {
    $stmt = $pdo->query("SELECT id, question_text, image_url FROM questions WHERE question_type = 'image_identify_person'");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($results) . " personality questions.\n";
    foreach ($results as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "Text: " . $row['question_text'] . "\n";
        echo "Image: " . ($row['image_url'] ? $row['image_url'] : "NULL") . "\n";
        echo "-------------------\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
