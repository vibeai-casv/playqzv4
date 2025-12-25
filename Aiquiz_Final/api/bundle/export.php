<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

// Enable error logging for debugging
error_log("Bundle export started");

$session = authenticate($pdo);
if ($session['role'] !== 'admin' && $session['role'] !== 'super_admin') {
    error_log("Bundle export forbidden - role: " . $session['role']);
    jsonResponse(['error' => 'Forbidden'], 403);
}

$input = getJsonInput();
$questionIds = $input['question_ids'] ?? [];

error_log("Export bundle requested for " . count($questionIds) . " questions");

if (empty($questionIds)) {
    error_log("No questions selected for bundle export");
    jsonResponse(['error' => 'No questions selected'], 400);
}

try {
    // 1. Fetch Questions
    error_log("Fetching questions from database...");
    $placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id IN ($placeholders)");
    $stmt->execute($questionIds);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Found " . count($questions) . " questions");

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

    error_log("Found " . count($mediaFiles) . " media files");

    // 3. Prepare ZIP Archive
    error_log("Creating ZIP archive...");
    $zip = new ZipArchive();
    $tempFile = tempnam(sys_get_temp_dir(), 'qbundle');
    if ($zip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
        error_log("ERROR: Cannot create zip file at: $tempFile");
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
    error_log("ZIP file created successfully");

    // Validate the ZIP file before streaming
    if (!file_exists($tempFile)) {
        error_log("ERROR: Temp ZIP file does not exist after close: $tempFile");
        throw new Exception("Failed to create ZIP file");
    }

    $fileSize = filesize($tempFile);
    error_log("ZIP file size: $fileSize bytes");

    if ($fileSize  === 0 || $fileSize === false) {
        error_log("ERROR: ZIP file is empty or unreadable");
        @unlink($tempFile);
        throw new Exception("Failed to create valid ZIP file");
    }

    // 4. Stream Download
    error_log("Streaming ZIP file to client");
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="question_bundle_' . date('Y-m-d_Hi') . '.zip"');
    header('Content-Length: ' . $fileSize);
    readfile($tempFile);
    unlink($tempFile);
    error_log("Bundle export completed successfully");

} catch (Exception $e) {
    error_log("Bundle export error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
