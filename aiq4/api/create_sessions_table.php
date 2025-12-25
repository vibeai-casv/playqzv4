<?php
require_once 'config.php';
require_once 'db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS `sessions` (
        `id` CHAR(36) PRIMARY KEY,
        `user_id` CHAR(36) NOT NULL,
        `token` VARCHAR(64) NOT NULL UNIQUE,
        `expires_at` TIMESTAMP NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "Sessions table created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
