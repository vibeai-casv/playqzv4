<?php
// Disable error display to prevent valid JSON corruption
ini_set('display_errors', 0);
// Log errors to server log instead
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug_errors.log');
error_reporting(E_ALL);

// Debug start
file_put_contents(__DIR__ . '/debug_errors.log', "Script starting at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Clear opcache to ensure latest code runs
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

require_once '../db.php';
require_once '../utils.php';

cors();

try {
    // Normal Authentication
    $session = authenticate($pdo);
    
    // Debug logging removed.
    // ...
} catch (Exception $e) {
    // Return 401/500 based on error
    if ($e->getMessage() === 'Unauthorized') {
        jsonResponse(['error' => 'Unauthorized'], 401);
    }
    error_log("Authentication Error: " . $e->getMessage());
    // Return 200 to bypass Apache error pages
    jsonResponse(['success' => false, 'error' => 'Authentication Exception: ' . $e->getMessage()], 200);
}

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

// Debug logging
error_log("=== UPLOAD REQUEST DEBUG ===");
error_log("Received mediaType: " . $mediaType);
error_log("POST data: " . print_r($_POST, true));
error_log("File name: " . $fileName);

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

// Generate filename based on original filename (sanitized)
$baseName = pathinfo($fileName, PATHINFO_FILENAME);
// Only remove truly dangerous characters, keep alphanumeric and common safe chars
$sanitizedFileName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $baseName);
$newFileName = $sanitizedFileName . "." . $ext;
$destination = $typeDir . $newFileName;

// Debug logging
error_log("=== UPLOAD DEBUG ===");
error_log("Original filename: " . $fileName);
error_log("Base name: " . $baseName);
error_log("Sanitized: " . $sanitizedFileName);
error_log("New filename: " . $newFileName);
error_log("Destination: " . $destination);

// If file exists, add timestamp suffix to prevent conflicts
// This ensures we always return the correct filename that was actually saved
if (file_exists($destination)) {
    $timestamp = time();
    $newFileName = $sanitizedFileName . "_" . $timestamp . "." . $ext;
    $destination = $typeDir . $newFileName;
    error_log("File exists, created new filename: " . $newFileName);
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
