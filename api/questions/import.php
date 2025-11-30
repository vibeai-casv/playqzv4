<?php
/**
 * Import Questions from JSON - Production Schema Compatible
 * POST /api/questions/import.php
 */

header('Content-Type: application/json');

// Include required files
$configPath = dirname(__FILE__) . '/../config.php';
$dbPath = dirname(__FILE__) . '/../db.php';
$utilsPath = dirname(__FILE__) . '/../utils.php';

if (!file_exists($configPath)) {
    echo json_encode(['error' => 'Config file not found']);
    exit;
}

require_once $configPath;
require_once $dbPath;
require_once $utilsPath;

// Set CORS headers
cors();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verify authentication using Token (Standard App Auth)
try {
    $session = authenticate($pdo);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication failed']);
    exit;
}

// Check Admin Role
if ($session['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized. Admin access required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON data
$input = getJsonInput();

if (!isset($input['questions']) || !is_array($input['questions'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input. Expected "questions" array']);
    exit;
}

$questions = $input['questions'];
$skipDuplicates = isset($input['skipDuplicates']) ? $input['skipDuplicates'] : true;

$imported = 0;
$skipped = 0;
$errors = array();

foreach ($questions as $index => $q) {
    try {
        // Validate required fields
        if (!isset($q['question']) || !isset($q['correct_answer']) || !isset($q['options'])) {
            $errors[] = "Question at index $index: Missing required fields";
            continue;
        }
        
        // Check for duplicates (using question_text)
        if ($skipDuplicates) {
            $stmt = $pdo->prepare("SELECT id FROM questions WHERE question_text = ?");
            $stmt->execute(array($q['question']));
            if ($stmt->fetch()) {
                $skipped++;
                continue;
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
        $questionText = $q['question'];
        $options = json_encode($q['options']);
        $correctAnswer = $q['correct_answer'];
        $imageUrl = isset($q['image_url']) ? $q['image_url'] : null;
        $explanation = isset($q['explanation']) ? $q['explanation'] : null;
        $userId = $session['user_id']; // Use the authenticated user ID
        
        // Insert question using CORRECT column names
        $stmt = $pdo->prepare("
            INSERT INTO questions (
                id, 
                question_text, 
                question_type, 
                category, 
                difficulty, 
                options, 
                correct_answer, 
                image_url, 
                explanation, 
                is_active, 
                created_by,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW())
        ");
        
        $stmt->execute(array(
            $id, 
            $questionText, 
            $type, 
            $category, 
            $difficulty, 
            $options, 
            $correctAnswer, 
            $imageUrl, 
            $explanation,
            $userId
        ));
        
        $imported++;
        
    } catch (PDOException $e) {
        $errors[] = "Question at index $index: " . $e->getMessage();
    } catch (Exception $e) {
        $errors[] = "Question at index $index: " . $e->getMessage();
    }
}

echo json_encode(array(
    'success' => true,
    'imported' => $imported,
    'skipped' => $skipped,
    'errors' => $errors,
    'total_processed' => count($questions)
));
?>
