<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

// Allow ALL origins for this script
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // BYPASS AUTHENTICATION
    // Instead of authenticate($pdo), we get a default user
    $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
    $defaultUserId = $stmt->fetchColumn();
    
    if (!$defaultUserId) {
        throw new Exception("No users found in database to assign upload to.");
    }
    
    // Mock session for compatibility
    $session = ['user_id' => $defaultUserId];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!isset($_FILES['file'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['file'];
    $type = $_POST['type'] ?? 'general';
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type');
    }

    // Set upload directory
    $uploadDir = __DIR__ . '/../../uploads/' . $type . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate filename based on original filename (sanitized)
    $originalName = $file['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    
    // Sanitize filename: keep alphanumeric, underscores, and hyphens
    $sanitizedBase = preg_replace('/[^a-zA-Z0-9_\-]/', '', $baseName);
    
    // Fallback if sanitization results in empty string
    if (empty($sanitizedBase)) {
        $sanitizedBase = 'upload_' . time();
    }
    
    $filename = $sanitizedBase . '.' . $extension;
    $filepath = $uploadDir . $filename;

    // Prevent overwriting: append timestamp if file exists
    if (file_exists($filepath)) {
        $filename = $sanitizedBase . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
    }

    // Move file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to save file');
    }

    // Public URL
    $baseUrl = '/uploads/' . $type . '/'; 
    // Note: User environment might need adjustment, but relative path usually works for frontend
    // If strict URL needed:
    // $baseUrl = '/playqzv4/uploads/' . $type . '/';
    
    $publicUrl = $baseUrl . $filename;

    // Save to media table
    $mediaId = generateUuid();
    $mimeType = mime_content_type($filepath);
    $sizeBytes = filesize($filepath);
    
    $stmt = $pdo->prepare("INSERT INTO media_library (
        id, filename, original_filename, url, type, mime_type, 
        size_bytes, description, uploaded_by, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->execute([
        $mediaId, 
        $filename, 
        $file['name'], 
        $publicUrl, 
        $type, 
        $mimeType, 
        $sizeBytes,
        "Public upload from Bulk Edit",
        $session['user_id']
    ]);

    jsonResponse([
        'success' => true,
        'media' => [
            'id' => $mediaId,
            'url' => $publicUrl,
            'filename' => $file['name']
        ]
    ]);

} catch (Exception $e) {
    error_log("Public Upload Error: " . $e->getMessage());
    jsonResponse(['error' => $e->getMessage()], 500);
}
