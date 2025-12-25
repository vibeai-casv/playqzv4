<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../../import_errors.log');

// Log function for debugging
function logImport($message) {
    error_log("[BUNDLE IMPORT] " . $message);
}

logImport("Import request started");

// Ensure output is buffered to prevent stray text from breaking JSON
ob_start();

cors();

$session = authenticate($pdo);
if ($session['role'] !== 'admin' && $session['role'] !== 'super_admin') {
    logImport("Access denied - user role: " . $session['role']);
    ob_end_clean();
    jsonResponse(['error' => 'Forbidden'], 403);
}

logImport("User authenticated: " . $session['username']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logImport("Invalid method: " . $_SERVER['REQUEST_METHOD']);
    ob_end_clean();
    jsonResponse(['error' => 'Method not allowed'], 405);
}

if (!isset($_FILES['bundle']) || $_FILES['bundle']['error'] !== UPLOAD_ERR_OK) {
    $uploadError = $_FILES['bundle']['error'] ?? 'No file';
    logImport("File upload error: " . $uploadError);
    ob_end_clean();
    jsonResponse(['error' => 'No bundle file uploaded. Error code: ' . $uploadError], 400);
}

$file = $_FILES['bundle']['tmp_name'];
logImport("File received: " . $_FILES['bundle']['name'] . " (" . $_FILES['bundle']['size'] . " bytes)");

$importStats = [
    'questions_imported' => 0,
    'questions_skipped' => 0,
    'media_imported' => 0,
    'errors' => []
];

try {
    logImport("Opening ZIP file...");
    $zip = new ZipArchive();
    if ($zip->open($file) !== TRUE) {
        throw new Exception("Failed to open ZIP bundle");
    }
    logImport("ZIP opened successfully. Files in archive: " . $zip->numFiles);

    // 1. Read Manifest
    logImport("Reading manifest.json...");
    $manifestJson = $zip->getFromName('manifest.json');
    if (!$manifestJson) {
        throw new Exception("Invalid bundle: missing manifest.json");
    }
    $manifest = json_decode($manifestJson, true);
    if (!$manifest) {
        throw new Exception("Invalid manifest JSON");
    }
    logImport("Manifest loaded. Questions: " . count($manifest['questions'] ?? []));
    logImport("Media metadata entries: " . count($manifest['media_metadata'] ?? []));

    // 2. Extract Media
    $uploadBaseDir = __DIR__ . '/../../'; // Root of project (api/../)
    logImport("Upload base directory: " . $uploadBaseDir);
    
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        
        // Check if it looks like a media file (starts with media/)
        if (strpos($filename, 'media/') === 0 && $filename !== 'media/') {
            // Strip 'media/' prefix to get the relative path
            $targetRelPath = substr($filename, 6); 
            $targetFullPath = $uploadBaseDir . $targetRelPath;
            
            // Ensure directory exists
            $targetDir = dirname($targetFullPath);
            if (!is_dir($targetDir)) {
                logImport("Creating directory: " . $targetDir);
                mkdir($targetDir, 0755, true);
            }
            
            // Extract file
            copy("zip://{$file}#{$filename}", $targetFullPath);
            $importStats['media_imported']++;
            logImport("Extracted: " . $targetRelPath);
        }
    }
    
    $zip->close(); 
    logImport("ZIP closed. Media files extracted: " . $importStats['media_imported']);

    // 3. Import Media Records into Database
    if (!empty($manifest['media_metadata'])) {
        logImport("Importing media metadata...");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $stmtMedia = $pdo->prepare("
            INSERT INTO media_library (
                id, filename, original_filename, url, type, mime_type, 
                size_bytes, description, metadata, uploaded_by, is_active, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW()
            ) ON DUPLICATE KEY UPDATE 
                updated_at = NOW()
        ");
        
        foreach ($manifest['media_metadata'] as $media) {
            try {
                $uploadedBy = $session['profile_id'] ?? $session['user_id'];
                
                $stmtMedia->execute([
                    $media['id'],
                    $media['filename'],
                    $media['original_filename'],
                    $media['url'],
                    $media['type'],
                    $media['mime_type'],
                    $media['size_bytes'],
                    $media['description'] ?? '',
                    json_encode($media['metadata'] ?? []),
                    $uploadedBy
                ]);
                logImport("Media imported: " . $media['id']);
            } catch (Exception $e) {
                logImport("Media import error: " . $e->getMessage());
                $importStats['errors'][] = "Media {$media['id']}: " . $e->getMessage();
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    // 4. Import Questions
    if (!empty($manifest['questions'])) {
        logImport("Importing questions...");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $stmtCheck = $pdo->prepare("SELECT id FROM questions WHERE id = ?");
        $stmtInsert = $pdo->prepare("
            INSERT INTO questions (
                id, question_text, question_type, options, correct_answer, 
                image_url, media_id, explanation, hint, difficulty, 
                category, subcategory, tags, points, time_limit_seconds, 
                is_active, is_verified, ai_generated, is_demo, created_by, 
                created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
            )
        ");
        
        foreach ($manifest['questions'] as $q) {
            try {
                // Check duplicate by ID
                $stmtCheck->execute([$q['id']]);
                if ($stmtCheck->fetch()) {
                    $importStats['questions_skipped']++;
                    logImport("Question skipped (duplicate): " . $q['id']);
                    continue; 
                }
                
                // Fallback for created_by
                $createdBy = $session['profile_id'] ?? $session['user_id'];

                $stmtInsert->execute([
                    $q['id'],
                    $q['question_text'],
                    $q['question_type'],
                    is_array($q['options']) ? json_encode($q['options']) : $q['options'],
                    $q['correct_answer'],
                    $q['image_url'],
                    $q['media_id'],
                    $q['explanation'],
                    $q['hint'],
                    $q['difficulty'],
                    $q['category'],
                    $q['subcategory'],
                    is_array($q['tags']) ? json_encode($q['tags']) : $q['tags'],
                    $q['points'],
                    $q['time_limit_seconds'],
                    $q['is_active'] ?? 1,
                    $q['is_verified'] ?? 1,
                    $q['ai_generated'] ?? 0,
                    $q['is_demo'] ?? 0,
                    $createdBy
                ]);
                
                $importStats['questions_imported']++;
                logImport("Question imported: " . $q['id']);
                
            } catch (Exception $e) {
                $error = "Question ID {$q['id']}: " . $e->getMessage();
                $importStats['errors'][] = $error;
                logImport("Question import error: " . $error);
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    logImport("Import completed successfully");
    logImport("Stats: " . json_encode($importStats));

    ob_end_clean();
    jsonResponse([
        'success' => true,
        'stats' => $importStats
    ]);

} catch (Exception $e) {
    $errorMsg = 'Bundle Import failed: ' . $e->getMessage();
    logImport("FATAL ERROR: " . $errorMsg);
    logImport("Stack trace: " . $e->getTraceAsString());
    ob_end_clean();
    jsonResponse(['error' => $errorMsg], 500);
}
?>
