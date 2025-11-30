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

if ($fileError !== 0) {
    jsonResponse(['error' => 'File upload error code: ' . $fileError], 400);
}

// Validate type
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    jsonResponse(['error' => 'Invalid file type'], 400);
}

// Generate unique name
$newFileName = uniqid('', true) . "." . $ext;
$uploadDir = '../../public/uploads/';
$destination = $uploadDir . $newFileName;

// Ensure dir exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (move_uploaded_file($fileTmp, $destination)) {
    $url = '/uploads/' . $newFileName; // Relative URL for frontend
    $id = generateUuid();
    
    // Save to DB
    try {
        $stmt = $pdo->prepare("INSERT INTO media_library (
            id, filename, original_filename, url, type, mime_type, size_bytes, uploaded_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $mimeType = mime_content_type($destination);
        $type = 'question_image'; // Default
        
        $stmt->execute([
            $id, $newFileName, $fileName, $url, $type, $mimeType, $fileSize, $session['user_id']
        ]);
        
        jsonResponse([
            'message' => 'File uploaded',
            'file' => [
                'id' => $id,
                'url' => $url,
                'filename' => $newFileName
            ]
        ], 201);
        
    } catch (PDOException $e) {
        jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
    }
} else {
    jsonResponse(['error' => 'Failed to move uploaded file'], 500);
}
?>
