<?php
// test_generation_cli.php
require_once 'api/db.php';

// Manually define logic similar to generate_questions.php but for CLI test
$topic = "Science";
$count = 3;
$difficulty = "easy";
$type = "text_mcq";

if (defined('AI_API_KEY') && !empty(AI_API_KEY)) {
    $apiKey = AI_API_KEY;
} else if (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY)) {
    $apiKey = GEMINI_API_KEY;
} else {
    // Attempt to extract from config.php if not defined (though db.php should have loaded it)
    die("Error: No API Key found in config.php. Please check AI_API_KEY or GEMINI_API_KEY.\n");
}

$api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$prompt = "Generate $count multiple-choice questions about '$topic' in the field of Artificial Intelligence.
Difficulty Level: $difficulty

REQUIREMENTS:
- Each question must have exactly 4 options
- OUTPUT FORMAT - Return ONLY a valid JSON array with no markdown, no code blocks:
[
  {
    \"question_text\": \"Example?\",
    \"options\": [\"A\", \"B\", \"C\", \"D\"],
    \"correct_answer\": \"A\",
    \"explanation\": \"Desc\",
    \"category\": \"$topic\",
    \"type\": \"text_mcq\",
    \"difficulty\": \"$difficulty\"
  }
]";

$payload = json_encode([
    "contents" => [["parts" => [["text" => $prompt]]]]
]);

echo "Sending request to Gemini API...\n";
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
    die("Gemini API Error (HTTP $httpCode): " . $response . "\n");
}

$responseData = json_decode($response, true);
$aiText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
$cleanJson = str_replace(['```json', '```'], '', $aiText);
$questions = json_decode(trim($cleanJson), true);

if ($questions && is_array($questions)) {
    echo "Successfully generated " . count($questions) . " questions!\n";
    foreach ($questions as $q) {
        echo "- " . $q['question_text'] . "\n";
    }
} else {
    echo "Failed to parse AI response.\n";
    echo "Raw Response: " . $aiText . "\n";
}
?>
