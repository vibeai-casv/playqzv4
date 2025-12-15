# Bulk Edit Question Types - Troubleshooting Guide

## Current Implementation

The bulk edit page at `https://aiquiz.vibeai.cv/aiq3/admin/bulk-edit` **already fetches question types dynamically** from the database.

### How It Works

1. **Frontend** (`BulkEditQuestions.tsx`):
   - Calls `/api/questions/types.php` on page load
   - Populates dropdown with all question types returned

2. **Backend** (`api/questions/types.php`):
   ```php
   SELECT DISTINCT question_type 
   FROM questions 
   WHERE question_type IS NOT NULL 
   ORDER BY question_type
   ```

## Why You Might Not See All Types

### Issue 1: File Not Uploaded to Production
**Solution:** Upload the `types.php` file

```bash
# File location in AIQ3_FINAL package:
AIQ3_FINAL/api/questions/types.php

# Upload to:
/public_html/aiq3/api/questions/types.php
```

### Issue 2: API Endpoint Not Responding
**Debug Steps:**

1. **Test the API directly** - Visit:
   ```
   https://aiquiz.vibeai.cv/aiq3/api/questions/types.php
   ```

2. **Expected Response:**
   ```json
   {
     "types": [
       "image_identify_logo",
       "image_identify_person",
       "text_mcq",
       "true_false"
     ]
   }
   ```

3. **If you get an error:**
   - Check `config.php` database credentials
   - Check if `questions` table exists
   - Check if there are any questions in the database

### Issue 3: Database Has No Questions
**Solution:** Check your database

Run this SQL query in phpMyAdmin:
```sql
SELECT DISTINCT question_type, COUNT(*) as count
FROM questions 
WHERE question_type IS NOT NULL
GROUP BY question_type
ORDER BY question_type;
```

If this returns empty, you need to import questions first.

### Issue 4: Browser Cache
**Solution:** Clear browser cache or hard refresh

- Chrome/Edge: `Ctrl + Shift + R`
- Firefox: `Ctrl + F5`

## Verification Steps

### Step 1: Test API Endpoint
Open browser and go to:
```
https://aiquiz.vibeai.cv/aiq3/api/questions/types.php
```

Should return JSON with all question types.

### Step 2: Check Browser Console
1. Open the bulk-edit page
2. Press `F12` to open DevTools
3. Go to **Console** tab
4. Look for errors related to fetching question types

### Step 3: Check Network Tab
1. Open **Network** tab in DevTools
2. Reload the page
3. Look for request to `types.php`
4. Check if it returns 200 status
5. Check the response data

## Quick Fix: Force Upload types.php

If the file is missing on production, upload it:

**File to upload:**
```
Source: e:\projects\playqzv4\AIQ3_FINAL\api\questions\types.php
Destination: /public_html/aiq3/api/questions/types.php
```

## Current Question Types in System

Based on your database, these types should appear:
- `text_mcq` - Multiple Choice Questions
- `image_identify_logo` - Logo Identification
- `image_identify_person` - Personality Identification  
- `true_false` - True/False Questions
- `short_answer` - Short Answer Questions

## If Still Not Working

### Debug: Add Error Logging

Edit `api/questions/types.php` and add logging:

```php
<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

// Get unique question types from the database
try {
    error_log("Fetching question types...");
    
    $stmt = $pdo->query("
        SELECT DISTINCT question_type 
        FROM questions 
        WHERE question_type IS NOT NULL 
        ORDER BY question_type
    ");
    
    $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    error_log("Found types: " . json_encode($types));
    
    jsonResponse(['types' => $types]);
    
} catch (PDOException $e) {
    error_log("Error fetching types: " . $e->getMessage());
    jsonResponse(['error' => 'Failed to fetch question types: ' . $e->getMessage()], 500);
}
?>
```

Then check your PHP error logs for the output.

## Summary

✅ **The code is already implemented correctly**
✅ **It already fetches all question types from database**
✅ **The file is in your AIQ3_FINAL build**

**Action Required:** 
1. Make sure `types.php` is uploaded to production
2. Test the API endpoint directly
3. Check browser console for errors
4. Verify questions exist in database
