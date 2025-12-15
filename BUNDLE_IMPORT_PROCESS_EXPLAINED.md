# Question Bundle Import Process - Detailed Explanation

## Overview

When you import a question bundle (ZIP file) in the admin panel, a sophisticated multi-step process extracts questions, images, and metadata into your database and file system.

---

## Step-by-Step Process

### 📤 **Step 1: Upload & Authentication**

```
User uploads ZIP file → API receives at /api/bundle/import.php
```

**Security Checks:**
- ✅ User must be logged in
- ✅ User role must be `admin` or `super_admin`
- ✅ File must be a valid ZIP archive
- ✅ Upload must be successful (no PHP upload errors)

**What happens:**
- File is temporarily stored (PHP's `tmp_name`)
- All activity is logged to `import_errors.log` for debugging

---

### 📦 **Step 2: ZIP Extraction & Validation**

```php
$zip = new ZipArchive();
$zip->open($file);
```

**Bundle Structure Expected:**
```
bundle.zip
├── manifest.json          # Metadata about questions & media
├── media/
│   └── uploads/
│       ├── logos/
│       │   ├── image1.jpg
│       │   └── image2.png
│       └── personalities/
│           └── person1.jpg
```

**Validation:**
- Checks if `manifest.json` exists
- Verifies JSON is valid
- Counts files in archive

---

### 📋 **Step 3: Read Manifest**

```json
{
  "questions": [
    {
      "id": 123,
      "question_text": "Identify this logo",
      "question_type": "image_identify_logo",
      "options": ["Option A", "Option B", "Option C", "Option D"],
      "correct_answer": "Option A",
      "image_url": "uploads/logos/image1.jpg",
      "media_id": 456,
      "difficulty": "medium",
      "points": 10,
      ...
    }
  ],
  "media_metadata": [
    {
      "id": 456,
      "filename": "image1.jpg",
      "original_filename": "company_logo.jpg",
      "url": "uploads/logos/image1.jpg",
      "type": "logo",
      "mime_type": "image/jpeg",
      "size_bytes": 45620
    }
  ]
}
```

**What's extracted:**
- Array of questions with all their properties
- Array of media files with metadata
- Export metadata (timestamp, version, etc.)

---

### 🖼️ **Step 4: Extract Media Files**

```
Loops through all files in ZIP archive
```

**For each file starting with `media/`:**
1. **Strip prefix:** `media/uploads/logos/image1.jpg` → `uploads/logos/image1.jpg`
2. **Determine target:** Resolves to `/var/www/html/aiq3/uploads/logos/image1.jpg`
3. **Create directories:** Creates `uploads/logos/` if it doesn't exist
4. **Copy file:** Extracts from ZIP to target location
5. **Set permissions:** Directory gets `0755` permissions

**Example:**
```php
// From ZIP
media/uploads/logos/google.png

// Extracted to
/public_html/aiq3/uploads/logos/google.png

// Accessible via
https://aiquiz.vibeai.cv/aiq3/uploads/logos/google.png
```

**Counter:** Tracks `media_imported` count

---

### 💾 **Step 5: Import Media Records to Database**

```sql
SET FOREIGN_KEY_CHECKS=0;  -- Temporarily disable FK checks

INSERT INTO media_library (
    id, filename, original_filename, url, type, mime_type,
    size_bytes, description, metadata, uploaded_by, is_active, 
    created_at, updated_at
) VALUES (...)
ON DUPLICATE KEY UPDATE updated_at = NOW();

SET FOREIGN_KEY_CHECKS=1;  -- Re-enable FK checks
```

**What happens:**
- Each media file metadata from manifest is inserted into `media_library` table
- If media record already exists (same ID), it only updates `updated_at` timestamp
- `uploaded_by` is set to the current admin user's profile ID
- All records are marked as `is_active = 1`

**Error Handling:**
- If media record fails, error is logged but process continues
- Other media files and questions still get imported

---

### 📝 **Step 6: Import Questions to Database**

```sql
SET FOREIGN_KEY_CHECKS=0;  -- Temporarily disable FK checks

-- For each question:
-- 1. Check if question ID already exists
SELECT id FROM questions WHERE id = ?;

-- 2. If NOT exists, insert new question
INSERT INTO questions (
    id, question_text, question_type, options, correct_answer,
    image_url, media_id, explanation, hint, difficulty,
    category, subcategory, tags, points, time_limit_seconds,
    is_active, is_verified, ai_generated, is_demo, created_by,
    created_at, updated_at
) VALUES (?, ?, ?, ...);

SET FOREIGN_KEY_CHECKS=1;  -- Re-enable FK checks
```

**Question Processing:**

#### Duplicate Detection:
```php
// Check by ID
if (question with this ID exists) {
    Skip → questions_skipped++
} else {
    Import → questions_imported++
}
```

#### Field Transformations:

| Field | Transformation |
|-------|----------------|
| `options` | Array → JSON string: `["A","B","C"]` → `'["A","B","C"]'` |
| `tags` | Array → JSON string |
| `image_url` | Stored as-is (relative path) |
| `media_id` | Links to `media_library.id` |
| `created_by` | Set to current admin's `profile_id` |
| `created_at` | Set to `NOW()` |
| `is_active` | Default `1` if not in manifest |
| `is_verified` | Default `1` if not in manifest |
| `ai_generated` | Default `0` if not in manifest |
| `is_demo` | Default `0` if not in manifest |

#### Error Handling:
- If a question fails to import, error is added to `errors[]` array
- Other questions continue to be imported
- Process doesn't fail completely

---

### 📊 **Step 7: Return Statistics**

```json
{
  "success": true,
  "stats": {
    "questions_imported": 15,
    "questions_skipped": 2,
    "media_imported": 15,
    "errors": [
      "Question ID 123: Duplicate entry",
      "Media 456: File already exists"
    ]
  }
}
```

**Response includes:**
- ✅ Total questions imported
- ⏭️ Questions skipped (duplicates)
- 🖼️ Media files extracted
- ❌ Any errors encountered

---

## Foreign Key Handling

### The `created_by` Challenge

**Problem:** Questions reference `profiles.id` via `created_by` foreign key, but imported questions might reference non-existent profiles.

**Solution:**
```php
// Temporarily disable foreign key checks
SET FOREIGN_KEY_CHECKS=0;

// Import all data
INSERT INTO questions ...

// Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;
```

**Fallback Logic:**
```php
// Use current admin's profile ID
$createdBy = $session['profile_id'] ?? $session['user_id'];
```

This ensures all imported questions are attributed to the importing admin.

---

## File System Layout After Import

```
/public_html/aiq3/
├── uploads/
│   ├── logos/           # Logo images
│   │   ├── google.png
│   │   ├── meta.jpg
│   │   └── microsoft.png
│   ├── personalities/   # Personality photos
│   │   ├── hinton.jpg
│   │   ├── bengio.jpg
│   │   └── lecun.jpg
│   └── questions/       # Other question media
└── import_errors.log    # Import debug log
```

---

## Database Changes After Import

### `media_library` Table:
```sql
id | filename      | url                        | type | uploaded_by
---|---------------|----------------------------|------|------------
456| google.png    | uploads/logos/google.png   | logo | 1 (admin)
457| hinton.jpg    | uploads/personalities/...  | person| 1 (admin)
```

### `questions` Table:
```sql
id  | question_text        | question_type         | image_url              | media_id
----|----------------------|-----------------------|------------------------|----------
123 | Identify this logo   | image_identify_logo   | uploads/logos/google...|  456
124 | Who is this?         | image_identify_person | uploads/personalities/..|  457
```

---

## Logging & Debugging

All import activity is logged to: `import_errors.log`

**Log entries include:**
```
[BUNDLE IMPORT] Import request started
[BUNDLE IMPORT] User authenticated: admin@example.com
[BUNDLE IMPORT] File received: questions_bundle.zip (245680 bytes)
[BUNDLE IMPORT] ZIP opened successfully. Files in archive: 32
[BUNDLE IMPORT] Manifest loaded. Questions: 15
[BUNDLE IMPORT] Media metadata entries: 15
[BUNDLE IMPORT] Creating directory: /var/www/html/aiq3/uploads/logos
[BUNDLE IMPORT] Extracted: uploads/logos/google.png
[BUNDLE IMPORT] Media imported: 456
[BUNDLE IMPORT] Question imported: 123
[BUNDLE IMPORT] Question skipped (duplicate): 124
[BUNDLE IMPORT] Import completed successfully
[BUNDLE IMPORT] Stats: {"questions_imported":14,"questions_skipped":1,...}
```

**To view logs:**
```bash
# On server
tail -f /public_html/aiq3/import_errors.log
```

---

## Error Scenarios & Handling

### ❌ **Invalid ZIP File**
```json
{
  "error": "Failed to open ZIP bundle"
}
```

### ❌ **Missing Manifest**
```json
{
  "error": "Invalid bundle: missing manifest.json"
}
```

### ❌ **Corrupted Manifest**
```json
{
  "error": "Invalid manifest JSON"
}
```

### ⚠️ **Partial Import Success**
```json
{
  "success": true,
  "stats": {
    "questions_imported": 10,
    "questions_skipped": 3,
    "media_imported": 12,
    "errors": [
      "Question ID 125: SQL constraint violation",
      "Media 460: Permission denied"
    ]
  }
}
```

Even with errors, successfully imported items remain in the database.

---

## Summary Flow Chart

```
Upload ZIP
    ↓
Authenticate User (Admin/Super Admin)
    ↓
Open ZIP Archive
    ↓
Read manifest.json
    ↓
Extract Media Files (media/* → uploads/*)
    ↓
Import Media Metadata → media_library table
    ↓
Import Questions → questions table
    ↓
Check for duplicates (skip if exists)
    ↓
Transform fields (JSON encode arrays)
    ↓
Set created_by to current admin
    ↓
Return Statistics (imported/skipped/errors)
    ↓
Log everything to import_errors.log
```

---

## Key Takeaways

1. ✅ **Non-destructive:** Existing questions are never overwritten
2. ✅ **Atomic media:** Each media file is extracted independently
3. ✅ **Detailed logging:** Everything is logged for debugging
4. ✅ **Graceful errors:** Single failures don't break entire import
5. ✅ **Foreign key safe:** Temporarily disables FK checks
6. ✅ **User attribution:** All imports attributed to importing admin
7. ✅ **Duplicate protection:** Questions checked by ID before insert

---

## Related Files

- **Import API:** `api/bundle/import.php`
- **Export API:** `api/bundle/export.php`
- **Frontend Component:** `client/src/pages/admin/ImportExport.tsx`
- **Logs:** `import_errors.log` (at project root)
