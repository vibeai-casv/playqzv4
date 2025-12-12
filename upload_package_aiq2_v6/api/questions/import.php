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

// Validate profile existence and Auto-Fix
$profileId = $session['profile_id'] ?? null;
$userId = $session['user_id'];

// Check if profile actually exists in DB (double check)
$stmt = $pdo->prepare("SELECT id FROM profiles WHERE id = ?");
$stmt->execute([$userId]);
if (!$stmt->fetch()) {
    // Profile missing! Create default profile to satisfy Foreign Key
    try {
        $email = $session['email'] ?? 'admin@system.local';
        $name = 'System Admin'; // Fallback name
        
        $createStmt = $pdo->prepare("
            INSERT INTO profiles (id, email, name, role, created_at, updated_at)
            VALUES (?, ?, ?, 'admin', NOW(), NOW())
        ");
        $createStmt->execute([$userId, $email, $name]);
        $profileId = $userId; // Now valid
    } catch (Exception $e) {
        jsonResponse(['error' => 'Critical: Admin profile missing and could not be auto-created: ' . $e->getMessage()], 500);
    }
} else {
    // If profile exists but session didn't have it (weird join issue?), use user_id as it strictly equals profile_id
    if (!$profileId) $profileId = $userId;
}

try {
    $pdo->beginTransaction();
    
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
    jsonResponse(['error' => 'Import failed: ' . $e->getMessage()], 500);
}
?>
