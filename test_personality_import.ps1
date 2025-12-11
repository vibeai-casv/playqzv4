# Test Personality Question Import - Automated Script
# This script will help you test importing a personality identification question

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Personality Question Import Test" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if test file exists
$testFile = "test_personality_question.json"
if (-not (Test-Path $testFile)) {
    Write-Host "❌ Error: Test file not found: $testFile" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Test file found: $testFile" -ForegroundColor Green
Write-Host ""

# Display test question details
Write-Host "📋 Test Question Details:" -ForegroundColor Yellow
$testQuestion = Get-Content $testFile | ConvertFrom-Json
Write-Host "  Question: $($testQuestion[0].question)" -ForegroundColor White
Write-Host "  Type: $($testQuestion[0].type)" -ForegroundColor White
Write-Host "  Category: $($testQuestion[0].category)" -ForegroundColor White
Write-Host "  Difficulty: $($testQuestion[0].difficulty)" -ForegroundColor White
Write-Host "  Correct Answer: $($testQuestion[0].correct_answer)" -ForegroundColor White
Write-Host ""

# Check if full personality questions file exists
$fullFile = "qbank\ai_personalities.json"
if (Test-Path $fullFile) {
    $fullQuestions = Get-Content $fullFile | ConvertFrom-Json
    Write-Host "✅ Full personality questions file found: $fullFile" -ForegroundColor Green
    Write-Host "  Total questions available: $($fullQuestions.Count)" -ForegroundColor White
    Write-Host ""
}

# Instructions
Write-Host "📝 How to Test:" -ForegroundColor Yellow
Write-Host ""
Write-Host "Option 1: Via Admin Panel (Recommended)" -ForegroundColor Cyan
Write-Host "  1. Start dev server: cd client && npm run dev" -ForegroundColor White
Write-Host "  2. Open browser: http://localhost:5173/admin/questions" -ForegroundColor White
Write-Host "  3. Click 'Import from JSON'" -ForegroundColor White
Write-Host "  4. Select: $testFile" -ForegroundColor White
Write-Host "  5. Click 'Import Questions'" -ForegroundColor White
Write-Host ""

Write-Host "Option 2: Via API (Direct)" -ForegroundColor Cyan
Write-Host "  Run: .\test_import_api.ps1" -ForegroundColor White
Write-Host ""

Write-Host "Option 3: Import All 15 Questions" -ForegroundColor Cyan
Write-Host "  1. Go to: http://localhost:5173/admin/questions" -ForegroundColor White
Write-Host "  2. Click 'Import from JSON'" -ForegroundColor White
Write-Host "  3. Select: $fullFile" -ForegroundColor White
Write-Host ""

# Ask user what they want to do
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "What would you like to do?" -ForegroundColor Yellow
Write-Host "  1. Start development server" -ForegroundColor White
Write-Host "  2. View test question JSON" -ForegroundColor White
Write-Host "  3. View all personality questions" -ForegroundColor White
Write-Host "  4. Open testing guide" -ForegroundColor White
Write-Host "  5. Exit" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Enter your choice (1-5)"

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "🚀 Starting development server..." -ForegroundColor Green
        Write-Host ""
        Set-Location client
        npm run dev
    }
    "2" {
        Write-Host ""
        Write-Host "📄 Test Question JSON:" -ForegroundColor Yellow
        Get-Content $testFile | Write-Host
    }
    "3" {
        if (Test-Path $fullFile) {
            Write-Host ""
            Write-Host "📄 All Personality Questions:" -ForegroundColor Yellow
            $questions = Get-Content $fullFile | ConvertFrom-Json
            $i = 1
            foreach ($q in $questions) {
                Write-Host ""
                Write-Host "Question $i:" -ForegroundColor Cyan
                Write-Host "  Text: $($q.question_text)" -ForegroundColor White
                Write-Host "  Answer: $($q.correct_answer)" -ForegroundColor White
                Write-Host "  Difficulty: $($q.difficulty)" -ForegroundColor White
                $i++
            }
        } else {
            Write-Host "❌ File not found: $fullFile" -ForegroundColor Red
        }
    }
    "4" {
        Write-Host ""
        Write-Host "📖 Opening testing guide..." -ForegroundColor Green
        if (Test-Path "TEST_PERSONALITY_QUESTION.md") {
            notepad "TEST_PERSONALITY_QUESTION.md"
        } else {
            Write-Host "❌ Testing guide not found" -ForegroundColor Red
        }
    }
    "5" {
        Write-Host ""
        Write-Host "👋 Goodbye!" -ForegroundColor Green
        exit 0
    }
    default {
        Write-Host ""
        Write-Host "❌ Invalid choice" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "For more information, see:" -ForegroundColor Yellow
Write-Host "  - TEST_PERSONALITY_QUESTION.md" -ForegroundColor White
Write-Host "  - AI_PERSONALITY_QUIZ_QUICKSTART.md" -ForegroundColor White
Write-Host "  - AI_PERSONALITY_QUIZ_GUIDE.md" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan
