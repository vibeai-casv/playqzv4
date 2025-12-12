<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);
if ($session['role'] !== 'admin' && $session['role'] !== 'super_admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$input = getJsonInput();
$questionIds = $input['question_ids'] ?? [];

if (empty($questionIds)) {
    jsonResponse(['error' => 'No questions selected'], 400);
}

try {
    // 1. Fetch Questions
    $placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id IN ($placeholders)");
    $stmt->execute($questionIds);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Identify and Fetch Associated Media
    $mediaFiles = [];
    $mediaIds = [];
    $imageUrls = [];

    foreach ($questions as $q) {
        if (!empty($q['media_id'])) {
            $mediaIds[] = $q['media_id'];
        }
        if (!empty($q['image_url'])) {
            // Check if it's a local upload
            if (strpos($q['image_url'], '/uploads/') !== false) {
                 $imageUrls[] = $q['image_url'];
            }
        }
    }

    // Fetch media records by ID
    if (!empty($mediaIds)) {
        $placeholders = str_repeat('?,', count($mediaIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM media_library WHERE id IN ($placeholders)");
        $stmt->execute($mediaIds);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mediaFiles[$row['id']] = $row; // Key by ID
        }
    }

    // Also try to find media records by URL if not found by ID
    // (Older imports might not have linked media_id correctly)
    if (!empty($imageUrls)) {
        $placeholders = str_repeat('?,', count($imageUrls) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM media_library WHERE url IN ($placeholders)");
        $stmt->execute($imageUrls);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($mediaFiles[$row['id']])) {
                $mediaFiles[$row['id']] = $row;
            }
        }
    }

    // 3. Prepare ZIP Archive
    $zip = new ZipArchive();
    $tempFile = tempnam(sys_get_temp_dir(), 'qbundle');
    if ($zip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
        throw new Exception("Cannot create zip file");
    }

    // Add JSON manifest
    $manifest = [
        'version' => '1.0',
        'exported_at' => date('c'),
        'exported_by' => $session['email'],
        'questions' => $questions,
        'media_metadata' => array_values($mediaFiles)
    ];
    $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

    // Add Media Files
    $addedFiles = []; // Track added files to avoid dupes in ZIP
    
    // Add files from media_library records
    foreach ($mediaFiles as $media) {
        $relativePath = $media['url']; // e.g. /uploads/logo/abc.png
        $localPath = __DIR__ . '/../../' . ltrim($relativePath, '/'); // Adjust to absolute fs path
        
        if (file_exists($localPath)) {
            $zipPath = 'media' . $relativePath; // e.g. media/uploads/logo/abc.png
            $zip->addFile($localPath, $zipPath);
            $addedFiles[$relativePath] = true;
        }
    }

    // Also scan questions for any image_urls that weren't in media_library but exist on disk
    foreach ($questions as $q) {
        if (!empty($q['image_url']) && strpos($q['image_url'], '/uploads/') !== false) {
            $relativePath = $q['image_url'];
            if (!isset($addedFiles[$relativePath])) {
                 $localPath = __DIR__ . '/../../' . ltrim($relativePath, '/');
                 if (file_exists($localPath)) {
                    $zipPath = 'media' . $relativePath;
                    $zip->addFile($localPath, $zipPath);
                    $addedFiles[$relativePath] = true;
                 }
            }
        }
    }

    $zip->close();

    // 4. Stream Download
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="question_bundle_' . date('Y-m-d_Hi') . '.zip"');
    header('Content-Length: ' . filesize($tempFile));
    readfile($tempFile);
    unlink($tempFile);

} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
