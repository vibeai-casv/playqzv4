# 🚀 QUICK COPY-PASTE PROMPTS

Copy the entire prompt block and paste directly into ChatGPT, Claude, or any LLM.

---

## 📘 PROMPT 1: MCQ Questions (AI Concepts)

**Customize:** Change topic, number, and difficulty as needed

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

---

## 👤 PROMPT 2: Identify AI Personalities

**Note:** After importing, you'll need to upload photos for each person

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

---

## 🏢 PROMPT 3: Identify AI Logos (Companies & Tools)

**Note:** After importing, you'll need to upload logos for each company/tool

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

---

## ⚡ Usage Steps:

1. **Copy** one of the prompts above
2. **Paste** into ChatGPT/Claude/any LLM
3. **Copy** the JSON output (remove ```json``` markers if present)
4. **Import** via Admin → Import/Export → Import JSON
5. **Upload images** (for Personality/Logo questions only)
6. **Activate** questions and start using!

---

## 🎯 Quick Customizations:

**Change the count:**
- "Generate **20** multiple-choice questions..."

**Change the topic (for MCQ only):**
- "...about **Neural Networks**..."
- "...about **Computer Vision**..."
- "...about **AI Ethics**..."

**Change the difficulty:**
- "Difficulty Level: **easy**"
- "Difficulty Level: **hard**"

---

**That's it! Simple copy-paste and you're done! 🎉**
