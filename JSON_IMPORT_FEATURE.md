# 📥 JSON Question Import Feature

## ✅ Feature Added

I've added a JSON import feature to the Admin Dashboard that allows you to bulk import questions from JSON files.

---

## 🎯 Features

✅ **Bulk Import** - Import multiple questions at once from JSON files  
✅ **Duplicate Detection** - Automatically skips questions that already exist (based on question text)  
✅ **Error Handling** - Shows detailed results with imported, skipped, and error counts  
✅ **Format Validation** - Validates JSON structure before import  
✅ **Detailed Results** - Shows exactly what was imported, skipped, and any errors  

---

## 📁 Files Created/Modified

### Backend:
- **`api/questions/import.php`** - API endpoint for importing questions

### Frontend:
- **`client/src/components/admin/JSONImporter.tsx`** - Import component
- **`client/src/pages/admin/Questions.tsx`** - Added Import button and modal

---

## 🚀 How to Use

### 1. Access the Feature

1. Login as admin
2. Go to **Admin Dashboard** → **Question Bank**
3. Click the **"Import JSON"** button (green button)

### 2. Prepare Your JSON File

Your JSON file should be an array of question objects:

```json
[
  {
    "type": "text_mcq",
    "category": "Fundamentals",
    "difficulty": "easy",
    "question": "What is AI?",
    "options": ["Option 1", "Option 2", "Option 3", "Option 4"],
    "correct_answer": "Option 1",
    "image_url": "optional",
    "explanation": "optional"
  },
  {
    "type": "text_mcq",
    "category": "LLMs",
    "difficulty": "medium",
    "question": "What does GPT stand for?",
    "options": ["Generative Pre-trained Transformer", "General Purpose Tool", "Global Processing Technology", "Generic Pattern Training"],
    "correct_answer": "Generative Pre-trained Transformer"
  }
]
```

### 3. Import Questions

1. Click **"Choose a JSON file"**
2. Select your JSON file
3. Check/uncheck **"Skip duplicate questions"** (recommended: keep checked)
4. Click **"Import Questions"**
5. Wait for the import to complete
6. Review the results

---

## 📋 Required Fields

Each question object must have:

- **`question`** - The question text (string)
- **`options`** - Array of answer options (array of strings)
- **`correct_answer`** - The correct answer (string, must match one of the options)

## 🔧 Optional Fields

- **`type`** - Question type (default: `"text_mcq"`)
  - Options: `text_mcq`, `image_identify_logo`, `image_identify_person`, `true_false`
- **`category`** - Question category (default: `"General"`)
- **`difficulty`** - Difficulty level (default: `"medium"`)
  - Options: `easy`, `medium`, `hard`
- **`image_url`** - URL to an image (for image-based questions)
- **`explanation`** - Explanation of the correct answer

---

## 🎯 Import Results

After import, you'll see:

- ✅ **Imported** - Number of questions successfully added
- ⚠️ **Skipped** - Number of duplicate questions skipped
- ❌ **Errors** - Number of questions that failed to import (with error details)
- 📊 **Total Processed** - Total number of questions in the file

---

## 📂 Sample Files

You already have sample question files in the `qbank` directory:

- `qbank/question2.json` - 50 AI/ML questions
- `qbank/question3.json` - More questions

You can use these files to test the import feature!

---

## 🔍 Duplicate Detection

The system checks for duplicates based on the **question text**. If a question with the same text already exists in the database, it will be skipped (if "Skip duplicates" is enabled).

This prevents:
- Accidentally importing the same questions twice
- Creating duplicate entries in the database

---

## 🛠️ Testing the Feature

### Test with Sample Data:

1. **Build the frontend:**
   ```powershell
   cd client
   npm run build
   ```

2. **Upload the new files to production:**
   - Upload `api/questions/import.php`
   - Upload `client/dist/*` (rebuilt frontend)

3. **Test the import:**
   - Login to admin dashboard
   - Click "Import JSON"
   - Upload `qbank/question2.json`
   - Click "Import Questions"
   - Should import 50 questions!

---

## 📊 Expected Results

When importing `question2.json`:

```
✓ Imported: 50 questions
⚠ Skipped: 0 duplicates
❌ Errors: 0
📊 Total processed: 50
```

If you import the same file again:

```
✓ Imported: 0 questions
⚠ Skipped: 50 duplicates
❌ Errors: 0
📊 Total processed: 50
```

---

## 🔒 Security

- ✅ **Admin only** - Only admin users can import questions
- ✅ **Authentication required** - Must be logged in
- ✅ **Validation** - JSON structure is validated
- ✅ **SQL injection protection** - Uses prepared statements
- ✅ **Error handling** - Graceful error messages

---

## 🐛 Troubleshooting

### "Invalid input. Expected 'questions' array"
- Make sure your JSON file contains an array of objects
- Check that the file is valid JSON

### "Failed to import questions"
- Check that you're logged in as admin
- Verify the API endpoint is accessible
- Check browser console for errors

### Questions not appearing after import
- Refresh the page
- Check that questions have `status: 'active'` (default)
- Verify in the database

---

## 🎉 Ready to Use!

The feature is now complete and ready to use. Just build the frontend and upload the files to production!

**Next steps:**
1. Build frontend: `npm run build`
2. Upload `api/questions/import.php`
3. Upload `client/dist/*`
4. Test with your sample JSON files!
