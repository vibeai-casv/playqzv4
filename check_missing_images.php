<?php
// Check for questions with missing images
$db = new mysqli('localhost', 'root', '', 'aiqz');

if($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

echo "Checking for questions with image URLs containing '6946' or '6947'...\n\n";

$sql = "SELECT id, question, image_url FROM questions WHERE image_url LIKE '%6946%' OR image_url LIKE '%6947%' LIMIT 10";
$result = $db->query($sql);

if($result) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "Question: " . substr($row['question'], 0, 60) . "...\n";
        echo "Image URL: " . $row['image_url'] . "\n";
        
        // Check if file exists
        $filepath = 'e:/projects/playqzv4' . $row['image_url'];
        echo "File exists: " . (file_exists($filepath) ? 'YES' : 'NO') . "\n";
        echo "---\n";
    }
} else {
    echo "Query failed: " . $db->error . "\n";
}

$db->close();
?>
