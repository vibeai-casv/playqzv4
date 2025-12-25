# AI Question Generation Prompts for Quiz Application

This document contains specialized prompts for generating different types of AI-related quiz questions through your web interface.

---

## 1. MCQ (Multiple Choice Questions) - AI Topics

### Prompt Template for MCQ Generation:

```
Generate {COUNT} multiple-choice questions about {TOPIC} in the field of Artificial Intelligence.
Difficulty Level: {DIFFICULTY} (easy/medium/hard)

REQUIREMENTS:
- Questions must be clear, unambiguous, and educational
- Focus on AI concepts, technologies, algorithms, ethics, applications, and current trends
- Each question must have exactly 4 options
- Include a mix of conceptual, applied, and factual questions
- Avoid yes/no questions
- Options should be plausible but only one correct
- Shuffle the options array so the correct answer is not always in the same position
- Explanations should teach the concept, not just state the answer

OUTPUT FORMAT - Return ONLY a valid JSON array with no markdown, no code blocks:
[
  {
    "text": "What is the primary advantage of using Transformers over RNNs for natural language processing?",
    "options": [
      "Lower computational cost",
      "Ability to process sequences in parallel",
      "Smaller model size",
      "Better performance on small datasets"
    ],
    "correct_answer": "Ability to process sequences in parallel",
    "explanation": "Transformers use self-attention mechanisms that allow parallel processing of sequences, unlike RNNs which process sequentially. This makes them much faster to train on modern hardware.",
    "category": "{TOPIC}",
    "type": "text_mcq",
    "difficulty": "{DIFFICULTY}"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Basic definitions, well-known facts, fundamental concepts
- Medium: Application of concepts, comparison between techniques, understanding relationships
- Hard: Advanced concepts, edge cases, optimization strategies, research-level knowledge

Generate {COUNT} questions following this format. Ensure diversity in topics within the AI domain.
```

### Example Usage:
- Topic: "Machine Learning Algorithms"
- Count: 5
- Difficulty: medium

---

## 2. Identify Personality - AI Pioneers & Influencers

### Prompt Template for Personality Questions:

```
Generate {COUNT} "Identify the AI Personality" questions.
Difficulty Level: {DIFFICULTY}

REQUIREMENTS:
- Focus on notable figures in AI: researchers, engineers, founders, thought leaders
- Include people from different eras (pioneers to modern influencers)
- Questions should describe achievements, contributions, or notable quotes
- The correct answer should be unambiguous based on the description
- Shuffle the options array so the correct answer is not always in the same position
- Include diverse representation (gender, geography, specialization)

PERSONALITY CATEGORIES:
- AI Pioneers (Turing, McCarthy, Minsky, etc.)
- Deep Learning Researchers (Hinton, LeCun, Bengio, etc.)
- Tech Leaders & Founders (Altman, Hassabis, Ng, etc.)
- AI Ethics & Policy Experts (Kate Crawford, Timnit Gebru, etc.)
- Contemporary AI Researchers and Practitioners

OUTPUT FORMAT - Return ONLY a valid JSON array:
[
  {
    "text": "This AI researcher, known as one of the 'Godfathers of AI', won the 2018 Turing Award for their pioneering work on backpropagation and convolutional neural networks. They currently work at Meta AI.",
    "options": [
      "Geoffrey Hinton",
      "Yann LeCun",
      "Yoshua Bengio",
      "Andrew Ng"
    ],
    "correct_answer": "Yann LeCun",
    "explanation": "Yann LeCun is the Chief AI Scientist at Meta (Facebook) and is renowned for his work on convolutional neural networks and deep learning. He shared the 2018 Turing Award with Geoffrey Hinton and Yoshua Bengio.",
    "category": "AI Personalities",
    "type": "image_identify_person",
    "difficulty": "{DIFFICULTY}",
    "image_required": true,
    "image_search_hint": "Yann LeCun portrait professional photo"
  }
]

IMPORTANT NOTES:
- The "image_identify_person" type means these questions will display a photo of the person
- The "correct_answer" field is the person's name (used for matching with uploaded images)
- Include "image_search_hint" to help admins find the right photo
- Questions should be answerable even without seeing the image (based on description)
- BUT the image will be the primary clue for users

DIFFICULTY GUIDELINES:
- Easy: Very famous figures, obvious achievements (e.g., "Father of AI")
- Medium: Well-known but require specific knowledge of contributions
- Hard: Lesser-known specialists, specific research areas, quotes/papers

Generate {COUNT} diverse personality questions covering different eras and specializations in AI.
```

### Example Usage:
- Count: 10
- Difficulty: medium
- Topics: Mix of pioneers, researchers, and current leaders

---

## 3. Identify Logo - AI Companies & Tools

### Prompt Template for Logo Identification:

```
Generate {COUNT} "Identify the AI Logo" questions about AI companies, frameworks, tools, and platforms.
Difficulty Level: {DIFFICULTY}

REQUIREMENTS:
- Focus on relevant AI/ML companies, frameworks, libraries, and platforms
- Include both established and emerging players
- Questions should describe what the product/company does
- Correct answer is the company/product name (must match logo filename)
- Shuffle the options array so the correct answer is not always in the same position
- Include diverse categories: cloud AI, frameworks, startups, research labs

LOGO CATEGORIES:
- AI Research Labs (OpenAI, DeepMind, Anthropic, Meta AI, etc.)
- ML Frameworks (TensorFlow, PyTorch, Keras, Scikit-learn, etc.)
- AI Cloud Platforms (Google Cloud AI, AWS SageMaker, Azure AI, etc.)
- AI Startups & Products (Stability AI, Midjourney, Hugging Face, etc.)
- AI Hardware (NVIDIA, TPU, Cerebras, etc.)

OUTPUT FORMAT - Return ONLY a valid JSON array:
[
  {
    "text": "Which AI research company, founded by Demis Hassabis, is known for developing AlphaGo and AlphaFold, and is now a subsidiary of Google?",
    "options": [
      "OpenAI",
      "DeepMind",
      "Anthropic",
      "Stability AI"
    ],
    "correct_answer": "DeepMind",
    "explanation": "DeepMind, founded in 2010 and acquired by Google in 2014, is famous for creating AlphaGo (which defeated world champions in Go) and AlphaFold (which solved the protein folding problem). It's led by Demis Hassabis.",
    "category": "AI Companies & Tools",
    "type": "image_identify_logo",
    "difficulty": "{DIFFICULTY}",
    "image_required": true,
    "logo_search_hint": "DeepMind logo official"
  }
]

IMPORTANT NOTES:
- The "image_identify_logo" type means these questions will display the company/product logo
- The "correct_answer" field must be the exact company/product name
- Include "logo_search_hint" to help admins find the official logo
- Questions describe the product/company to give context
- The logo will be the primary visual clue for users

DIFFICULTY GUIDELINES:
- Easy: Very popular logos (OpenAI, TensorFlow, Google AI, NVIDIA)
- Medium: Well-known in AI community but not mainstream (Hugging Face, Weights & Biases)
- Hard: Specialized tools, newer startups, research-focused companies

DIVERSITY REQUIREMENTS:
- Mix of: Research labs (30%), Frameworks/Tools (30%), Companies/Startups (30%), Hardware/Cloud (10%)
- Include both text-heavy and icon-based logos
- Avoid logos that look too similar in the same question set

Generate {COUNT} logo identification questions with diverse and recognizable AI brands.
```

### Example Usage:
- Count: 15
- Difficulty: medium
- Categories: Mix across research, tools, and companies

---

## Implementation Steps for Web Interface

### Step 1: Update `generate_questions.php`

Replace lines 377-403 with conditional logic that uses the appropriate prompt based on `$type`:

```php
// Construct Prompt based on question type
switch ($type) {
    case 'text_mcq':
        $prompt = getMCQPrompt($topic, $count, $difficulty, $type);
        break;
    
    case 'image_identify_person':
        $prompt = getPersonalityPrompt($count, $difficulty, $type);
        break;
    
    case 'image_identify_logo':
        $prompt = getLogoPrompt($count, $difficulty, $type);
        break;
    
    default:
        $prompt = getMCQPrompt($topic, $count, $difficulty, $type);
        break;
}
```

### Step 2: Add Prompt Functions

Add these three functions before the main logic section (around line 372):

```php
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
    \"text\": \"What is the primary advantage of using Transformers over RNNs?\",
    \"options\": [\"Lower computational cost\", \"Ability to process sequences in parallel\", \"Smaller model size\", \"Better performance on small datasets\"],
    \"correct_answer\": \"Ability to process sequences in parallel\",
    \"explanation\": \"Transformers use self-attention mechanisms that allow parallel processing of sequences, unlike RNNs which process sequentially.\",
    \"category\": \"$topic\",
    \"type\": \"$type\"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Basic definitions, well-known facts, fundamental concepts
- Medium: Application of concepts, comparison between techniques
- Hard: Advanced concepts, edge cases, research-level knowledge

Generate $count questions following this format. Ensure diversity within the topic.";
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
    \"text\": \"This AI researcher won the 2018 Turing Award for pioneering work on backpropagation and CNNs. Currently at Meta AI.\",
    \"options\": [\"Geoffrey Hinton\", \"Yann LeCun\", \"Yoshua Bengio\", \"Andrew Ng\"],
    \"correct_answer\": \"Yann LeCun\",
    \"explanation\": \"Yann LeCun is Chief AI Scientist at Meta and renowned for CNNs and deep learning. He shared the 2018 Turing Award with Hinton and Bengio.\",
    \"category\": \"AI Personalities\",
    \"type\": \"$type\"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Very famous figures, obvious achievements
- Medium: Well-known but require specific knowledge
- Hard: Lesser-known specialists, specific research areas

Generate $count diverse personality questions covering different eras and specializations.";
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
    \"text\": \"Which AI research company, founded by Demis Hassabis, developed AlphaGo and AlphaFold?\",
    \"options\": [\"OpenAI\", \"DeepMind\", \"Anthropic\", \"Stability AI\"],
    \"correct_answer\": \"DeepMind\",
    \"explanation\": \"DeepMind, founded in 2010 and acquired by Google in 2014, created AlphaGo and AlphaFold. Led by Demis Hassabis.\",
    \"category\": \"AI Companies & Tools\",
    \"type\": \"$type\"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Very popular (OpenAI, TensorFlow, Google AI, NVIDIA)
- Medium: Well-known in AI community (Hugging Face, Weights & Biases)
- Hard: Specialized tools, newer startups

DIVERSITY: Mix Research labs (30%), Frameworks/Tools (30%), Companies/Startups (30%), Hardware/Cloud (10%)

Generate $count logo identification questions with diverse and recognizable AI brands.";
}
```

---

## Quick Reference - Prompts for Copy-Paste

### For MCQ:
```
Topic: [Enter AI topic like "Neural Networks", "NLP", "Computer Vision", etc.]
Count: [5-20]
Difficulty: [easy/medium/hard]
Type: text_mcq
```

### For Personality:
```
Topic: Not required (automatically focuses on AI personalities)
Count: [5-15]
Difficulty: [easy/medium/hard]
Type: image_identify_person
```

### For Logo:
```
Topic: Not required (automatically focuses on AI companies/tools)
Count: [5-20]
Difficulty: [easy/medium/hard]
Type: image_identify_logo
```

---

## Testing Recommendations

1. **Test with small batches first** (count: 3-5) to verify format
2. **Check for duplicates** - The system has duplicate detection
3. **Review before activating** - Generated questions start as drafts
4. **Upload images separately** - For personality/logo questions, images must be uploaded via admin panel
5. **Validate JSON responses** - If generation fails, check AI provider quotas

---

## Troubleshooting

### Common Issues:

1. **"Failed to parse AI response as JSON"**
   - Some AI models add markdown formatting
   - The system tries to clean this automatically
   - Try switching AI provider in config.php

2. **"Quota exceeded"**
   - Free tier limits reached
   - Wait for quota reset or upgrade plan
   - Switch to alternative provider (OpenRouter, Groq)

3. **Questions lack quality**
   - Increase difficulty for more depth
   - Use more specific topics
   - Try different AI models (GPT-4, Claude, etc.)

4. **Duplicate questions**
   - System automatically skips duplicates
   - Based on question_text (MCQ) or correct_answer (Logo/Personality)

---

Last Updated: December 14, 2024
