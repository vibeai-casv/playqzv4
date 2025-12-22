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
    jsonResponse(['error' => 'AI API Key is not configured. Please get a key and update api/config.php'], 500);
}

// ============================================================================
// AI PROVIDER FUNCTIONS
// ============================================================================

/**
 * Call Google Gemini API directly
 */
function callGemini($prompt) {
    $model = defined('AI_MODEL') ? AI_MODEL : 'gemini-1.5-flash';
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
            'temperature' => 0.7
        ]
    ];

    $response = makeCurlRequest($url, $data, [
        'Content-Type: application/json'
    ]);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        $msg = $json['error']['message'];
        if ($json['error']['code'] == 429 || stripos($msg, 'quota') !== false) {
             throw new Exception("Gemini Quota Exceeded. Please try again later or switch models. (Original: $msg)");
        }
        throw new Exception('Gemini API Error: ' . $msg);
    }
    
    if (!isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception('Invalid response format from Gemini');
    }
    
    return $json['candidates'][0]['content']['parts'][0]['text'];
}

/**
 * Call OpenRouter API
 */
function callOpenRouter($prompt) {
    $url = 'https://openrouter.ai/api/v1/chat/completions';
    $model = defined('AI_MODEL') ? AI_MODEL : 'google/gemini-2.0-flash-exp:free';
    
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

    $response = makeCurlRequest($url, $data, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . AI_API_KEY,
        'HTTP-Referer: https://aiquiz.vibeai.cv',
        'X-Title: AI Quizzer'
    ]);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('OpenRouter API Error: ' . ($json['error']['message'] ?? json_encode($json['error'])));
    }
    
    if (!isset($json['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response format from OpenRouter');
    }
    
    return $json['choices'][0]['message']['content'];
}

/**
 * Call Groq API
 */
function callGroq($prompt) {
    $url = 'https://api.groq.com/openai/v1/chat/completions';
    $model = defined('AI_MODEL') ? AI_MODEL : 'llama-3.3-70b-versatile';
    
    $data = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant that generates quiz questions. Always respond with valid JSON only.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.7,
        'response_format' => ['type' => 'json_object']
    ];

    $response = makeCurlRequest($url, $data, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . AI_API_KEY
    ]);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('Groq API Error: ' . ($json['error']['message'] ?? json_encode($json['error'])));
    }
    
    if (!isset($json['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response format from Groq');
    }
    
    return $json['choices'][0]['message']['content'];
}

/**
 * Call OpenAI API (or compatible APIs)
 */
function callOpenAI($prompt) {
    $url = 'https://api.openai.com/v1/chat/completions';
    $model = defined('AI_MODEL') ? AI_MODEL : 'gpt-3.5-turbo';
    
    $data = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant that generates quiz questions. Always respond with valid JSON only.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.7,
        'response_format' => ['type' => 'json_object']
    ];

    $response = makeCurlRequest($url, $data, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . AI_API_KEY
    ]);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('OpenAI API Error: ' . ($json['error']['message'] ?? json_encode($json['error'])));
    }
    
    if (!isset($json['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response format from OpenAI');
    }
    
    return $json['choices'][0]['message']['content'];
}

/**
 * Call Anthropic Claude API
 */
function callAnthropic($prompt) {
    $url = 'https://api.anthropic.com/v1/messages';
    $model = defined('AI_MODEL') ? AI_MODEL : 'claude-3-5-haiku-20241022';
    
    $data = [
        'model' => $model,
        'max_tokens' => 4096,
        'messages' => [
            [
                'role' => 'user',
                'content' => $prompt . "\n\nIMPORTANT: Respond with ONLY valid JSON, no markdown, no explanation."
            ]
        ],
        'temperature' => 0.7
    ];

    $response = makeCurlRequest($url, $data, [
        'Content-Type: application/json',
        'x-api-key: ' . AI_API_KEY,
        'anthropic-version: 2023-06-01'
    ]);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('Anthropic API Error: ' . ($json['error']['message'] ?? json_encode($json['error'])));
    }
    
    if (!isset($json['content'][0]['text'])) {
        throw new Exception('Invalid response format from Anthropic');
    }
    
    return $json['content'][0]['text'];
}

/**
 * Call Hugging Face Inference API
 */
function callHuggingFace($prompt) {
    $model = defined('AI_MODEL') ? AI_MODEL : 'meta-llama/Meta-Llama-3-8B-Instruct';
    $url = "https://api-inference.huggingface.co/models/$model";
    
    $data = [
        'inputs' => $prompt . "\n\nRespond with ONLY valid JSON array, no additional text.",
        'parameters' => [
            'temperature' => 0.7,
            'max_new_tokens' => 2048,
            'return_full_text' => false
        ]
    ];

    $response = makeCurlRequest($url, $data, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . AI_API_KEY
    ]);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('Hugging Face API Error: ' . ($json['error'] ?? json_encode($json)));
    }
    
    if (isset($json[0]['generated_text'])) {
        return $json[0]['generated_text'];
    }
    
    throw new Exception('Invalid response format from Hugging Face');
}

/**
 * Call Together AI API
 */
function callTogether($prompt) {
    $url = 'https://api.together.xyz/v1/chat/completions';
    $model = defined('AI_MODEL') ? AI_MODEL : 'meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo';
    
    $data = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant that generates quiz questions. Always respond with valid JSON only.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'temperature' => 0.7,
        'response_format' => ['type' => 'json_object']
    ];

    $response = makeCurlRequest($url, $data, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . AI_API_KEY
    ]);
    
    $json = json_decode($response, true);
    
    if (isset($json['error'])) {
        throw new Exception('Together AI API Error: ' . ($json['error']['message'] ?? json_encode($json['error'])));
    }
    
    if (!isset($json['choices'][0]['message']['content'])) {
        throw new Exception('Invalid response format from Together AI');
    }
    
    return $json['choices'][0]['message']['content'];
}

/**
 * Helper function to make CURL requests
 */
function makeCurlRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 second timeout
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('Curl error: ' . $error);
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 400) {
        throw new Exception("HTTP Error $httpCode: $response");
    }
    
    return $response;
}

/**
 * Main function to call appropriate AI provider
 */
function callAI($prompt) {
    $provider = defined('AI_PROVIDER') ? strtolower(AI_PROVIDER) : 'gemini';
    
    switch ($provider) {
        case 'gemini':
        case 'google':
            return callGemini($prompt);
        
        case 'openrouter':
            return callOpenRouter($prompt);
        
        case 'groq':
            return callGroq($prompt);
        
        case 'openai':
            return callOpenAI($prompt);
        
        case 'anthropic':
        case 'claude':
            return callAnthropic($prompt);
        
        case 'huggingface':
        case 'hf':
            return callHuggingFace($prompt);
        
        case 'together':
            return callTogether($prompt);
        
        default:
            throw new Exception("Unsupported AI provider: $provider. Supported: gemini, openrouter, groq, openai, anthropic, huggingface, together");
    }
}

// ============================================================================
// PROMPT GENERATION FUNCTIONS
// ============================================================================

/**
 * Generate optimized prompt for MCQ questions
 */
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
    \"text\": \"What is the primary advantage of using Transformers over RNNs for natural language processing?\",
    \"options\": [\"Lower computational cost\", \"Ability to process sequences in parallel\", \"Smaller model size\", \"Better performance on small datasets\"],
    \"correct_answer\": \"Ability to process sequences in parallel\",
    \"explanation\": \"Transformers use self-attention mechanisms that allow parallel processing of sequences, unlike RNNs which process sequentially. This makes them much faster to train on modern hardware.\",
    \"category\": \"$topic\",
    \"type\": \"$type\"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Basic definitions, well-known facts, fundamental concepts
- Medium: Application of concepts, comparison between techniques, understanding relationships
- Hard: Advanced concepts, edge cases, optimization strategies, research-level knowledge

Generate $count questions following this format. Ensure diversity within the topic.";
}

/**
 * Generate optimized prompt for AI Personality identification questions
 */
function getPersonalityPrompt($count, $difficulty, $type) {
    return "Generate $count 'Identify the AI Personality' questions about notable figures in Artificial Intelligence.
Difficulty Level: $difficulty

REQUIREMENTS:
- Focus on notable figures in AI: researchers, engineers, founders, thought leaders
- Include people from different eras (pioneers to modern influencers)
- Questions should describe achievements, contributions, or notable facts
- The correct answer should be unambiguous based on the description
- Include diverse representation (gender, geography, specialization)

PERSONALITY CATEGORIES TO COVER:
- AI Pioneers (Alan Turing, John McCarthy, Marvin Minsky, etc.)
- Deep Learning Researchers (Geoffrey Hinton, Yann LeCun, Yoshua Bengio, etc.)
- Tech Leaders & Founders (Sam Altman, Demis Hassabis, Andrew Ng, etc.)
- AI Ethics & Policy Experts (Kate Crawford, Timnit Gebru, etc.)
- Contemporary AI Researchers and Practitioners

OUTPUT FORMAT - Return ONLY a valid JSON array with no markdown, no code blocks:
[
  {
    \"text\": \"This AI researcher, known as one of the 'Godfathers of AI', won the 2018 Turing Award for pioneering work on backpropagation and convolutional neural networks. Currently Chief AI Scientist at Meta.\",
    \"options\": [\"Geoffrey Hinton\", \"Yann LeCun\", \"Yoshua Bengio\", \"Andrew Ng\"],
    \"correct_answer\": \"Yann LeCun\",
    \"explanation\": \"Yann LeCun is the Chief AI Scientist at Meta (Facebook) and is renowned for his work on convolutional neural networks and deep learning. He shared the 2018 Turing Award with Geoffrey Hinton and Yoshua Bengio.\",
    \"category\": \"AI Personalities\",
    \"type\": \"$type\"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Very famous figures, obvious achievements (e.g., 'Father of AI', Turing Test creator)
- Medium: Well-known in AI community but require specific knowledge of contributions
- Hard: Lesser-known specialists, specific research areas, quotes from papers

Generate $count diverse personality questions covering different eras and specializations in AI.";
}

/**
 * Generate optimized prompt for AI Logo identification questions
 */
function getLogoPrompt($count, $difficulty, $type) {
    return "Generate $count 'Identify the AI Logo' questions about AI companies, frameworks, tools, and platforms.
Difficulty Level: $difficulty

REQUIREMENTS:
- Focus on relevant AI/ML companies, frameworks, libraries, and platforms
- Include both established and emerging players
- Questions should describe what the product/company does
- Correct answer is the exact company/product name
- Include diverse categories from the list below

LOGO CATEGORIES TO COVER:
- AI Research Labs (OpenAI, DeepMind, Anthropic, Meta AI, Google AI, Microsoft Research, etc.)
- ML Frameworks & Libraries (TensorFlow, PyTorch, Keras, Scikit-learn, JAX, Hugging Face Transformers, etc.)
- AI Cloud Platforms (Google Cloud AI, AWS SageMaker, Azure AI, Vertex AI, etc.)
- AI Startups & Products (Stability AI, Midjourney, Runway, Cohere, Replicate, etc.)
- AI Development Tools (Weights & Biases, LangChain, LlamaIndex, Pinecone, Weaviate, etc.)
- AI Hardware Companies (NVIDIA, Google TPU, Cerebras, Graphcore, etc.)

OUTPUT FORMAT - Return ONLY a valid JSON array with no markdown, no code blocks:
[
  {
    \"text\": \"Which AI research company, founded by Demis Hassabis in 2010, is known for developing AlphaGo and AlphaFold, and is now a subsidiary of Google?\",
    \"options\": [\"OpenAI\", \"DeepMind\", \"Anthropic\", \"Stability AI\"],
    \"correct_answer\": \"DeepMind\",
    \"explanation\": \"DeepMind, founded in 2010 and acquired by Google in 2014, is famous for creating AlphaGo (which defeated world champions in Go) and AlphaFold (which solved the protein folding problem). It's led by Demis Hassabis.\",
    \"category\": \"AI Companies & Tools\",
    \"type\": \"$type\"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Very popular logos everyone knows (OpenAI, TensorFlow, Google AI, NVIDIA, ChatGPT)
- Medium: Well-known in AI community but not mainstream (Hugging Face, Weights & Biases, Anthropic)
- Hard: Specialized tools, newer startups, research-focused companies (Cerebras, Cohere, Replicate)

DIVERSITY REQUIREMENTS:
- Mix of: Research labs (30%), Frameworks/Tools (30%), Companies/Startups (30%), Hardware/Cloud (10%)
- Include both text-heavy and icon-based logos
- Avoid logos that look too similar in the same question set

Generate $count logo identification questions with diverse and recognizable AI brands.";
}

// ============================================================================
// MAIN LOGIC
// ============================================================================

// Construct Prompt based on question type
switch ($type) {
    case 'image_identify_person':
        $prompt = getPersonalityPrompt($count, $difficulty, $type);
        break;
    
    case 'image_identify_logo':
        $prompt = getLogoPrompt($count, $difficulty, $type);
        break;
    
    case 'text_mcq':
    default:
        $prompt = getMCQPrompt($topic, $count, $difficulty, $type);
        break;
}


try {
    // Call AI
    $aiResponse = callAI($prompt);
    
    // Clean up response - remove markdown code blocks, extra whitespace, etc.
    $aiResponse = trim($aiResponse);
    $aiResponse = preg_replace('/^```json\s*/i', '', $aiResponse);
    $aiResponse = preg_replace('/^```\s*/i', '', $aiResponse);
    $aiResponse = preg_replace('/\s*```$/i', '', $aiResponse);
    $aiResponse = trim($aiResponse);
    
    // Try to extract JSON if it's wrapped in text
    if (!str_starts_with($aiResponse, '[') && !str_starts_with($aiResponse, '{')) {
        preg_match('/\[.*\]/s', $aiResponse, $matches);
        if (!empty($matches[0])) {
            $aiResponse = $matches[0];
        }
    }
    
    $questions = json_decode($aiResponse, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Failed to parse AI response as JSON: ' . json_last_error_msg() . '. Response: ' . substr($aiResponse, 0, 500));
    }
    
    if (!is_array($questions)) {
        throw new Exception('AI response is not an array. Got: ' . gettype($questions));
    }
    
    // Validate question structure
    foreach ($questions as $idx => $q) {
        if (!isset($q['text']) || !isset($q['options']) || !isset($q['correct_answer'])) {
            throw new Exception("Question #$idx is missing required fields (text, options, or correct_answer)");
        }
        if (!is_array($q['options']) || count($q['options']) !== 4) {
            throw new Exception("Question #$idx must have exactly 4 options");
        }
        if (!in_array($q['correct_answer'], $q['options'])) {
            throw new Exception("Question #$idx: correct_answer must be one of the options");
        }
    }
    
    // Insert into Database
    $pdo->beginTransaction();
    
    $insertedCount = 0;
    $skippedCount = 0;
    
    foreach ($questions as $q) {
        $qType = $type;
        $qText = $q['text'];
        $qAnswer = $q['correct_answer'];
        
        // Duplicate Check
        $isDuplicate = false;
        if (in_array($qType, ['image_identify_logo', 'image_identify_person'])) {
            $stmt = $pdo->prepare("SELECT id FROM questions WHERE question_type = ? AND correct_answer = ?");
            $stmt->execute([$qType, $qAnswer]);
            if ($stmt->fetch()) $isDuplicate = true;
        } else {
            $stmt = $pdo->prepare("SELECT id FROM questions WHERE question_text = ?");
            $stmt->execute([$qText]);
            if ($stmt->fetch()) $isDuplicate = true;
        }

        if ($isDuplicate) {
            $skippedCount++;
            continue; 
        }

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
    
    $message = "Successfully generated and saved $insertedCount questions as drafts.";
    if ($skippedCount > 0) {
        $message .= " ($skippedCount duplicates skipped)";
    }
    
    jsonResponse([
        'success' => true, 
        'message' => $message,
        'count' => $insertedCount,
        'skipped' => $skippedCount,
        'provider' => defined('AI_PROVIDER') ? AI_PROVIDER : 'gemini',
        'model' => defined('AI_MODEL') ? AI_MODEL : 'default'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse(['error' => $e->getMessage()], 500);
}
?>
