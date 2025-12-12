<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

// Disable error reporting for cleaner JSON responses
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); 
// Ensure output is buffered to prevent stray text from breaking JSON
ob_start();

cors();

$session = authenticate($pdo);
if ($session['role'] !== 'admin' && $session['role'] !== 'super_admin') {
    ob_end_clean();
    jsonResponse(['error' => 'Forbidden'], 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    jsonResponse(['error' => 'Method not allowed'], 405);
}

if (!isset($_FILES['bundle']) || $_FILES['bundle']['error'] !== UPLOAD_ERR_OK) {
    ob_end_clean();
    jsonResponse(['error' => 'No bundle file uploaded'], 400);
}

$file = $_FILES['bundle']['tmp_name'];
$importStats = [
    'questions_imported' => 0,
    'questions_skipped' => 0,
    'media_imported' => 0,
    'errors' => []
];

try {
    $zip = new ZipArchive();
    if ($zip->open($file) !== TRUE) {
        throw new Exception("Failed to open ZIP bundle");
    }

    // 1. Read Manifest
    $manifestJson = $zip->getFromName('manifest.json');
    if (!$manifestJson) {
        throw new Exception("Invalid bundle: missing manifest.json");
    }
    $manifest = json_decode($manifestJson, true);
    if (!$manifest) {
        throw new Exception("Invalid manifest JSON");
    }

    // 2. Extract Media
    // We need to extract 'media/uploads/...' to the actual '../uploads/...'
    // Careful with paths!
    $uploadBaseDir = __DIR__ . '/../../'; // Root of project (api/../)
    
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        
        // Check if it looks like a media file (starts with media/)
        if (strpos($filename, 'media/') === 0 && $filename !== 'media/') {
            // Strip 'media/' prefix to get the relative path as it should be on server (e.g. uploads/logo/x.png)
            $targetRelPath = substr($filename, 6); 
            $targetFullPath = $uploadBaseDir . $targetRelPath;
            
            // Ensure directory exists
            $targetDir = dirname($targetFullPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Extract file
            copy("zip://{$file}#{$filename}", $targetFullPath);
            $importStats['media_imported']++;
        }
    }
    
    $zip->close(); 

    // 3. Import Media Records into Database
    // We should try to insert media_library records if they don't exist
    // Relax constraints: use IGNORE or ON DUPLICATE KEY UPDATE
    if (!empty($manifest['media_metadata'])) {
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
                // Ensure uploaded_by is valid or fallback to current admin
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
            } catch (Exception $e) {
                // Ignore media import errors, proceed
                // echo $e->getMessage();
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    // 4. Import Questions
    if (!empty($manifest['questions'])) {
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
                
            } catch (Exception $e) {
                $importStats['errors'][] = "Question ID {$q['id']} failed: " . $e->getMessage();
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    ob_end_clean();
    jsonResponse([
        'success' => true,
        'stats' => $importStats
    ]);

} catch (Exception $e) {
    ob_end_clean();
    jsonResponse(['error' => 'Bundle Import failed: ' . $e->getMessage()], 500);
}
?>
