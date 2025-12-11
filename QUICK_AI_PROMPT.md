# 🎯 QUICK COPY-PASTE PROMPT FOR AI

Copy this entire prompt and paste it into ChatGPT, Claude, Gemini, or any LLM to generate logo identification questions.

---

## 📋 READY-TO-USE PROMPT

```
I need you to generate 20 logo identification quiz questions for an educational quiz application.

Generate questions in this EXACT JSON format:

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

REQUIREMENTS:

1. CATEGORIES (distribute evenly across all 20 questions):
   - Technology (Apple, Microsoft, Google, Samsung, etc.)
   - Automotive (Toyota, BMW, Mercedes, Tesla, etc.)
   - Food & Beverage (McDonald's, Coca-Cola, Starbucks, etc.)
   - Fashion (Nike, Adidas, Gucci, Zara, etc.)
   - Sports (NBA, FIFA, Olympics, etc.)
   - Entertainment (Netflix, Disney, Warner Bros, etc.)
   - Finance (Visa, Mastercard, PayPal, etc.)
   - Retail (Amazon, Walmart, Target, etc.)

2. DIFFICULTY DISTRIBUTION:
   - 8 questions: EASY (40%) - Globally famous logos everyone knows
   - 8 questions: MEDIUM (40%) - Well-known but less iconic
   - 4 questions: HARD (20%) - Industry-specific or regional brands

3. LOGO DESCRIPTION must include:
   - Colors (be specific: "bright red", "vibrant green", etc.)
   - Shapes (circles, swooshes, letters, symbols)
   - Style (minimalist, vintage, modern, playful)
   - Distinctive features that make it recognizable

4. OPTIONS (all 4 choices):
   - Must be from the SAME category/industry
   - Make wrong answers plausible (similar brands)
   - Correct answer must match one option EXACTLY

5. EXPLANATION should include:
   - Interesting facts about the logo design
   - Designer name or year created (if notable)
   - Meaning or symbolism behind the design
   - Why it's effective or memorable

6. SEARCH QUERY:
   - Provide a Google search term to find the official logo
   - Format: "[Brand name] logo official PNG transparent"

DIFFICULTY EXAMPLES:

EASY (everyone knows these):
- Apple, McDonald's, Nike, Coca-Cola, Google, Amazon, Facebook, Disney

MEDIUM (well-known but less distinctive):
- Spotify, Airbnb, Slack, Dropbox, Uber, LinkedIn, PayPal, eBay

HARD (industry-specific or newer):
- Salesforce, SAP, Oracle, Siemens, Deloitte, Accenture, Workday

OUTPUT:
- Valid JSON array with exactly 20 questions
- Each question must have ALL fields filled
- Ensure variety in categories and difficulty
- Make it educational and interesting!

Generate the 20 questions now.
```

---

## ✅ AFTER YOU GET THE RESPONSE

1. **Copy the JSON output**
2. **Validate it** at jsonlint.com
3. **Save as** `logo-questions.json`
4. **Download logo images** using the search queries
5. **Upload to your server** using the LogoUploader component
6. **Import questions** to your database

---

## 🎨 ALTERNATIVE: CATEGORY-SPECIFIC PROMPT

If you want questions from a specific category only:

```
Generate 20 logo identification questions for TECHNOLOGY brands only.

Use the same JSON format as before, but focus exclusively on:
- Tech giants: Apple, Microsoft, Google, Amazon, Meta
- Social media: Twitter/X, Instagram, TikTok, LinkedIn, Snapchat
- Software: Adobe, Salesforce, Oracle, SAP, Atlassian
- Hardware: Samsung, Dell, HP, Lenovo, ASUS
- Startups: Stripe, Notion, Figma, Canva, Zoom

Mix difficulties:
- EASY: Apple, Google, Microsoft, Amazon, Facebook
- MEDIUM: Spotify, Slack, Dropbox, Zoom, Adobe
- HARD: Salesforce, SAP, Oracle, Workday, ServiceNow

Generate 20 questions in the same JSON format.
```

Replace "TECHNOLOGY" with any category: Food & Beverage, Automotive, Fashion, Sports, etc.

---

## 💡 TIPS FOR BEST RESULTS

1. **Copy the ENTIRE prompt** including all requirements
2. **Don't modify** the JSON format - it's designed to work with your system
3. **If output is incomplete**, ask: "Continue generating the remaining questions"
4. **If JSON is invalid**, ask: "Please fix the JSON formatting errors"
5. **For more variety**, run the prompt multiple times with different categories

---

## 🚀 QUICK START WORKFLOW

1. Copy prompt above
2. Paste into ChatGPT/Claude/Gemini
3. Wait for JSON output (20 questions)
4. Copy JSON to a file
5. Use search_query fields to download logos
6. Upload logos via your admin panel
7. Import questions to database

**That's it! You'll have 20 professional logo questions ready to use!** 🎉

---

## 📝 SAMPLE EXPECTED OUTPUT

You should get something like this:

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
    "brand_name": "McDonald's",
    "logo_description": "Two golden arches forming an 'M' shape on a red background. The arches are bright yellow/gold and create one of the most recognizable symbols in fast food.",
    "question_text": "Identify the brand from this logo:",
    "options": ["Burger King", "McDonald's", "KFC", "Wendy's"],
    "correct_answer": "McDonald's",
    "explanation": "The Golden Arches were introduced in 1962 and represent the architectural arches of early McDonald's restaurants. The logo is recognized by 88% of people worldwide.",
    "difficulty": "easy",
    "category": "Food & Beverage",
    "subcategory": "Fast Food",
    "tags": ["fast-food", "restaurant", "global", "iconic"],
    "search_query": "McDonald's golden arches logo PNG",
    "points": 10,
    "time_limit_seconds": 20
  }
  // ... 18 more questions
]
```

---

**Ready to generate amazing logo questions! Just copy and paste! 📋✨**
