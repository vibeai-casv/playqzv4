# Logo Identification Questions - AI Generation Prompt

Use this prompt with ChatGPT, Claude, Gemini, or other LLMs to generate logo identification questions.

---

## 🤖 PROMPT FOR LLMs

```
I need you to generate logo identification quiz questions for an educational quiz application.

## Output Format

Generate 20 questions in the following JSON format:

```json
[
  {
    "brand_name": "Nike",
    "logo_description": "A curved swoosh symbol in black or white, suggesting motion and speed. The design is simple, dynamic, and instantly recognizable.",
    "question_text": "Identify the brand from this logo:",
    "options": ["Adidas", "Nike", "Puma", "Reebok"],
    "correct_answer": "Nike",
    "explanation": "The Nike Swoosh, designed by Carolyn Davidson in 1971, represents the wing of Nike, the Greek goddess of victory. It symbolizes movement and speed.",
    "difficulty": "easy",
    "category": "Sports Brands",
    "subcategory": "Athletic Footwear",
    "tags": ["sports", "footwear", "athletic", "iconic"],
    "search_query": "Nike swoosh logo official PNG transparent",
    "points": 10,
    "time_limit_seconds": 30
  }
]
```

## Requirements

### Categories (distribute evenly)
- Technology (Apple, Microsoft, Google, etc.)
- Automotive (Toyota, BMW, Mercedes, etc.)
- Food & Beverage (McDonald's, Coca-Cola, Starbucks, etc.)
- Fashion (Nike, Adidas, Gucci, etc.)
- Sports Teams/Brands
- Entertainment (Netflix, Disney, Warner Bros, etc.)
- Finance (Visa, Mastercard, PayPal, etc.)
- Retail (Amazon, Walmart, Target, etc.)

### Difficulty Distribution
- **40% Easy**: Globally recognized, iconic logos (Apple, McDonald's, Nike, Coca-Cola)
- **40% Medium**: Well-known but less distinctive (Spotify, Airbnb, Slack, Dropbox)
- **20% Hard**: Industry-specific, rebranded, or regional brands

### Question Design Rules
1. **Logo Description**: Describe the logo in detail (colors, shapes, symbols, text) as if someone needs to identify it from the description
2. **Options**: All 4 options must be from the SAME category/industry
3. **Distractors**: Make wrong answers plausible (similar brands, same industry)
4. **Correct Answer**: Must match exactly one of the options
5. **Explanation**: Include interesting facts about the logo's design, history, or meaning
6. **Search Query**: Provide a Google search query to find the official logo image

### Logo Description Guidelines
Include these details:
- **Colors**: Primary and secondary colors
- **Shapes**: Geometric shapes, symbols, icons
- **Text**: Any text/wordmark, font style
- **Style**: Minimalist, vintage, modern, etc.
- **Distinctive features**: What makes it unique

### Example Difficulty Levels

**Easy Examples:**
- Apple (bitten apple)
- McDonald's (golden arches)
- Nike (swoosh)
- Coca-Cola (red script)
- Google (colorful letters)

**Medium Examples:**
- Spotify (green circle with sound waves)
- Airbnb (abstract "A" symbol)
- Slack (colorful hashtag)
- Dropbox (open box)
- Uber (stylized U)

**Hard Examples:**
- Salesforce (cloud with characters)
- SAP (blue letters)
- Oracle (red text)
- Siemens (teal text)
- Deloitte (green dot pattern)

## Additional Guidelines

1. **Global Recognition**: Focus on internationally known brands
2. **Current Logos**: Use current logo designs, not outdated versions
3. **No Ambiguity**: Correct answer should be clear and unambiguous
4. **Educational Value**: Explanations should teach something interesting
5. **Fair Difficulty**: Difficulty should match actual recognition level

## Output

Generate exactly 20 questions following this format. Ensure:
- Valid JSON format
- All required fields present
- Realistic difficulty distribution
- Diverse categories
- High-quality, educational content
```

---

## 📋 ALTERNATIVE PROMPT (Simpler Version)

```
Generate 20 logo identification quiz questions in JSON format.

For each question:
1. Choose a well-known brand
2. Describe its logo in detail (colors, shapes, symbols)
3. Provide 4 multiple choice options (all from same industry)
4. Include the correct answer
5. Add an interesting explanation about the logo
6. Rate difficulty: easy/medium/hard
7. Assign a category

Format:
{
  "brand_name": "Brand Name",
  "logo_description": "Detailed description of the logo",
  "question_text": "Identify the brand from this logo:",
  "options": ["Option A", "Option B", "Option C", "Option D"],
  "correct_answer": "Option B",
  "explanation": "Interesting fact about the logo",
  "difficulty": "easy",
  "category": "Technology",
  "search_query": "brand name logo PNG"
}

Categories: Technology, Automotive, Food & Beverage, Fashion, Sports, Entertainment, Finance, Retail

Difficulty:
- Easy: 40% (very famous logos)
- Medium: 40% (well-known logos)
- Hard: 20% (industry-specific logos)
```

---

## 🎯 CATEGORY-SPECIFIC PROMPTS

### Technology Brands
```
Generate 10 logo identification questions for TECHNOLOGY brands.

Include:
- Tech giants: Apple, Microsoft, Google, Amazon
- Social media: Facebook/Meta, Twitter/X, Instagram, TikTok
- Software: Adobe, Salesforce, Oracle, SAP
- Hardware: Samsung, Dell, HP, Lenovo

Mix of easy (Apple, Google) and hard (Salesforce, Oracle) questions.
```

### Food & Beverage
```
Generate 10 logo identification questions for FOOD & BEVERAGE brands.

Include:
- Fast food: McDonald's, KFC, Burger King, Subway
- Beverages: Coca-Cola, Pepsi, Starbucks, Red Bull
- Snacks: Lay's, Doritos, Pringles, Oreo
- Restaurants: Pizza Hut, Domino's, Taco Bell

Mix of easy (McDonald's, Coca-Cola) and hard (regional chains) questions.
```

### Automotive
```
Generate 10 logo identification questions for AUTOMOTIVE brands.

Include:
- Luxury: Mercedes-Benz, BMW, Audi, Lexus
- Mass market: Toyota, Honda, Ford, Volkswagen
- Sports: Ferrari, Porsche, Lamborghini
- Electric: Tesla, Rivian, Lucid

Mix of easy (Mercedes, BMW) and hard (newer brands) questions.
```

---

## 📝 SAMPLE OUTPUT

Here's what you should expect from the LLM:

```json
[
  {
    "brand_name": "Apple",
    "logo_description": "A bitten apple silhouette in solid black or white, with a clean, minimalist design. The bite is on the right side, and there's a small leaf at the top.",
    "question_text": "Identify the brand from this logo:",
    "options": ["Microsoft", "Apple", "Samsung", "Google"],
    "correct_answer": "Apple",
    "explanation": "The Apple logo, designed by Rob Janoff in 1977, is one of the most recognizable logos worldwide. The bite was added to ensure it wasn't mistaken for a cherry.",
    "difficulty": "easy",
    "category": "Technology",
    "subcategory": "Consumer Electronics",
    "tags": ["technology", "computers", "smartphones", "iconic"],
    "search_query": "Apple logo official PNG transparent",
    "points": 10,
    "time_limit_seconds": 20
  },
  {
    "brand_name": "Spotify",
    "logo_description": "A green circle with three curved lines (sound waves) in black, creating a modern and dynamic appearance. The design suggests audio and streaming.",
    "question_text": "Identify the brand from this logo:",
    "options": ["Spotify", "Pandora", "SoundCloud", "Apple Music"],
    "correct_answer": "Spotify",
    "explanation": "Spotify's logo represents sound waves, symbolizing music streaming. The vibrant green color (#1DB954) has become synonymous with the brand since its launch in 2008.",
    "difficulty": "medium",
    "category": "Entertainment",
    "subcategory": "Music Streaming",
    "tags": ["music", "streaming", "audio", "entertainment"],
    "search_query": "Spotify logo official PNG transparent",
    "points": 15,
    "time_limit_seconds": 30
  }
]
```

---

## 🔄 POST-PROCESSING STEPS

After getting the JSON output from the LLM:

### 1. Validate JSON
```javascript
// Check if valid JSON
try {
  const questions = JSON.parse(llmOutput);
  console.log(`✓ Valid JSON with ${questions.length} questions`);
} catch (e) {
  console.error('✗ Invalid JSON format');
}
```

### 2. Download Logo Images
```bash
# Use the search_query field to find images
# Download to: public/uploads/logo/
# Naming: brand-name-logo.png
```

### 3. Upload to Server
```javascript
// For each question, upload the logo
for (const q of questions) {
  const logoFile = await fetch(`./logos/${q.brand_name.toLowerCase()}-logo.png`);
  const media = await uploadMedia(logoFile, 'logo');
  q.media_id = media.id;
  q.image_url = media.url;
}
```

### 4. Import Questions
```javascript
// Bulk import to database
for (const q of questions) {
  await createQuestion({
    question_text: q.question_text,
    question_type: 'image_identify_logo',
    media_id: q.media_id,
    image_url: q.image_url,
    options: q.options,
    correct_answer: q.correct_answer,
    explanation: q.explanation,
    difficulty: q.difficulty,
    category: q.category,
    tags: q.tags,
    points: q.points,
    time_limit_seconds: q.time_limit_seconds
  });
}
```

---

## 💡 TIPS FOR BEST RESULTS

1. **Be Specific**: Tell the LLM exactly what you want
2. **Provide Examples**: Show 2-3 complete examples
3. **Set Constraints**: Specify exact number, format, categories
4. **Iterate**: If output isn't perfect, refine the prompt
5. **Validate**: Always check the JSON before importing
6. **Test**: Try a few questions manually before bulk import

---

## 🚀 QUICK START

1. Copy one of the prompts above
2. Paste into ChatGPT, Claude, or Gemini
3. Review the generated JSON
4. Download logo images using the search queries
5. Upload logos to your server
6. Import questions to database

---

**Ready to generate amazing logo questions! 🎨**
