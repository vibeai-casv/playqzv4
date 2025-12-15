# 📋 Copy-Paste Prompts for External LLM Use

Use these prompts in ChatGPT, Claude, or any LLM browser window. Copy the entire prompt, paste it, and get JSON output that you can directly import into your app.

---

## 🔵 PROMPT 1: MCQ Questions (Multiple Choice - Text Based)

**Copy everything below this line:**

```
Generate 10 multiple-choice questions about "Large Language Models" in the field of Artificial Intelligence.
Difficulty Level: medium

REQUIREMENTS:
- Questions must be clear, unambiguous, and educational
- Focus on AI concepts, technologies, algorithms, ethics, applications, and current trends
- Each question must have exactly 4 options
- Include a mix of conceptual, applied, and factual questions
- Avoid yes/no questions
- Options should be plausible but only one correct
- Explanations should teach the concept, not just state the answer

OUTPUT FORMAT - Return ONLY a valid JSON array with no markdown, no code blocks, no additional text:
[
  {
    "text": "What is the primary advantage of using Transformers over RNNs for natural language processing?",
    "options": ["Lower computational cost", "Ability to process sequences in parallel", "Smaller model size", "Better performance on small datasets"],
    "correct_answer": "Ability to process sequences in parallel",
    "explanation": "Transformers use self-attention mechanisms that allow parallel processing of sequences, unlike RNNs which process sequentially. This makes them much faster to train on modern hardware.",
    "category": "Large Language Models",
    "type": "text_mcq",
    "difficulty": "medium"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Basic definitions, well-known facts, fundamental concepts
- Medium: Application of concepts, comparison between techniques, understanding relationships
- Hard: Advanced concepts, edge cases, optimization strategies, research-level knowledge

Generate 10 questions following this exact format. Ensure diversity within the topic. Return ONLY the JSON array with no markdown formatting.
```

**How to use:**
1. Copy the entire prompt above
2. Paste into ChatGPT, Claude, or any LLM
3. Copy the JSON output (remove any ```json``` markdown if present)
4. Go to your app → Admin → Import/Export → Import JSON
5. Paste and import!

**Customization:**
- Change the number: "Generate **15** multiple-choice questions..."
- Change the topic: "...about **Neural Networks**..."
- Change difficulty: "Difficulty Level: **hard**"

---

## 🟣 PROMPT 2: Identify Personality Questions (AI Researchers & Leaders)

**Copy everything below this line:**

```
Generate 10 "Identify the AI Personality" questions about notable figures in Artificial Intelligence.
Difficulty Level: medium

REQUIREMENTS:
- Focus on notable figures in AI: researchers, engineers, founders, thought leaders
- Include people from different eras (pioneers to modern influencers)
- Questions should describe achievements, contributions, or notable facts
- The correct answer should be unambiguous based on the description
- Include diverse representation (gender, geography, specialization)

PERSONALITY CATEGORIES TO COVER:
- AI Pioneers (Alan Turing, John McCarthy, Marvin Minsky, etc.)
- Deep Learning Researchers (Geoffrey Hinton, Yann LeCun, Yoshua Bengio, etc.)
- Tech Leaders & Founders (Sam Altman, Demis Hassabis, Andrew Ng, Elon Musk, etc.)
- AI Ethics & Policy Experts (Kate Crawford, Timnit Gebru, etc.)
- Contemporary AI Researchers and Practitioners

OUTPUT FORMAT - Return ONLY a valid JSON array with no markdown, no code blocks, no additional text:
[
  {
    "text": "This AI researcher, known as one of the 'Godfathers of AI', won the 2018 Turing Award for pioneering work on backpropagation and convolutional neural networks. Currently Chief AI Scientist at Meta.",
    "options": ["Geoffrey Hinton", "Yann LeCun", "Yoshua Bengio", "Andrew Ng"],
    "correct_answer": "Yann LeCun",
    "explanation": "Yann LeCun is the Chief AI Scientist at Meta (Facebook) and is renowned for his work on convolutional neural networks and deep learning. He shared the 2018 Turing Award with Geoffrey Hinton and Yoshua Bengio.",
    "category": "AI Personalities",
    "type": "image_identify_person",
    "difficulty": "medium"
  }
]

DIFFICULTY GUIDELINES:
- Easy: Very famous figures, obvious achievements (e.g., 'Father of AI', Turing Test creator)
- Medium: Well-known in AI community but require specific knowledge of contributions
- Hard: Lesser-known specialists, specific research areas, quotes from papers

Generate 10 diverse personality questions covering different eras and specializations in AI. Return ONLY the JSON array with no markdown formatting.
```

**How to use:**
1. Copy the entire prompt above
2. Paste into ChatGPT, Claude, or any LLM
3. Copy the JSON output (remove any ```json``` markdown if present)
4. Go to your app → Admin → Import/Export → Import JSON
5. Paste and import!
6. **IMPORTANT:** After import, go to Question Management and upload photos for each personality

**Customization:**
- Change the number: "Generate **15** 'Identify the AI Personality' questions..."
- Change difficulty: "Difficulty Level: **easy**" (for more famous people)

---

## 🟠 PROMPT 3: Identify Logo Questions (AI Companies & Tools)

**Copy everything below this line:**

```
Generate 15 "Identify the AI Logo" questions about AI companies, frameworks, tools, and platforms.
Difficulty Level: medium

REQUIREMENTS:
- Focus on relevant AI/ML companies, frameworks, libraries, and platforms
- Include both established and emerging players
- Questions should describe what the product/company does
- Correct answer is the exact company/product name
- Include diverse categories from the list below

LOGO CATEGORIES TO COVER:
- AI Research Labs (OpenAI, DeepMind, Anthropic, Meta AI, Google AI, Microsoft Research, etc.)
- ML Frameworks & Libraries (TensorFlow, PyTorch, Keras, Scikit-learn, JAX, Hugging Face, etc.)
- AI Cloud Platforms (Google Cloud AI, AWS SageMaker, Azure AI, Vertex AI, etc.)
- AI Startups & Products (Stability AI, Midjourney, Runway, Cohere, Replicate, Perplexity, etc.)
- AI Development Tools (Weights & Biases, LangChain, LlamaIndex, Pinecone, Weaviate, etc.)
- AI Hardware Companies (NVIDIA, Google TPU, Cerebras, Graphcore, etc.)

OUTPUT FORMAT - Return ONLY a valid JSON array with no markdown, no code blocks, no additional text:
[
  {
    "text": "Which AI research company, founded by Demis Hassabis in 2010, is known for developing AlphaGo and AlphaFold, and is now a subsidiary of Google?",
    "options": ["OpenAI", "DeepMind", "Anthropic", "Stability AI"],
    "correct_answer": "DeepMind",
    "explanation": "DeepMind, founded in 2010 and acquired by Google in 2014, is famous for creating AlphaGo (which defeated world champions in Go) and AlphaFold (which solved the protein folding problem). It's led by Demis Hassabis.",
    "category": "AI Companies & Tools",
    "type": "image_identify_logo",
    "difficulty": "medium"
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

Generate 15 logo identification questions with diverse and recognizable AI brands. Return ONLY the JSON array with no markdown formatting.
```

**How to use:**
1. Copy the entire prompt above
2. Paste into ChatGPT, Claude, or any LLM
3. Copy the JSON output (remove any ```json``` markdown if present)
4. Go to your app → Admin → Import/Export → Import JSON
5. Paste and import!
6. **IMPORTANT:** After import, go to Question Management and upload logos for each company/tool

**Customization:**
- Change the number: "Generate **20** 'Identify the AI Logo' questions..."
- Change difficulty: "Difficulty Level: **easy**" (for mainstream logos only)

---

## 🎯 Quick Start Workflow

### Step 1: Generate Questions
1. Choose a prompt above (MCQ, Personality, or Logo)
2. Optionally customize the count, topic, or difficulty
3. Copy and paste into ChatGPT, Claude, or any LLM
4. Wait for JSON response

### Step 2: Clean the Output (if needed)
If the LLM wraps the JSON in markdown, remove it:
- Remove opening: ` ```json `
- Remove closing: ` ``` `
- You should have pure JSON starting with `[` and ending with `]`

### Step 3: Import to Your App
1. Go to Admin Panel → Import/Export
2. Click "Import JSON" tab
3. Paste the JSON
4. Click "Import Questions"
5. Review imported questions in Question Management

### Step 4: For Image-Based Questions (Personality & Logo)
1. Go to Question Management
2. Find the newly imported questions
3. Click "Upload Image" for each question
4. Upload the corresponding photo (personality) or logo (company)
5. Activate the questions once images are uploaded

---

## 📝 Example Topics for MCQ

Feel free to replace "Large Language Models" with any of these:

**AI Fundamentals:**
- Neural Networks
- Machine Learning Algorithms
- Deep Learning Basics
- Supervised vs Unsupervised Learning

**Advanced Topics:**
- Transformer Architecture
- Attention Mechanisms
- Reinforcement Learning
- Generative Adversarial Networks (GANs)

**Applied AI:**
- Natural Language Processing
- Computer Vision
- Speech Recognition
- Recommender Systems

**AI in Practice:**
- MLOps and Model Deployment
- AI Ethics and Bias
- Prompt Engineering
- Fine-tuning Large Models

**Current Trends:**
- Large Language Models (LLMs)
- Diffusion Models
- Retrieval-Augmented Generation (RAG)
- AI Agents and AutoGPT

---

## 🔧 Troubleshooting

### Issue: LLM outputs markdown code blocks
**Solution:** Manually remove ` ```json ` at the start and ` ``` ` at the end

### Issue: JSON has syntax errors
**Solution:** 
- Use a JSON validator (jsonlint.com)
- Ask the LLM: "Please fix this JSON and output only valid JSON"

### Issue: Questions are too generic
**Solution:** 
- Be more specific in topic (e.g., "GPT Architecture" instead of "AI")
- Increase difficulty level to "hard"

### Issue: Duplicates when importing
**Solution:** 
- The app automatically skips duplicates
- You'll see a message showing how many were skipped

### Issue: Need more/fewer questions
**Solution:** Simply change the number in the prompt (e.g., "Generate **20** questions...")

---

## 💡 Pro Tips

1. **Generate in batches:** Do 10-15 at a time for best quality
2. **Mix difficulties:** Generate easy, medium, and hard separately
3. **Diverse topics:** Use different specific topics for variety
4. **Review before import:** Read through the JSON to check quality
5. **Test small first:** Import 5 questions first to test the process
6. **Keep backups:** Save good JSON outputs for future use

---

## 🎨 Combining Question Types

**For a comprehensive AI quiz, use all three:**

1. **MCQ (60%):** 30 questions on various AI topics
2. **Personality (20%):** 10 questions on AI researchers/leaders
3. **Logo (20%):** 10 questions on AI companies/tools

This gives you a well-rounded quiz with variety!

---

**Last Updated:** December 14, 2024  
**Compatible with:** AI Quiz Platform - Import JSON Feature

---

## 📊 Sample Output Preview

When you paste a prompt, you should get output like this:

```json
[
  {
    "text": "What technique allows LLMs to focus on relevant parts of the input sequence?",
    "options": ["Backpropagation", "Self-attention", "Dropout", "Batch normalization"],
    "correct_answer": "Self-attention",
    "explanation": "Self-attention is the core mechanism in Transformers that allows the model to weigh the importance of different tokens in the input sequence.",
    "category": "Large Language Models",
    "type": "text_mcq",
    "difficulty": "medium"
  },
  {
    "text": "Which company developed the GPT series of language models?",
    "options": ["Google", "Meta", "OpenAI", "Microsoft"],
    "correct_answer": "OpenAI",
    "explanation": "OpenAI developed the GPT (Generative Pre-trained Transformer) series, including GPT-3 and GPT-4.",
    "category": "Large Language Models",
    "type": "text_mcq",
    "difficulty": "easy"
  }
]
```

Just copy this entire JSON array and import it!

---

**Happy Question Generating! 🚀**
