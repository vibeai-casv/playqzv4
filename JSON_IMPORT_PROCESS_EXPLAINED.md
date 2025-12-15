# JSON Question Import Process - Detailed Explanation

## Overview

When you import questions from a JSON file in the admin panel, the system processes each question individually, validates it, checks for duplicates, and inserts it into the database.

---

## JSON File Format Expected

```json
{
  "questions": [
    {
      "question_text": "Who is this AI pioneer?",
      "question_type": "image_identify_person",
      "category": "AI Personalities",
      "difficulty": "medium",
      "correct_answer": "Geoffrey Hinton",
      "options": ["Geoffrey Hinton", "Yann LeCun", "Yoshua Bengio", "Andrew Ng"],
      "explanation": "Geoffrey Hinton is known as the Godfather of AI",
      "image_url": "uploads/personalities/hinton.jpg",
      "points": 10
    },
    {
      "question_text": "What is machine learning?",
      "question_type": "text_mcq",
      "category": "Fundamentals",
      "difficulty": "easy",
      "correct_answer": "A subset of AI",
      "options": ["A subset of AI", "A type of hardware", "A database", "An OS"],
      "explanation": "ML is a subset of artificial intelligence",
      "points": 5
    }
  ]
}
```

---

## Step-by-Step Import Process

### 📤 **Step 1: Upload & Authentication**

```
User uploads JSON file → API receives at /api/questions/import.php
```

**Security Checks:**
- ✅ User must be logged in
- ✅ User role must be `admin` or `super_admin`
- ✅ Request must be POST method
- ✅ JSON must be valid and parseable

**What happens:**
```php
$session = authenticate($pdo);
if ($session['role'] !== 'admin' && $session['role'] !== 'super_admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$input = getJsonInput();
$questions = $input['questions'] ?? [];
```

---

### 🔍 **Step 2: Validation**

**Check if questions array exists:**
```php
if (empty($questions)) {
    jsonResponse(['error' => 'No questions provided'], 400);
}

if (!is_array($questions)) {
    jsonResponse(['error' => 'Invalid format. Expected array of questions'], 400);
}
```

**Required fields for each question:**
- ✅ `question_text` - Cannot be empty
- ✅ `question_type` - Must be specified
- ✅ `category` - Must be specified  
- ✅ `difficulty` - Must be specified
- ✅ `correct_answer` - Must be specified

**Optional fields:**
- `options` - Array of choices (defaults to `[]`)
- `explanation` - Description (defaults to empty string)
- `image_url` - Media path (defaults to empty string)
- `points` - Score value (defaults to `10`)

---

### 👤 **Step 3: Determine `created_by` Profile**

**Complex fallback logic to find a valid profile ID:**

```
┌─────────────────────────────────────┐
│ 1. Use session profile_id          │
│    ↓ (if not found)                 │
│ 2. Lookup profile from user_id     │
│    ↓ (if not found)                 │
│ 3. Find ANY admin profile           │
│    ↓ (if not found)                 │
│ 4. Find ANY profile                 │
│    ↓ (if not found)                 │
│ 5. Create system profile            │
└─────────────────────────────────────┘
```

**Step 3.1 - Session Profile:**
```php
$profileId = $session['profile_id'] ?? $userId;
```

**Step 3.2 - Lookup from User ID:**
```php
if (!$profileId && $userId) {
    SELECT id FROM profiles WHERE id = $userId;
}
```

**Step 3.3 - Find Admin Profile:**
```php
if (!$profileId) {
    SELECT id FROM profiles WHERE role IN ('admin', 'super_admin') LIMIT 1;
}
```

**Step 3.4 - Find Any Profile:**
```php
if (!$profileId) {
    SELECT id FROM profiles LIMIT 1;
}
```

**Step 3.5 - Create System Profile:**
```php
if (!$profileId) {
    // Create user record
    INSERT INTO users (id, email, password_hash, ...)
    VALUES (uuid, 'system@import.local', ...);
    
    // Create profile record
    INSERT INTO profiles (id, email, name, role, ...)
    VALUES (uuid, 'system@import.local', 'System Import', 'admin', ...);
}
```

This ensures imports never fail due to missing `created_by` reference.

---

### 🔓 **Step 4: Disable Foreign Key Checks**

```sql
SET FOREIGN_KEY_CHECKS=0;
SET SESSION FOREIGN_KEY_CHECKS=0;
```

**Why?**
- The `created_by` field references `profiles.id`
- Imported questions might reference non-existent profiles
- Disabling FK checks allows import to proceed

**Safety:**
- Applied BEFORE transaction starts
- Applied AGAIN inside transaction (for MySQL compatibility)
- Re-enabled after import completes

---

### 🔄 **Step 5: Begin Transaction**

```php
$pdo->beginTransaction();
```

**Why use transactions?**
- ✅ All questions imported atomically
- ✅ If one fails critically, all rollback
- ✅ Database remains consistent

---

### 📝 **Step 6: Process Each Question**

```
For each question in JSON array:
    ├─ Validate required fields
    ├─ Generate new UUID for question ID
    ├─ Check for exact duplicates
    ├─ Check for similar duplicates
    ├─ Insert if unique
    └─ Track stats (imported/skipped/errors)
```

#### **6.1 - Field Validation**

```php
$requiredFields = ['question_text', 'question_type', 'category', 'difficulty', 'correct_answer'];

foreach ($requiredFields as $field) {
    if (!isset($question[$field]) || empty($question[$field])) {
        $errors[] = "Question {index}: Missing {field}";
        continue; // Skip this question
    }
}
```

If any required field is missing → Question is **skipped**.

---

#### **6.2 - Generate UUID**

```php
$id = generateUuid();
// Example: "a1b2c3d4-e5f6-7890-abcd-ef1234567890"
```

Every imported question gets a **new unique ID**, even if the JSON file contains IDs.

---

#### **6.3 - Exact Duplicate Detection**

```sql
SELECT id, is_active 
FROM questions 
WHERE TRIM(question_text) = ? 
LIMIT 1
```

**Matches if:**
- Question text is **exactly the same** (after trimming whitespace)
- Checks ALL questions (active, inactive, drafts)

**If duplicate found:**
```php
$errors[] = "Question {index}: Duplicate - identical question already exists";
$skipped++;
continue; // Skip to next question
```

---

#### **6.4 - Similar Duplicate Detection**

```sql
SELECT id, question_text 
FROM questions 
WHERE LOWER(TRIM(REPLACE(question_text, '  ', ' '))) = ?
LIMIT 1
```

**Matches if:**
- Question text is **similar** after:
  - Converting to lowercase
  - Trimming whitespace
  - Replacing multiple spaces with single space

**Example:**
```
Import: "What  is  AI?"
Existing: "what is ai?"
→ SIMILAR → Skipped
```

**If similar found:**
```php
$errors[] = "Question {index}: Similar question exists - '{first 50 chars}'";
$skipped++;
continue;
```

---

#### **6.5 - Insert Question**

```sql
INSERT INTO questions (
    id, question_text, question_type, options, correct_answer,
    explanation, difficulty, category, points, image_url,
    is_active, is_verified, created_at, updated_at, created_by
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW(), NOW(), ?)
```

**Field Mapping:**

| JSON Field | Database Column | Transformation | Default |
|------------|----------------|----------------|---------|
| `question_text` | `question_text` | Trimmed | *Required* |
| `question_type` | `question_type` | As-is | *Required* |
| `options` | `options` | Array → JSON string | `[]` |
| `correct_answer` | `correct_answer` | As-is | *Required* |
| `explanation` | `explanation` | As-is | Empty string |
| `difficulty` | `difficulty` | As-is | *Required* |
| `category` | `category` | As-is | *Required* |
| `points` | `points` | As-is | `10` |
| `image_url` | `image_url` | As-is | Empty string |
| - | `id` | Generated UUID | Auto |
| - | `is_active` | Always `0` | `0` (inactive) |
| - | `is_verified` | Always `0` | `0` (not verified) |
| - | `created_by` | From step 3 | Profile ID |
| - | `created_at` | `NOW()` | Current time |
| - | `updated_at` | `NOW()` | Current time |

**Important Notes:**
- ✅ Imported questions are **inactive** by default (`is_active = 0`)
- ✅ Imported questions are **not verified** (`is_verified = 0`)
- ✅ Admin must manually activate them later
- ✅ `options` array is JSON-encoded: `["A","B","C"]` → `'["A","B","C"]'`

---

#### **6.6 - Track Statistics**

```php
if (inserted successfully) {
    $imported++;
} else {
    $skipped++;
    $errors[] = "Question {index}: {error message}";
}
```

---

### ✅ **Step 7: Commit Transaction**

```php
$pdo->commit();
$pdo->exec("SET FOREIGN_KEY_CHECKS=1"); // Re-enable FK checks
```

**All imported questions are now in database.**

---

### 📊 **Step 8: Return Response**

```json
{
  "success": true,
  "imported": 8,
  "skipped": 2,
  "total": 10,
  "errors": [
    "Question 3: Duplicate - identical question already exists (active)",
    "Question 7: Similar question exists - 'What is machine learning?'"
  ],
  "summary": "10 total, 8 imported, 2 skipped/failed"
}
```

**Response Fields:**
- `success` - `true` if at least one question imported
- `imported` - Count of successfully inserted questions
- `skipped` - Count of duplicates or failed questions
- `total` - Total questions in JSON file
- `errors` - Array of error messages with question numbers
- `summary` - Human-readable summary

---

## Error Handling

### ❌ **Critical Errors (Import Fails Completely)**

**1. Authentication Failed**
```json
{
  "error": "Forbidden"
}
```
HTTP 403 - User is not admin/super_admin

**2. Invalid JSON Format**
```json
{
  "error": "No questions provided"
}
```
HTTP 400 - JSON doesn't have `questions` array

**3. Not an Array**
```json
{
  "error": "Invalid format. Expected array of questions"
}
```
HTTP 400 - `questions` is not an array

**4. Database Transaction Failed**
```json
{
  "error": "Import failed: {exception message}"
}
```
HTTP 500 - Database error, all changes rolled back

---

### ⚠️ **Per-Question Errors (Import Continues)**

**1. Missing Required Field**
```
"Question 3: Missing question_type"
```
→ Question skipped, others continue

**2. Exact Duplicate**
```
"Question 5: Duplicate - identical question already exists (active)"
```
→ Question skipped

**3. Similar Duplicate**
```
"Question 7: Similar question exists - 'What is AI?...'"
```
→ Question skipped

**4. Database Constraint Error**
```
"Question 9: Database error - SQLSTATE[23000]: Integrity constraint violation..."
```
→ Question skipped, error logged

---

## Duplicate Detection Logic

### **Exact Match Example:**

**Existing Question:**
```
"What is artificial intelligence?"
```

**Import Attempt:**
```
"  What is artificial intelligence?  "
```

**Result:** ✅ **DUPLICATE** - Trimmed text matches exactly → **Skipped**

---

### **Similar Match Example:**

**Existing Question:**
```
"What is AI?"
```

**Import Attempt:**
```
"WHAT  IS  AI?"
```

**Normalization:**
```
Existing:  "what is ai?"     (lowercase, single spaces)
Import:    "what is ai?"     (lowercase, single spaces)
```

**Result:** ✅ **SIMILAR** - Normalized text matches → **Skipped**

---

### **Different Question Example:**

**Existing Question:**
```
"What is machine learning?"
```

**Import Attempt:**
```
"What is deep learning?"
```

**Result:** ✅ **UNIQUE** - Different text → **Imported**

---

## Database State After Import

### Questions Table:
```sql
id                                  | question_text              | question_type         | is_active | created_by
------------------------------------|----------------------------|-----------------------|-----------|------------
a1b2c3d4-e5f6-7890-abcd-...        | Who is this AI pioneer?    | image_identify_person | 0         | profile_123
b2c3d4e5-f6a7-8901-bcde-...        | What is machine learning?  | text_mcq              | 0         | profile_123
```

**Notice:**
- ✅ New UUIDs generated (not from JSON)
- ✅ `is_active = 0` (inactive by default)
- ✅ `created_by` set to importing admin's profile
- ✅ `created_at` = current timestamp

---

## Comparison: JSON Import vs Bundle Import

| Feature | JSON Import | Bundle Import |
|---------|-------------|---------------|
| **File Format** | `.json` | `.zip` |
| **Contains Images** | No (URLs only) | Yes (extracted) |
| **Media Import** | No | Yes (to uploads/) |
| **Database Tables** | `questions` only | `questions` + `media_library` |
| **Duplicate Check** | Text-based | ID-based |
| **Default State** | Inactive (`is_active=0`) | Active/Preserved |
| **ID Handling** | Always new UUID | Preserves original ID |
| **Use Case** | Quick text questions | Full question+media export/import |

---

## Frontend Usage

**1. User selects JSON file** in Import/Export page

**2. File is read and parsed:**
```javascript
const fileContent = await file.text();
const data = JSON.parse(fileContent);
```

**3. Sent to API:**
```javascript
const response = await fetchAPI('/questions/import.php', {
  method: 'POST',
  body: JSON.stringify({ questions: data.questions })
});
```

**4. Response displayed:**
```
✅ Import successful!
   8 questions imported
   2 questions skipped (duplicates)
```

---

## Logging & Debugging

All profile resolution steps are logged:

```
[PHP Error Log]
Import - Initial userId: 123, profileId: null
Import - Found profile from userId: 123
Import - Final profileId to use: 123
```

**To enable more verbose logging**, check:
- PHP error logs in hosting control panel
- Custom log file: `/public_html/aiq3/import_errors.log`

---

## Summary Flow Chart

```
Upload JSON File
    ↓
Authenticate (Admin/Super Admin)
    ↓
Parse JSON → Extract questions array
    ↓
Determine created_by profile ID
    (Try session → user → admin → any → create)
    ↓
Disable Foreign Key Checks
    ↓
Begin Transaction
    ↓
For Each Question:
    ├─ Validate required fields
    ├─ Generate new UUID
    ├─ Check exact duplicate (trimmed text)
    ├─ Check similar duplicate (normalized text)
    ├─ If unique → INSERT
    ├─ If duplicate → SKIP
    └─ Track stats
    ↓
Commit Transaction
    ↓
Re-enable Foreign Key Checks
    ↓
Return Statistics (imported/skipped/errors)
```

---

## Key Differences from Bundle Import

### JSON Import:
- ✅ **Simpler** - Just questions, no media
- ✅ **Faster** - No file extraction
- ✅ **Text-based** - Duplicate detection by question text
- ✅ **New IDs** - Always generates fresh UUIDs
- ✅ **Inactive** - Questions start inactive
- ⚠️ **No images** - Image URLs must already exist

### Bundle Import:
- ✅ **Complete** - Questions + images + metadata
- ✅ **ID preservation** - Keeps original question IDs
- ✅ **Media handling** - Extracts and registers images
- ✅ **Active state** - Preserves original active status
- ⚠️ **More complex** - ZIP extraction, multiple tables

---

## Best Practices

### ✅ **Use JSON Import When:**
- Adding text-only MCQ questions
- Importing from AI-generated question banks
- Quick bulk import without images
- Questions reference existing image URLs

### ✅ **Use Bundle Import When:**
- Exporting/importing complete question sets
- Moving questions between servers
- Including logo/personality images
- Preserving exact question IDs

---

## Troubleshooting

### Issue: "All questions skipped as duplicates"

**Check:**
```sql
SELECT question_text FROM questions LIMIT 10;
```
Your JSON questions might already exist.

**Fix:** Use unique question text or delete duplicates first.

---

### Issue: "Missing question_type errors"

**Check JSON format:**
```json
{
  "questions": [
    {
      "question_text": "...",
      "question_type": "text_mcq",  ← Must be present
      ...
    }
  ]
}
```

---

### Issue: "Foreign key constraint failure"

**This shouldn't happen** - FK checks are disabled.

**If it does:**
1. Check if `SET FOREIGN_KEY_CHECKS=0` is working
2. Verify MySQL user has permission to disable FK checks
3. Try dropping the constraint manually

---

## Related Files

- **Import API:** `api/questions/import.php`
- **Frontend:** `client/src/pages/admin/ImportExport.tsx`
- **Error Logs:** PHP error log + `import_errors.log`
