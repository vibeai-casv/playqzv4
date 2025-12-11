# Testing Personality Question Import with XAMPP

## 🎯 Your Setup

- ✅ XAMPP is running
- ✅ Project accessible at: `http://projects/playqzv4/`
- ✅ API endpoint: `http://projects/playqzv4/api`

## 🚀 Quick Test (3 Steps)

### Step 1: Start Frontend Dev Server

The frontend needs to run separately from XAMPP to use React/Vite:

```powershell
cd client
npm run dev
```

This will start the frontend at `http://localhost:5173`

The `.env` file is already configured to connect to your XAMPP backend.

### Step 2: Login as Admin

1. Open browser: `http://localhost:5173/login`
2. Login with your admin credentials
3. You should be redirected to the admin dashboard

### Step 3: Import Test Question

1. Navigate to: `http://localhost:5173/admin/questions`
2. Click: **"Import from JSON"** button
3. Select file: `test_personality_question.json` (in project root)
4. Click: **"Import Questions"**
5. ✅ Success! You should see: "1 question imported successfully"

## 🧪 Test the Imported Question

### Take a Quiz with the Personality Question

1. Go to: `http://localhost:5173/quiz-config`
2. Configure quiz:
   - **Number of questions**: 5
   - **Difficulty**: Medium (or Mixed)
   - **Topics**: Check ✅ **Personalities**
3. Click: **"Start Quiz"**
4. You should see:
   - Geoffrey Hinton's photo
   - Question: "Identify this AI pioneer"
   - 4 multiple choice options
   - Click image to zoom

### Verify the Answer

- **Correct Answer**: Geoffrey Hinton
- **Explanation**: "Geoffrey Hinton is known as the 'Godfather of AI' and won the 2024 Nobel Prize in Physics for his pioneering work in artificial neural networks and deep learning."

## 🔍 Verify in Database

You can check the database directly in phpMyAdmin:

1. Open: `http://localhost/phpmyadmin`
2. Select your database
3. Run this query:

```sql
SELECT 
    id,
    question_text,
    question_type,
    category,
    difficulty,
    correct_answer,
    image_url,
    is_active,
    created_at
FROM questions
WHERE category = 'Personalities'
ORDER BY created_at DESC
LIMIT 5;
```

## 📊 Import All 15 Personality Questions

Once the test question works, import the full set:

1. Go to: `http://localhost:5173/admin/questions`
2. Click: **"Import from JSON"**
3. Select: `qbank/ai_personalities.json`
4. Click: **"Import Questions"**
5. ✅ 15 questions imported!

## 🛠️ Troubleshooting

### Issue: "Cannot connect to API"

**Check:**
- Is XAMPP running? (Apache and MySQL)
- Can you access: `http://projects/playqzv4/api/` in browser?
- Is the `.env` file in `client/` folder?

**Solution:**
```powershell
# Restart XAMPP services
# Then restart the frontend
cd client
npm run dev
```

### Issue: "Authentication failed"

**Check:**
- Are you logged in?
- Is your session valid?

**Solution:**
1. Logout and login again
2. Check browser console for errors
3. Verify your admin role in database

### Issue: "Import button not visible"

**Check:**
- Are you logged in as admin?
- Check your role in the database:

```sql
SELECT id, email, name, role FROM profiles WHERE email = 'your@email.com';
```

**Solution:**
- Make sure your role is 'admin' or 'super_admin'

### Issue: "Image not displaying in quiz"

**Check:**
- Is the image URL accessible?
- Try opening the URL directly in browser

**Solution:**
- The images are from Wikimedia Commons (public domain)
- They should load automatically
- Check your internet connection

## 📁 File Locations

- **Test Question**: `e:\projects\playqzv4\test_personality_question.json`
- **Full Questions**: `e:\projects\playqzv4\qbank\ai_personalities.json`
- **API Endpoint**: `http://projects/playqzv4/api/questions/import.php`
- **Frontend**: `http://localhost:5173`

## 🎯 Expected Results

### After Import

**In Admin Panel** (`/admin/questions`):
- Total questions count increases by 1
- New question visible in list
- Type: `image_identify_person`
- Category: `Personalities`
- Status: Active ✅

**In Quiz Config** (`/quiz-config`):
- "Personalities" topic available
- Shows count of personality questions

**During Quiz** (`/take-quiz`):
- Image displays correctly
- 4 options appear
- Zoom works on click
- Explanation shows after answering

## 🚀 Alternative: Direct API Test

If you want to test the API directly without the frontend:

```powershell
# First, login to get token
$response = Invoke-RestMethod -Uri "http://projects/playqzv4/api/auth/login.php" `
  -Method POST `
  -ContentType "application/json" `
  -Body '{"email":"admin@example.com","password":"your_password"}'

$token = $response.token

# Import the test question
$questions = Get-Content "test_personality_question.json" | ConvertFrom-Json
$body = @{
    questions = $questions
    skipDuplicates = $true
} | ConvertTo-Json -Depth 10

$result = Invoke-RestMethod -Uri "http://projects/playqzv4/api/questions/import.php" `
  -Method POST `
  -ContentType "application/json" `
  -Headers @{ "Authorization" = "Bearer $token" } `
  -Body $body

$result
```

## 📚 More Information

- `QUICK_TEST_GUIDE.md` - General testing guide
- `TEST_PERSONALITY_QUESTION.md` - Detailed testing instructions
- `AI_PERSONALITY_QUIZ_QUICKSTART.md` - Feature overview
- `AI_PERSONALITY_QUIZ_GUIDE.md` - Complete documentation

## ✅ Ready to Test!

Your XAMPP setup is ready. Just start the frontend dev server and follow the 3 steps above!

```powershell
cd client
npm run dev
```

Then open `http://localhost:5173/login` and start testing! 🎉
