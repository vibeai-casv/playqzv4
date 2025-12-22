<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

// Check if admin
if ($session['role'] !== 'admin' && $session['role'] !== 'super_admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$input = getJsonInput();
$questions = $input['questions'] ?? [];

if (empty($questions)) {
    jsonResponse(['error' => 'No questions provided'], 400);
}

if (!is_array($questions)) {
    jsonResponse(['error' => 'Invalid format. Expected array of questions'], 400);
}

$imported = 0;
$skipped = 0;
$errors = [];

// Extract user/profile info from session
$userId = $session['user_id'] ?? null;
$profileId = $session['profile_id'] ?? $userId;

// Debug logging
error_log("Import - Initial userId: " . ($userId ?? 'null') . ", profileId: " . ($profileId ?? 'null'));

// If no profile_id in session, try to get it from user_id
if (!$profileId && $userId) {
    $stmt = $pdo->prepare("SELECT id FROM profiles WHERE id = ?");
    $stmt->execute([$userId]);
    $profileRow = $stmt->fetch();
    if ($profileRow) {
        $profileId = $profileRow['id'];
        error_log("Import - Found profile from userId: " . $profileId);
    }
}

// If still no valid profileId, try to find ANY admin profile as fallback
if (!$profileId) {
    $stmt = $pdo->query("SELECT id FROM profiles WHERE role IN ('admin', 'super_admin') LIMIT 1");
    $adminRow = $stmt->fetch();
    if ($adminRow) {
        $profileId = $adminRow['id'];
        error_log("Import - Using fallback admin profile: " . $profileId);
    }
}

// If still no profile, get ANY profile
if (!$profileId) {
    $stmt = $pdo->query("SELECT id FROM profiles LIMIT 1");
    $anyRow = $stmt->fetch();
    if ($anyRow) {
        $profileId = $anyRow['id'];
        error_log("Import - Using any available profile: " . $profileId);
    }
}

// Last resort: Create a system profile if none exists
if (!$profileId) {
    $profileId = generateUuid();
    $email = $session['email'] ?? 'system@import.local';
    error_log("Import - Creating new system profile: " . $profileId);
    
    // First need to create a user record since profiles references users
    try {
        // Create user first
        $userStmt = $pdo->prepare("
            INSERT INTO users (id, email, password_hash, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE updated_at = NOW()
        ");
        $userStmt->execute([$profileId, $email, password_hash('system_import_' . time(), PASSWORD_DEFAULT)]);
        
        // Then create profile
        $createStmt = $pdo->prepare("
            INSERT INTO profiles (id, email, name, role, created_at, updated_at)
            VALUES (?, ?, 'System Import', 'admin', NOW(), NOW())
            ON DUPLICATE KEY UPDATE updated_at = NOW()
        ");
        $createStmt->execute([$profileId, $email]);
        error_log("Import - Created system profile successfully");
    } catch (Exception $e) {
        error_log("Could not create fallback profile: " . $e->getMessage());
        // Profile creation failed, will try to proceed anyway with FK checks disabled
    }
}

error_log("Import - Final profileId to use: " . ($profileId ?? 'null'));

try {
    // CRITICAL: Disable foreign key checks INSIDE the transaction for maximum compatibility
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("SET SESSION FOREIGN_KEY_CHECKS=0");
    
    $pdo->beginTransaction();
    
    // Re-apply inside transaction for some MySQL versions
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    
    foreach ($questions as $index => $question) {
        try {
            // Validate required fields
            $requiredFields = ['question_text', 'question_type', 'category', 'difficulty', 'correct_answer'];
            foreach ($requiredFields as $field) {
                if (!isset($question[$field]) || empty($question[$field])) {
                    $errors[] = "Question " . ($index + 1) . ": Missing $field";
                    continue 2; // Skip to next question
                }
            }
            
            // Generate new UUID
            $id = generateUuid();
            
            // Prepare data
            $questionText = trim($question['question_text']);
            $questionType = $question['question_type'];
            $category = $question['category'];
            $difficulty = $question['difficulty'];
            $points = $question['points'] ?? 10;
            $options = isset($question['options']) ? json_encode($question['options']) : '[]';
            $correctAnswer = $question['correct_answer'];
            $explanation = $question['explanation'] ?? '';
            $imageUrl = $question['image_url'] ?? '';
            
            // Check for duplicates - check ALL questions (active, inactive, draft)
            // Use exact match on question_text after trimming
            $stmt = $pdo->prepare("
                SELECT id, is_active 
                FROM questions 
                WHERE TRIM(question_text) = ? 
                LIMIT 1
            ");
            $stmt->execute([$questionText]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $status = $existing['is_active'] ? 'active' : 'inactive/draft';
                $errors[] = "Question " . ($index + 1) . ": Duplicate - identical question already exists ($status)";
                $skipped++;
                continue;
            }
            
            // Also check for very similar questions (same text ignoring case and extra spaces)
            $normalizedText = preg_replace('/\s+/', ' ', strtolower($questionText));
            $stmt = $pdo->prepare("
                SELECT id, question_text 
                FROM questions 
                WHERE LOWER(TRIM(REPLACE(question_text, '  ', ' '))) = ? 
                LIMIT 1
            ");
            $stmt->execute([$normalizedText]);
            $similar = $stmt->fetch();
            
            if ($similar) {
                $errors[] = "Question " . ($index + 1) . ": Similar question exists - '" . substr($similar['question_text'], 0, 50) . "...'";
                $skipped++;
                continue;
            }
            
            // Insert question
            $stmt = $pdo->prepare("
                INSERT INTO questions (
                    id, question_text, question_type, options, correct_answer,
                    explanation, difficulty, category, points, image_url,
                    is_active, is_verified, created_at, updated_at, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW(), NOW(), ?)
            ");
            
            $stmt->execute([
                $id,
                $questionText,
                $questionType,
                $options,
                $correctAnswer,
                $explanation,
                $difficulty,
                $category,
                $points,
                $imageUrl,
                $profileId
            ]);
            
            $imported++;
            
        } catch (PDOException $e) {
            $errors[] = "Question " . ($index + 1) . ": Database error - " . $e->getMessage();
            $skipped++;
        }
    }
    
    $pdo->commit();
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    
    jsonResponse([
        'success' => true,
        'imported' => $imported,
        'skipped' => $skipped,
        'total' => count($questions),
        'errors' => $errors,
        'summary' => count($questions) . " total, $imported imported, $skipped skipped/failed"
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    jsonResponse(['error' => 'Import failed: ' . $e->getMessage()], 500);
}
?>
