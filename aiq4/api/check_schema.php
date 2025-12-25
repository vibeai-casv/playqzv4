<?php
require_once 'db.php';
$stmt = $pdo->query('DESCRIBE questions');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . PHP_EOL;
}
?>
