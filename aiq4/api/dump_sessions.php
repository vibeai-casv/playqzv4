<?php
require 'db.php';
try {
    $stmt = $pdo->query("SELECT * FROM sessions");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Count: " . count($sessions) . "\n";
    foreach ($sessions as $s) {
        echo "Token: " . substr($s['token'], 0, 10) . "... | User: " . $s['user_id'] . " | Expires: " . $s['expires_at'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
