<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();
$session = authenticate($pdo);

// Verify admin access
if ($session['role'] !== 'admin') {
    jsonResponse(['error' => 'Unauthorized'], 403);
}

$input = getJsonInput();
$topic = $input['topic'] ?? '';
$count = $input['count'] ?? 5;
$difficulty = $input['difficulty'] ?? 'medium';
$type = $input['type'] ?? 'text_mcq';

if (empty($topic)) {
    jsonResponse(['error' => 'Topic is required'], 400);
}

if (empty(AI_API_KEY)) {
    jsonResponse(['error' => 'AI API Key is not configured. Please get a key from openrouter.ai or aistudio.google.com and update api/config.php'], 500);
}

function callGemini($prompt) {
    $model = defined('AI_MODEL') ? AI_MODEL : 'gemini-1.5-flash';
    // Clean model name if it contains 'google/' prefix for OpenRouter compatibility
    $model = str_replace('google/', '', $model);
    
    $url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=" . AI_API_KEY;
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'responseMimeType' => 'application/json'
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }
    
    curl_close($ch);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('Gemini API Error: ' . $json['error']['message']);
    }
    
    if (!isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception('Invalid response format from Gemini');
    }
    
    return $json['candidates'][0]['content']['parts'][0]['text'];
}

function callOpenRouter($prompt) {
    $url = 'https://openrouter.ai/api/v1/chat/completions';
    $model = defined('AI_MODEL') ? AI_MODEL : 'google/gemini-2.0-flash-lite-preview-02-05:free';
    
    $data = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'response_format' => ['type' => 'json_object']
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . AI_API_KEY,
        'HTTP-Referer: https://aiquiz.vibeai.cv', // Required by OpenRouter
        'X-Title: AI Quizzer' // Optional
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }
    
    curl_close($ch);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('OpenRouter API Error: ' . ($json['error']['message'] ?? json_encode($json['error'])));
    }
    
    if (!isset($json['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response format from OpenRouter');
    }
    
    return $json['choices'][0]['message']['content'];
}

// Construct Prompt
$prompt = "Generate $count multiple choice questions (MCQ) about '$topic'. 
Difficulty: $difficulty.
Format: JSON Array.
Each object must have:
- text: The question text
- options: Array of 4 strings
- correct_answer: The correct option string (must be one of the options)
- explanation: Brief explanation of the answer
- category: '$topic'
- type: '$type'

Example:
[
  {
    \"text\": \"What is the capital of France?\",
    \"options\": [\"London\", \"Berlin\", \"Paris\", \"Madrid\"],
    \"correct_answer\": \"Paris\",
    \"explanation\": \"Paris is the capital city of France.\",
    \"category\": \"Geography\",
    \"type\": \"text_mcq\"
  }
]
RETURN ONLY JSON.";

try {
    // Call AI based on provider
    if (defined('AI_PROVIDER') && AI_PROVIDER === 'openrouter') {
        $aiResponse = callOpenRouter($prompt);
    } else {
        $aiResponse = callGemini($prompt);
    }
    
    // Clean up response if it contains markdown code blocks
    $aiResponse = preg_replace('/^```json\s*|\s*```$/', '', trim($aiResponse));
    
    $questions = json_decode($aiResponse, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Failed to parse AI response as JSON: ' . json_last_error_msg());
    }
    
    // Insert into Database
    $pdo->beginTransaction();
    
    $insertedCount = 0;
    foreach ($questions as $q) {
        $id = generateUuid();
        $stmt = $pdo->prepare("INSERT INTO questions (
            id, question_text, question_type, options, correct_answer, explanation, 
            difficulty, category, created_by, is_active, ai_generated, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, NOW())");
        
        $stmt->execute([
            $id,
            $q['text'],
            $type,
            json_encode($q['options']),
            $q['correct_answer'],
            $q['explanation'] ?? '',
            $difficulty,
            $q['category'] ?? $topic,
            $session['user_id']
        ]);
        $insertedCount++;
    }
    
    $pdo->commit();
    
    jsonResponse([
        'success' => true, 
        'message' => "Successfully generated and saved $insertedCount questions as drafts.",
        'count' => $insertedCount
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
