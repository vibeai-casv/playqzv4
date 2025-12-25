<?php
// api/admin/generate_questions.php
header('Content-Type: application/json');
require_once '../db.php';

// Authentication Check
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$topic = $data['topic'] ?? 'General AI';
$count = $data['count'] ?? 5;
$difficulty = $data['difficulty'] ?? 'medium';
$type = $data['type'] ?? 'text_mcq';

// Retrieve API Key from config or environment
if (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY)) {
    $apiKey = GEMINI_API_KEY;
} else if (defined('AI_API_KEY') && !empty(AI_API_KEY)) {
    $apiKey = AI_API_KEY;
} else {
    // Fallback or handle error
    $apiKey = "YOUR_GEMINI_API_KEY"; 
}

$api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

function getMCQPrompt($topic, $count, $difficulty, $type) {
    return "Generate $count multiple-choice questions about '$topic' in the field of Artificial Intelligence.
Difficulty Level: $difficulty

REQUIREMENTS:
- Questions must be clear, unambiguous, and educational
- Focus on AI concepts, technologies, algorithms, ethics, applications, and current trends
- Each question must have exactly 4 options
- Include a mix of conceptual, applied, and factual questions
- Avoid yes/no questions
- Options should be plausible but only one correct
- Explanations should teach the concept, not just state the answer

OUTPUT FORMAT - Return ONLY a valid JSON array with no markdown, no code blocks:
[
  {
    \"question_text\": \"What is the primary advantage of using Transformers over RNNs?\",
    \"options\": [\"Lower computational cost\", \"Ability to process sequences in parallel\", \"Smaller model size\", \"Better performance on small datasets\"],
    \"correct_answer\": \"Ability to process sequences in parallel\",
    \"explanation\": \"Transformers use self-attention mechanisms that allow parallel processing of sequences, unlike RNNs which process sequentially.\",
    \"category\": \"$topic\",
    \"type\": \"$type\",
    \"difficulty\": \"$difficulty\"
  }
]";
}

function getPersonalityPrompt($count, $difficulty, $type) {
    return "Generate $count 'Identify the AI Personality' questions.
Difficulty Level: $difficulty

REQUIREMENTS:
- Focus on notable figures in AI: researchers, engineers, founders, thought leaders
- Include people from different eras (pioneers to modern influencers)
- Questions should describe achievements, contributions, or notable facts
- The correct answer should be unambiguous
- Include diverse representation

PERSONALITY CATEGORIES: AI Pioneers, Deep Learning Researchers, Tech Leaders, AI Ethics Experts, Contemporary Researchers

OUTPUT FORMAT - Return ONLY a valid JSON array:
[
  {
    \"question_text\": \"This AI researcher won the 2018 Turing Award for pioneering work on backpropagation and CNNs. Currently at Meta AI.\",
    \"options\": [\"Geoffrey Hinton\", \"Yann LeCun\", \"Yoshua Bengio\", \"Andrew Ng\"],
    \"correct_answer\": \"Yann LeCun\",
    \"explanation\": \"Yann LeCun is Chief AI Scientist at Meta and renowned for CNNs and deep learning.\",
    \"category\": \"AI Personalities\",
    \"type\": \"$type\",
    \"difficulty\": \"$difficulty\"
  }
]";
}

function getLogoPrompt($count, $difficulty, $type) {
    return "Generate $count 'Identify the AI Logo' questions about AI companies, frameworks, tools, and platforms.
Difficulty Level: $difficulty

REQUIREMENTS:
- Focus on AI/ML companies, frameworks, libraries, and platforms
- Include both established and emerging players
- Questions should describe what the product/company does
- Correct answer is the company/product name
- Include diverse categories

LOGO CATEGORIES: Research Labs (OpenAI, DeepMind), ML Frameworks (TensorFlow, PyTorch), Cloud AI, AI Startups, AI Hardware

OUTPUT FORMAT - Return ONLY a valid JSON array:
[
  {
    \"question_text\": \"Which AI research company, founded by Demis Hassabis, developed AlphaGo and AlphaFold?\",
    \"options\": [\"OpenAI\", \"DeepMind\", \"Anthropic\", \"Stability AI\"],
    \"correct_answer\": \"DeepMind\",
    \"explanation\": \"DeepMind, founded in 2010 and acquired by Google in 2014, created AlphaGo and AlphaFold.\",
    \"category\": \"AI Companies & Tools\",
    \"type\": \"$type\",
    \"difficulty\": \"$difficulty\"
  }
]";
}

// Construct Prompt
switch ($type) {
    case 'image_identify_person':
    case 'personality':
        $prompt = getPersonalityPrompt($count, $difficulty, 'personality');
        break;
    case 'image_identify_logo':
        $prompt = getLogoPrompt($count, $difficulty, $type);
        break;
    default:
        $prompt = getMCQPrompt($topic, $count, $difficulty, 'text_mcq');
        break;
}

$payload = json_encode([
    "contents" => [["parts" => [["text" => $prompt]]]]
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['success' => false, 'error' => 'Gemini API Error: ' . $response]);
    exit;
}

$responseData = json_decode($response, true);
$aiText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';

// Clean Gemini's markdown
$cleanJson = str_replace(['```json', '```'], '', $aiText);
$questions = json_decode(trim($cleanJson), true);

if ($questions && is_array($questions)) {
    $insertedCount = 0;
    foreach ($questions as $q) {
        $stmt = $pdo->prepare("INSERT INTO questions (question_text, options, correct_answer, explanation, category, question_type, difficulty, status, ai_generated) VALUES (?, ?, ?, ?, ?, ?, ?, 'draft', 1)");
        $success = $stmt->execute([
            $q['question_text'] ?? $q['text'] ?? '', 
            json_encode($q['options'] ?? []), 
            $q['correct_answer'] ?? '', 
            $q['explanation'] ?? '',
            $q['category'] ?? $topic,
            $q['type'] ?? $type,
            $q['difficulty'] ?? $difficulty
        ]);
        if ($success) $insertedCount++;
    }
    echo json_encode(['success' => true, 'count' => $insertedCount]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to parse AI response', 'debug' => $aiText]);
}
?>
