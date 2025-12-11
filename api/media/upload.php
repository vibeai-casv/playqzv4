<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

if (!isset($_FILES['file'])) {
    jsonResponse(['error' => 'No file uploaded'], 400);
}

$file = $_FILES['file'];
$fileName = $file['name'];
$fileTmp = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];
$mediaType = $_POST['type'] ?? 'question_image'; // logo, personality, question_image, avatar
$description = $_POST['description'] ?? '';

// Validate media type
$validTypes = ['logo', 'personality', 'question_image', 'avatar'];
if (!in_array($mediaType, $validTypes)) {
    jsonResponse(['error' => 'Invalid media type'], 400);
}

if ($fileError !== 0) {
    jsonResponse(['error' => 'File upload error code: ' . $fileError], 400);
}

// Validate file type
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    jsonResponse(['error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP, SVG'], 400);
}

// Validate file size (5MB max)
$maxSize = 5 * 1024 * 1024;
if ($fileSize > $maxSize) {
    jsonResponse(['error' => 'File too large. Maximum size: 5MB'], 400);
}

// Create organized directory structure
$uploadBaseDir = __DIR__ . '/../../uploads/';
$typeDir = $uploadBaseDir . $mediaType . '/';

if (!is_dir($typeDir)) {
    mkdir($typeDir, 0777, true);
}

// Generate name based on original filename (sanitized)
$sanitizedFileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
$newFileName = $sanitizedFileName . "." . $ext;
$destination = $typeDir . $newFileName;

// If file exists, append timestamp to prevent overwrite but keep name
if (file_exists($destination)) {
    $newFileName = $sanitizedFileName . "_" . time() . "." . $ext;
    $destination = $typeDir . $newFileName;
}

if (move_uploaded_file($fileTmp, $destination)) {
    // URL relative to public root
    $url = '/uploads/' . $mediaType . '/' . $newFileName;
    $id = generateUuid();
    
    // Get image metadata
    $metadata = [];
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $imageInfo = @getimagesize($destination);
        if ($imageInfo) {
            $metadata = [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'mime' => $imageInfo['mime']
            ];
        }
    }
    
    // Save to DB
    try {
        $stmt = $pdo->prepare("INSERT INTO media_library (
            id, filename, original_filename, url, type, mime_type, 
            size_bytes, description, metadata, uploaded_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $mimeType = mime_content_type($destination);
        
        $stmt->execute([
            $id, 
            $newFileName, 
            $fileName, 
            $url, 
            $mediaType, 
            $mimeType, 
            $fileSize,
            $description,
            json_encode($metadata),
            $session['user_id']
        ]);
        
        jsonResponse([
            'success' => true,
            'message' => 'File uploaded successfully',
            'media' => [
                'id' => $id,
                'url' => $url,
                'filename' => $newFileName,
                'original_filename' => $fileName,
                'type' => $mediaType,
                'mime_type' => $mimeType,
                'size_bytes' => $fileSize,
                'description' => $description,
                'metadata' => $metadata
            ]
        ], 201);
        
    } catch (PDOException $e) {
        // Delete uploaded file if database insert fails
        @unlink($destination);
        jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
    }
} else {
    jsonResponse(['error' => 'Failed to move uploaded file'], 500);
}
?>
