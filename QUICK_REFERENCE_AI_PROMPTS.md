# 🚀 Quick Reference: AI Question Generation

## Copy These Prompts to Your Web Interface

---

### ✅ **1. MCQ Questions (Multiple Choice - Text Based)**

**When to use:** Generate traditional multiple-choice questions about AI concepts

**Settings for Web Interface:**
```
Type: text_mcq
Topic: [Enter specific AI topic]
Count: 5-20
Difficulty: easy | medium | hard
```

**Example Topics:**
- Neural Networks
- Natural Language Processing
- Computer Vision
- Machine Learning Algorithms
- AI Ethics and Fairness
- Deep Learning Architectures
- Reinforcement Learning
- Generative AI
- Large Language Models
- AI in Production/MLOps

**Best Practices:**
- Be specific with topics for better quality
- Start with 5 questions to test
- Use "medium" difficulty for general knowledge
- Use "hard" for technical depth

---

### ✅ **2. Identify Personality (Image-Based - AI Personalities)**

**When to use:** Generate questions about famous AI researchers, founders, and influencers

**Settings for Web Interface:**
```
Type: image_identify_person
Topic: (leave empty - auto-focuses on AI personalities)
Count: 5-15
Difficulty: easy | medium | hard
```

**What You'll Get:**
- Questions describing achievements of AI personalities
- 4 multiple-choice options with famous AI figures
- Correct answer is the person's name
- **IMPORTANT:** You'll need to upload photos separately via admin panel

**Difficulty Levels:**
- **Easy:** Very famous (Alan Turing, Geoffrey Hinton, Sam Altman)
- **Medium:** Well-known researchers (Yann LeCun, Fei-Fei Li, Andrew Ng)
- **Hard:** Specialized researchers, ethics experts (Kate Crawford, Timnit Gebru)

**Next Steps After Generation:**
1. Questions are saved as drafts
2. Go to Question Management in admin panel
3. Upload corresponding photos for each personality
4. Activate questions once images are uploaded

---

### ✅ **3. Identify Logo (Image-Based - AI Companies/Tools)**

**When to use:** Generate questions about AI company logos, frameworks, and platforms

**Settings for Web Interface:**
```
Type: image_identify_logo
Topic: (leave empty - auto-focuses on AI companies/tools)
Count: 5-20
Difficulty: easy | medium | hard
```

**What You'll Get:**
- Questions describing AI companies, frameworks, or tools
- 4 multiple-choice options with company/product names
- Correct answer is the exact company/product name
- **IMPORTANT:** You'll need to upload logos separately via admin panel

**Difficulty Levels:**
- **Easy:** Mainstream logos (OpenAI, TensorFlow, NVIDIA, Google AI)
- **Medium:** Known in AI community (Hugging Face, Anthropic, PyTorch)
- **Hard:** Specialized tools (Cerebras, Weights & Biases, Pinecone)

**Categories Covered:**
- Research Labs (30%) - OpenAI, DeepMind, Anthropic
- Frameworks/Tools (30%) - TensorFlow, PyTorch, Keras
- Companies/Startups (30%) - Stability AI, Midjourney, Cohere
- Hardware/Cloud (10%) - NVIDIA, AWS SageMaker, Azure AI

**Next Steps After Generation:**
1. Questions are saved as drafts
2. Go to Question Management in admin panel
3. Download and upload official logos for each company/tool
4. Activate questions once images are uploaded

---

## 📊 Recommended Workflow

### For Building a Complete Quiz:

1. **Generate MCQ Base** (10-15 questions)
   - Type: `text_mcq`
   - Topic: Your main AI topic
   - Difficulty: `medium`

2. **Add Personality Questions** (5-8 questions)
   - Type: `image_identify_person`
   - Difficulty: `easy` or `medium`
   - Upload photos afterward

3. **Add Logo Questions** (5-8 questions)
   - Type: `image_identify_logo`
   - Difficulty: `medium`
   - Upload logos afterward

4. **Review & Activate**
   - Check all generated questions in drafts
   - Edit any that need improvement
   - Upload all required images
   - Activate the best questions

---

## ⚡ Quick Tips

### For Best Results:

**MCQ Questions:**
- Use specific topics instead of broad ones
  - ✅ Good: "Transformer Architecture"
  - ❌ Too broad: "AI"
- Mix difficulty levels for variety
- Generate in smaller batches (5-10) for quality control

**Personality Questions:**
- Easy difficulty gives you household names
- Medium gives diverse but recognizable figures
- Hard includes specialists and ethics experts
- Always have photos ready or plan to find them

**Logo Questions:**
- Easy = logos everyone recognizes
- Medium = AI practitioners know these
- Hard = cutting-edge tools and startups
- Use official brand logos from company websites

### Avoiding Common Issues:

1. **Start Small:** Generate 3-5 questions first to test
2. **Check Duplicates:** System auto-skips, but review manually
3. **Review Before Activating:** All generated questions start as drafts
4. **Have Images Ready:** For logo/personality, prepare to upload images
5. **Monitor AI Quotas:** If errors occur, check API limits

---

## 🔧 Troubleshooting

**Error: "Failed to parse AI response as JSON"**
- Try reducing count (generate fewer questions)
- Switch AI provider in config.php
- Contact admin if persists

**Error: "Quota exceeded"**
- Free tier limit reached
- Wait for quota reset or upgrade
- Try alternative AI provider

**Questions are low quality**
- Increase difficulty level
- Use more specific topics for MCQ
- Try different AI model in config

**Need to change question after generating**
- All questions save as drafts first
- Edit them in Question Management panel
- Delete and regenerate if needed

---

## 📝 Example Session

**Scenario:** Building a quiz on "Large Language Models"

1. **Generate MCQ Base:**
   - Type: `text_mcq`
   - Topic: `Large Language Models`
   - Count: `10`
   - Difficulty: `medium`

2. **Generate Personalities:**
   - Type: `image_identify_person`
   - Count: `8`
   - Difficulty: `medium`
   - (Will get: Altman, Hinton, LeCun, Bengio, etc.)

3. **Generate Company Logos:**
   - Type: `image_identify_logo`
   - Count: `8`
   - Difficulty: `easy`
   - (Will get: OpenAI, Anthropic, Hugging Face, etc.)

4. **Review all 26 questions in drafts**

5. **Upload 16 images** (8 personality photos + 8 logos)

6. **Activate final selection** (maybe 20 best questions)

---

**Last Updated:** December 14, 2024  
**Compatible with:** AI Quiz Platform v4
