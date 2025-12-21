<?php
$db = new PDO('mysql:host=localhost;dbname=aiqz;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== QUESTIONS TABLE SCHEMA ===\n";
$stmt = $db->query("DESCRIBE questions");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $col) {
    echo "{$col['Field']} ({$col['Type']})\n";
}
?>
