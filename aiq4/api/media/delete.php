<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

// Check if admin
if ($session['role'] !== 'admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$input = getJsonInput();
$id = $input['id'] ?? null;

if (!$id) {
    jsonResponse(['error' => 'ID is required'], 400);
}

try {
    // Get file path to delete from disk
    $stmt = $pdo->prepare("SELECT url FROM media_library WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch();

    if ($file) {
        // URL is like /uploads/filename.ext
        $filePath = '../../public' . $file['url'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM media_library WHERE id = ?");
    $stmt->execute([$id]);
    
    jsonResponse(['message' => 'Media deleted']);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
