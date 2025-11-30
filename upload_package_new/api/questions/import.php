<?php
/**
 * Import Questions from JSON
 * POST /api/questions/import.php
 */

header('Content-Type: application/json');
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

// Set CORS headers
header("Access-Control-Allow-Origin: " . ALLOWED_ORIGIN);
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verify authentication
$user = verifyAuth();
if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON data from request body
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['questions']) || !is_array($input['questions'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input. Expected "questions" array']);
        exit;
    }
    
    $questions = $input['questions'];
    $skipDuplicates = isset($input['skipDuplicates']) ? $input['skipDuplicates'] : true;
    
    $imported = 0;
    $skipped = 0;
    $errors = [];
    
    foreach ($questions as $index => $q) {
        try {
            // Validate required fields
            if (!isset($q['question']) || !isset($q['correct_answer']) || !isset($q['options'])) {
                $errors[] = "Question at index $index: Missing required fields";
                continue;
            }
            
            // Check for duplicates based on question text
            if ($skipDuplicates) {
                $stmt = $pdo->prepare("SELECT id FROM questions WHERE question = ?");
                $stmt->execute([$q['question']]);
                if ($stmt->fetch()) {
                    $skipped++;
                    continue; // Skip this question
                }
            }
            
            // Generate UUID
            $id = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
            
            // Prepare data
            $type = isset($q['type']) ? $q['type'] : 'text_mcq';
            $category = isset($q['category']) ? $q['category'] : 'General';
            $difficulty = isset($q['difficulty']) ? $q['difficulty'] : 'medium';
            $question = $q['question'];
            $options = json_encode($q['options']);
            $correctAnswer = $q['correct_answer'];
            $imageUrl = isset($q['image_url']) ? $q['image_url'] : null;
            $explanation = isset($q['explanation']) ? $q['explanation'] : null;
            
            // Insert question
            $stmt = $pdo->prepare("
                INSERT INTO questions (
                    id, type, category, difficulty, question, 
                    options, correct_answer, image_url, explanation, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $id, $type, $category, $difficulty, $question,
                $options, $correctAnswer, $imageUrl, $explanation
            ]);
            
            $imported++;
            
        } catch (PDOException $e) {
            $errors[] = "Question at index $index: " . $e->getMessage();
        }
    }
    
    echo json_encode([
        'success' => true,
        'imported' => $imported,
        'skipped' => $skipped,
        'errors' => $errors,
        'total_processed' => count($questions)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
