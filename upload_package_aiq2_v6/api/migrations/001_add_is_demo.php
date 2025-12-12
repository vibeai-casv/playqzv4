<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db.php';

try {
    echo "Checking for is_demo column...\n";
    
    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM questions LIKE 'is_demo'");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        echo "Adding is_demo column to questions table...\n";
        $sql = "ALTER TABLE questions ADD COLUMN is_demo TINYINT(1) DEFAULT 0";
        $pdo->exec($sql);
        echo "Column is_demo added successfully.\n";
    } else {
        echo "Column is_demo already exists.\n";
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
