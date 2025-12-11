# AI Personality Quiz Feature

## Overview
The AI Personality Quiz feature allows users to identify prominent figures in the world of Artificial Intelligence from their images, similar to the logo identification feature.

## Features Implemented

### 1. **Question Type Support**
- The database already supports `image_identify_person` question type
- Questions display an image of an AI personality with 4 multiple-choice options
- Users must select the correct name from the options

### 2. **Category: Personalities**
- All personality questions are categorized under "Personalities"
- Subcategories include:
  - AI Pioneers (Geoffrey Hinton, Yann LeCun, Yoshua Bengio, etc.)
  - AI Leaders (Sam Altman, Demis Hassabis, etc.)
  - AI Researchers (Fei-Fei Li, Ian Goodfellow, Andrej Karpathy, etc.)
  - AI Educators (Andrew Ng, etc.)
  - Tech CEOs (Sundar Pichai, Satya Nadella, Elon Musk, etc.)
  - AI Ethics (Timnit Gebru, etc.)

### 3. **Sample Questions Included**
The `qbank/ai_personalities.json` file contains 15 questions featuring:
- **Geoffrey Hinton** - Godfather of AI, 2024 Nobel Prize in Physics
- **Yann LeCun** - Meta Chief AI Scientist, CNN pioneer
- **Sam Altman** - OpenAI CEO
- **Demis Hassabis** - DeepMind CEO, 2024 Nobel Prize in Chemistry
- **Andrew Ng** - Google Brain co-founder, AI educator
- **Fei-Fei Li** - ImageNet creator, Stanford professor
- **Yoshua Bengio** - Turing Award winner
- **Ian Goodfellow** - GAN inventor
- **Sundar Pichai** - Google/Alphabet CEO
- **Ilya Sutskever** - OpenAI Chief Scientist
- **Andrej Karpathy** - Former Tesla AI Director
- **Elon Musk** - xAI founder
- **Timnit Gebru** - AI ethics researcher
- **Satya Nadella** - Microsoft CEO
- **Jürgen Schmidhuber** - LSTM pioneer

## How to Import Questions

### Method 1: Using the Admin Panel (Recommended)
1. Log in to the admin panel at `/admin`
2. Navigate to **Questions** page
3. Click the **"Import from JSON"** button
4. Select the file: `qbank/ai_personalities.json`
5. Click **"Import Questions"**
6. The system will import all 15 personality questions

### Method 2: Using the API Directly
```bash
# Upload the JSON file via the API
curl -X POST https://aiquiz.vibeai.cv/api/questions/import.php \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@qbank/ai_personalities.json"
```

## How Users Take the Quiz

### Step 1: Configure Quiz
1. Go to **Quiz Config** page
2. Select number of questions (5, 10, 20, or 50)
3. Choose difficulty level
4. **Select "Personalities" category**
5. Click **"Start Quiz"**

### Step 2: Take Quiz
- Each question shows an image of an AI personality
- 4 multiple-choice options are provided
- Click on the correct name
- The system auto-advances to the next question
- Click on the image to zoom in for better viewing

### Step 3: View Results
- After completing the quiz, view your score
- See correct answers and explanations
- Learn about each AI personality's contributions

## Question Structure

Each personality question includes:
- **Image**: High-quality photo from Wikimedia Commons
- **Question Text**: "Identify this AI pioneer/researcher/leader"
- **4 Options**: Names of similar AI personalities
- **Correct Answer**: The actual person in the image
- **Explanation**: Brief bio highlighting their key contributions
- **Difficulty**: Easy, Medium, or Hard
- **Tags**: Relevant keywords (e.g., "deep learning", "OpenAI", "Nobel Prize")

## Difficulty Levels

- **Easy**: Well-known figures like Sam Altman, Andrew Ng, Elon Musk, Sundar Pichai, Satya Nadella
- **Medium**: Prominent researchers like Geoffrey Hinton, Yann LeCun, Demis Hassabis, Fei-Fei Li, Ian Goodfellow, Andrej Karpathy
- **Hard**: Specialized researchers like Yoshua Bengio, Ilya Sutskever, Timnit Gebru, Jürgen Schmidhuber

## Adding More Personalities

To add more AI personalities:

1. **Find a suitable image**:
   - Use Wikimedia Commons for copyright-free images
   - Ensure the image clearly shows the person's face
   - Recommended size: 440px width minimum

2. **Create the question JSON**:
```json
{
  "question_text": "Identify this AI researcher",
  "question_type": "image_identify_person",
  "options": ["Person A", "Person B", "Person C", "Person D"],
  "correct_answer": "Person A",
  "explanation": "Brief bio and key contributions",
  "difficulty": "medium",
  "category": "Personalities",
  "subcategory": "AI Researchers",
  "tags": ["relevant", "keywords"],
  "image_url": "https://upload.wikimedia.org/..."
}
```

3. **Import via admin panel** or add to a JSON file and bulk import

## Integration with Existing Features

### Quiz Configuration
- The "Personalities" category appears in the quiz config page
- Shows the number of available personality questions
- Can be combined with other categories for mixed quizzes

### Media Library
- Personality images can be uploaded to the media library
- Type: `personality`
- Can be assigned to questions through the admin panel

### Analytics
- Track which personalities are most/least recognized
- Identify difficulty patterns
- Monitor user performance on personality questions

## Future Enhancements

Potential improvements:
1. **More personalities**: Add researchers from different AI subfields
2. **Historical figures**: Include Alan Turing, John McCarthy, Marvin Minsky
3. **Regional diversity**: Include AI leaders from different countries
4. **Hint system**: Provide clues about their work or achievements
5. **Time-based challenges**: Speed rounds for personality identification
6. **Leaderboards**: Rankings for personality quiz specialists

## Technical Details

### Database Schema
```sql
question_type ENUM('text_mcq', 'image_identify_logo', 'image_identify_person', ...)
category VARCHAR(100) -- 'Personalities'
image_url TEXT -- URL to personality image
```

### Frontend Components
- **QuizConfig.tsx**: Category selection with "Personalities" option
- **TakeQuiz.tsx**: Displays image and handles image zoom
- **QuizResults.tsx**: Shows correct answers with explanations

### Backend APIs
- **GET /api/questions/stats.php**: Returns count of personality questions
- **POST /api/questions/import.php**: Imports personality questions
- **GET /api/quiz/generate.php**: Generates quiz with personality questions

## Notes

- All images are from Wikimedia Commons (public domain or CC-licensed)
- Questions are designed to be educational and informative
- Explanations provide context about each person's contributions to AI
- Difficulty is based on public recognition and prominence in media

## Support

For issues or questions:
- Check the admin diagnostics page
- Review the question import logs
- Ensure images are accessible (not blocked by firewall)
- Verify the "Personalities" category exists in the database
