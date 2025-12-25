<?php
/**
 * List Image-Based Questions (Personality & Logo)
 * Returns questions of type 'image_identify_person' and 'image_identify_logo' with their images
 */

require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

try {
    // Verify admin authentication
    $session = authenticate($pdo);
    
    if (!$session || $session['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }

    // Get query parameters
    $type = $_GET['type'] ?? 'all'; // 'all', 'personality', 'logo'
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    // Build the query based on type filter
    $conditions = [];
    $params = [];
    
    if ($type === 'personality') {
        $conditions[] = "(question_type = 'image_identify_person' OR question_type = 'personality')";
    } elseif ($type === 'logo') {
        $conditions[] = "question_type = 'image_identify_logo'";
    } else {
        $conditions[] = "(question_type = 'image_identify_person' OR question_type = 'image_identify_logo' OR question_type = 'personality')";
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM questions $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get questions with images
    $sql = "
        SELECT 
            id,
            question_text,
            question_type,
            image_url,
            options,
            correct_answer,
            difficulty,
            category,
            points,
            is_active,
            CASE WHEN is_active = 1 THEN 'active' ELSE 'inactive' END as status,
            created_at,
            updated_at
        FROM questions
        $whereClause
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse options JSON for each question
    foreach ($questions as &$question) {
        if ($question['options']) {
            $question['options'] = json_decode($question['options'], true);
        }
    }
    
    // Count questions by type
    $personalityCountSql = "SELECT COUNT(*) as count FROM questions WHERE (question_type = 'image_identify_person' OR question_type = 'personality')";
    $personalityCount = $pdo->query($personalityCountSql)->fetch(PDO::FETCH_ASSOC)['count'];
    
    $logoCountSql = "SELECT COUNT(*) as count FROM questions WHERE question_type = 'image_identify_logo'";
    $logoCount = $pdo->query($logoCountSql)->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Count questions with images
    $withImageSql = "
        SELECT COUNT(*) as count 
        FROM questions 
        WHERE (question_type = 'image_identify_person' OR question_type = 'image_identify_logo' OR question_type = 'personality')
        AND image_url IS NOT NULL 
        AND image_url != ''
    ";
    $withImageCount = $pdo->query($withImageSql)->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'questions' => $questions,
        'pagination' => [
            'total' => (int)$totalCount,
            'limit' => $limit,
            'offset' => $offset,
            'hasMore' => ($offset + $limit) < $totalCount
        ],
        'stats' => [
            'personality_count' => (int)$personalityCount,
            'logo_count' => (int)$logoCount,
            'with_image_count' => (int)$withImageCount,
            'total_count' => (int)$personalityCount + (int)$logoCount
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    error_log("Error in list_image_questions.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch image questions',
        'message' => $e->getMessage()
    ]);
}
?>
