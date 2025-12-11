# Testing Personality Question Import

## Quick Test Guide

This guide will help you test adding a personality identification question to your quiz application.

## Test File Created

I've created a test file: `test_personality_question.json` with a single Geoffrey Hinton question.

## Option 1: Test via Admin Panel (Recommended)

### Prerequisites
1. Make sure your development server is running
2. You must be logged in as an admin user

### Steps

1. **Start the development server** (if not already running):
   ```bash
   cd client
   npm run dev
   ```

2. **Navigate to the admin questions page**:
   - Open your browser to: `http://localhost:5173/admin/questions`
   - Or if using production: `https://aiquiz.vibeai.cv/admin/questions`

3. **Import the test question**:
   - Click the **"Import from JSON"** button
   - Select the file: `test_personality_question.json`
   - Click **"Import Questions"**
   - You should see: "✅ 1 question imported successfully"

4. **Verify the import**:
   - The question should appear in the questions list
   - Filter by category: "Personalities"
   - You should see: "Identify this AI pioneer"

5. **Test in a quiz**:
   - Go to `/quiz-config`
   - Select:
     - Number of questions: 5
     - Difficulty: Medium
     - Topics: Check ✅ **Personalities**
   - Click "Start Quiz"
   - You should see Geoffrey Hinton's image with 4 options

## Option 2: Test via API (Direct)

If you want to test the API directly using curl or Postman:

### Using PowerShell

```powershell
# First, login to get a token
$loginResponse = Invoke-RestMethod -Uri "http://localhost/playqzv4/api/auth/login.php" `
  -Method POST `
  -ContentType "application/json" `
  -Body '{"email":"admin@example.com","password":"your_password"}'

$token = $loginResponse.token

# Read the test file
$questions = Get-Content "test_personality_question.json" | ConvertFrom-Json

# Import the question
$importBody = @{
    questions = $questions
    skipDuplicates = $true
} | ConvertTo-Json -Depth 10

$importResponse = Invoke-RestMethod -Uri "http://localhost/playqzv4/api/questions/import.php" `
  -Method POST `
  -ContentType "application/json" `
  -Headers @{ "Authorization" = "Bearer $token" } `
  -Body $importBody

$importResponse
```

## Option 3: Import All 15 Personality Questions

To import all 15 AI personality questions from the existing file:

1. **Via Admin Panel**:
   - Go to `/admin/questions`
   - Click "Import from JSON"
   - Select: `qbank/ai_personalities.json`
   - Click "Import Questions"
   - You should see: "✅ 15 questions imported successfully"

2. **Via API**:
   ```powershell
   # Read the full personality questions file
   $questions = Get-Content "qbank/ai_personalities.json" | ConvertFrom-Json
   
   # Import all questions
   $importBody = @{
       questions = $questions
       skipDuplicates = $true
   } | ConvertTo-Json -Depth 10
   
   $importResponse = Invoke-RestMethod -Uri "http://localhost/playqzv4/api/questions/import.php" `
     -Method POST `
     -ContentType "application/json" `
     -Headers @{ "Authorization" = "Bearer $token" } `
     -Body $importBody
   
   $importResponse
   ```

## Expected Results

### After Successful Import

1. **In Admin Panel** (`/admin/questions`):
   - Total questions count increases by 1 (or 15 for full import)
   - New question appears with:
     - Type: `image_identify_person`
     - Category: `Personalities`
     - Difficulty: `medium`
     - Status: Active ✅

2. **In Quiz Configuration** (`/quiz-config`):
   - "Personalities" topic is available to select
   - Shows count of available personality questions

3. **During Quiz** (`/take-quiz`):
   - Image displays correctly (from Wikimedia Commons)
   - 4 multiple choice options appear
   - Click to zoom functionality works
   - After answering, explanation is shown

## Troubleshooting

### Issue: "Authentication failed"
- **Solution**: Make sure you're logged in as an admin user
- Check that your session token is valid

### Issue: "Question already exists" (skipped)
- **Solution**: This is normal if `skipDuplicates` is true
- The question was already imported before
- To re-import, delete the existing question first

### Issue: "Invalid input format"
- **Solution**: Check that the JSON file is properly formatted
- Ensure all required fields are present:
  - `question` (question text)
  - `type` (question_type)
  - `options` (array of 4 options)
  - `correct_answer`
  - `category`
  - `difficulty`

### Issue: Image not displaying
- **Solution**: 
  - Check that `image_url` is valid
  - Wikimedia Commons URLs should work
  - Test the URL directly in a browser

## Database Verification

To verify the question was added to the database:

```sql
-- Check if the question exists
SELECT 
    id,
    question_text,
    question_type,
    category,
    difficulty,
    correct_answer,
    is_active
FROM questions
WHERE category = 'Personalities'
ORDER BY created_at DESC
LIMIT 5;
```

## Next Steps

After successfully testing:

1. ✅ Import the full set of 15 personality questions
2. ✅ Test taking a quiz with personality questions
3. ✅ Verify the results page shows images and explanations
4. ✅ Check analytics to see personality question statistics
5. ✅ Consider adding more personalities (see `AI_PERSONALITY_QUIZ_GUIDE.md`)

## Files Reference

- `test_personality_question.json` - Single test question (Geoffrey Hinton)
- `qbank/ai_personalities.json` - Full set of 15 personality questions
- `AI_PERSONALITY_QUIZ_QUICKSTART.md` - Quick start guide
- `AI_PERSONALITY_QUIZ_GUIDE.md` - Comprehensive documentation
- `AI_PERSONALITY_QUIZ_SUMMARY.md` - Implementation details

## Support

If you encounter any issues:
1. Check the browser console for errors
2. Check the network tab to see API responses
3. Verify your admin role in the database
4. Ensure the backend API is running correctly
