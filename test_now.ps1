# Test Personality Question Import
# This script will test the complete flow

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Testing Personality Question Import" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Check if test file exists
Write-Host "Step 1: Checking test file..." -ForegroundColor Yellow
if (Test-Path "test_personality_question.json") {
    Write-Host "✅ Test file found!" -ForegroundColor Green
    $testQuestion = Get-Content "test_personality_question.json" | ConvertFrom-Json
    Write-Host "   Question: $($testQuestion[0].question)" -ForegroundColor White
    Write-Host "   Answer: $($testQuestion[0].correct_answer)" -ForegroundColor White
} else {
    Write-Host "❌ Test file not found!" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Step 2: Test API connection
Write-Host "Step 2: Testing API connection..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://projects/playqzv4/api/auth/login.php" `
        -Method POST `
        -ContentType "application/json" `
        -Body '{"email":"test","password":"test"}' `
        -ErrorAction Stop
    Write-Host "❌ API returned unexpected success" -ForegroundColor Red
} catch {
    if ($_.Exception.Response.StatusCode -eq 401) {
        Write-Host "✅ API is working (returned 401 for invalid credentials)" -ForegroundColor Green
    } else {
        Write-Host "⚠️  API returned: $($_.Exception.Message)" -ForegroundColor Yellow
    }
}
Write-Host ""

# Step 3: Instructions for manual testing
Write-Host "Step 3: Manual Testing Required" -ForegroundColor Yellow
Write-Host ""
Write-Host "Please complete these steps manually:" -ForegroundColor White
Write-Host ""
Write-Host "1. Open browser: http://localhost:5173/login" -ForegroundColor Cyan
Write-Host "   - Login with your admin credentials" -ForegroundColor White
Write-Host ""
Write-Host "2. Navigate to: http://localhost:5173/admin/questions" -ForegroundColor Cyan
Write-Host "   - Click 'Import from JSON'" -ForegroundColor White
Write-Host "   - Select: test_personality_question.json" -ForegroundColor White
Write-Host "   - Click 'Import Questions'" -ForegroundColor White
Write-Host ""
Write-Host "3. Test the quiz: http://localhost:5173/quiz-config" -ForegroundColor Cyan
Write-Host "   - Select 'Personalities' topic" -ForegroundColor White
Write-Host "   - Start quiz" -ForegroundColor White
Write-Host "   - You should see Geoffrey Hinton's image!" -ForegroundColor White
Write-Host ""

# Step 4: Open browser automatically
Write-Host "Step 4: Opening browser..." -ForegroundColor Yellow
Start-Process "http://localhost:5173/login"
Write-Host "✅ Browser opened!" -ForegroundColor Green
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Ready to test! Follow the steps above." -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
