# 🚨 Critical Fix: Database Schema Mismatch

## 🔍 The Issue

The diagnostic report revealed a critical mismatch between the `import.php` script and your production database schema.

| Feature | Old Script (Wrong) | Production Database (Correct) |
| :--- | :--- | :--- |
| **Question Text** | `question` | `question_text` |
| **Question Type** | `type` | `question_type` |
| **Status** | `status` | `is_active` |

This mismatch causes SQL errors like `"Unknown column 'question' in 'field list'"`.

---

## ✅ The Fix

I have updated `api/questions/import.php` to use the **correct column names** (`question_text`, `question_type`, etc.).

### 🚀 Action Required

You **MUST** upload the updated `import.php` file to your server for the import to work.

**File to Upload**:
```
Local:  e:\projects\playqzv4\api\questions\import.php
Remote: /public_html/api/questions/import.php
Action: OVERWRITE
```

---

## 🧪 Verification

1.  **Upload** the file.
2.  **Login** as admin.
3.  **Import** your JSON file.

The script now correctly maps your JSON data (which uses `question`) to the database column (`question_text`).

```php
// Inside the new import.php
$questionText = $q['question']; // Get from JSON
...
INSERT INTO questions (question_text, ...) VALUES ($questionText, ...) // Insert into DB
```
