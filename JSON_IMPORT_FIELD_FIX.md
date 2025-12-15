# ✅ JSON Import Issue - FIXED

## 🔍 Problem

When importing `qbank/questions/il/dsset1cp-25.json`, you received this error:
```
0 question(s) skipped: Question 1: Missing question_text 
Question 2: Missing question_text 
Question 3: Missing question_text 
...and 20 more
```

## 🎯 Root Cause

The JSON file was using `"text"` as the field name:
```json
{
  "text": "This AI safety and research company...",
  "options": [...],
  ...
}
```

But your import system expects `"question_text"`:
```php
// From api/questions/import.php line 65
$requiredFields = ['question_text', 'question_type', 'category', 'difficulty', 'correct_answer'];
```

## ✅ Solution

I've created a fixed version: `qbank/questions/il/dsset1cp-25_fixed.json`

**Changes:**
- Changed `"text"` → `"question_text"` in all 25 questions
- All other fields remain the same
- Proper formatting maintained

## 📝 How to Import

**Option 1: Use the Fixed File**
```
1. Go to Admin → Import/Export → Import JSON
2. Copy contents of: qbank/questions/il/dsset1cp-25_fixed.json
3. Paste and click Import
4. All 25 questions should import successfully
```

**Option 2: Fix Other Files (If Needed)**

If you have other JSON files with the same issue, use this Python script:

```python
import json

# Read file
with open('path/to/file.json', 'r') as f:
    data = json.load(f)

# Fix field name
for question in data:
    question['question_text'] = question.pop('text')

# Save fixed file
with open('path/to/file_fixed.json', 'w') as f:
    json.dump(data, f, indent=2)
```

## 📊 File Details

**Original File:**
- Path: `qbank/questions/il/dsset1cp-25.json`
- Questions: 25
- Type: `image_identify_logo`
- Field name: `"text"` ❌

**Fixed File:**
- Path: `qbank/questions/il/dsset1cp-25_fixed.json`
- Questions: 25
- Type: `image_identify_logo`
- Field name: `"question_text"` ✅

## 🎯 Expected Result After Import

After importing `dsset1cp-25_fixed.json`:
- ✅ 25 AI logo identification questions imported
- ✅ All questions saved as drafts (inactive)
- ✅ Category: "AI Companies & Tools"
- ✅ Difficulty: medium
- ✅ Type: image_identify_logo

## 📋 Next Steps

**After Successful Import:**

1. **Go to Question Management**
2. **Find the imported questions** (filter by type: image_identify_logo)
3. **Upload logos for each company:**
   - Anthropic
   - PyTorch
   - Stability AI
   - Replicate
   - Scikit-learn
   - Weights & Biases
   - Cerebras
   - JAX
   - Pinecone
   - Cohere
   - LangChain
   - Perplexity AI
   - AWS SageMaker
   - Meta
   - Hugging Face
   - Graphcore
   - LlamaIndex
   - Vertex AI
   - Runway
   - Weaviate
   - Keras
   - OpenAI
   - Azure Cognitive Services
   - NVIDIA
   - EleutherAI

4. **Get logos from:**
   - Official company websites
   - Wikipedia
   - Brand resource pages
   - Use square format (512x512px recommended)

5. **Activate questions** once logos are uploaded

## 🔧 For Future Imports

**Required JSON Format:**
```json
[
  {
    "question_text": "Your question here",     ← Must be "question_text"
    "options": ["A", "B", "C", "D"],
    "correct_answer": "A",
    "explanation": "Why this is correct",
    "category": "Category Name",
    "type": "text_mcq | image_identify_person | image_identify_logo",
    "difficulty": "easy | medium | hard"
  }
]
```

**Required Fields:**
- ✅ `question_text` (not "text")
- ✅ `question_type` (or "type")
- ✅ `category`
- ✅ `difficulty`
- ✅ `correct_answer`
- ✅ `options` (array of 4 strings)

**Optional Fields:**
- `explanation`
- `image_url`
- `tags`

## 💡 Tips

**Generating Questions:**
- Use prompts from `PROMPTS_PLAIN_TEXT.txt`
- LLM output uses `"text"` by default
- Either:
  - Fix the JSON after generation, OR
  - Modify the prompt to specify `"question_text"`

**Updated Prompt (for ChatGPT/Claude):**
```
Change the output format to use "question_text" instead of "text":
[
  {
    "question_text": "...",    ← Specify this!
    "options": [...],
    ...
  }
]
```

---

**Fixed File Ready:** `qbank/questions/il/dsset1cp-25_fixed.json`  
**Status:** ✅ Ready to import  
**Questions:** 25 AI logo identification questions
