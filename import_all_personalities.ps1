# Import All Personality Questions
# Maps qbank/ai_personalities.json format to API format

Write-Host "Started Import Process..." -ForegroundColor Cyan

# 1. Login
$loginUrl = "http://projects/playqzv4/api/auth/login.php"
$loginBody = @{
    email = "testadmin@local.test"
    password = "testadmin123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-WebRequest -Uri $loginUrl -Method POST -Body $loginBody -ContentType "application/json"
    $token = ($loginResponse.Content | ConvertFrom-Json).token
    Write-Host "Login Successful" -ForegroundColor Green
} catch {
    Write-Host "Login Failed: $($_.Exception.Message)" -ForegroundColor Red
    exit
}

# 2. Prepare Data
$sourceFile = "qbank/ai_personalities.json"
if (-not (Test-Path $sourceFile)) {
    Write-Host "File not found: $sourceFile" -ForegroundColor Red
    exit
}

$questions = Get-Content $sourceFile | ConvertFrom-Json
Write-Host "Loaded $($questions.Count) questions from file." -ForegroundColor Yellow

# Map fields: question_text -> question, question_type -> type
$formattedQuestions = $questions | ForEach-Object {
    @{
        question = $_.question_text
        type = $_.question_type
        options = $_.options
        correct_answer = $_.correct_answer
        explanation = $_.explanation
        difficulty = $_.difficulty
        category = $_.category
        subcategory = $_.subcategory
        tags = $_.tags
        image_url = $_.image_url
        points = 10
    }
}

$importPayload = @{
    questions = $formattedQuestions
    skipDuplicates = $true
} | ConvertTo-Json -Depth 10

# 3. Import
$importUrl = "http://projects/playqzv4/api/questions/import.php"
try {
    $importResponse = Invoke-WebRequest -Uri $importUrl -Method POST -Headers @{ "Authorization" = "Bearer $token" } -Body $importPayload -ContentType "application/json"
    $result = $importResponse.Content | ConvertFrom-Json
    
    Write-Host ""
    Write-Host "Import Complete!" -ForegroundColor Green
    Write-Host "--------------------------------" -ForegroundColor Cyan
    Write-Host "Imported: $($result.imported)" -ForegroundColor Green
    Write-Host "Skipped:  $($result.skipped)" -ForegroundColor Yellow
    Write-Host "--------------------------------" -ForegroundColor Cyan
    
    if ($result.errors.Count -gt 0) {
        Write-Host "Errors encountered:" -ForegroundColor Red
        $result.errors | ForEach-Object { Write-Host "- $_" }
    }
} catch {
    Write-Host "Import Request Failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Response: $($_.ErrorDetails.Message)"
}
