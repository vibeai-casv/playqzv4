# 📚 AI Question Generation - Documentation Index

Welcome! This is your complete guide to generating AI quiz questions using external LLMs.

---

## 🚀 QUICK START ⭐

**Want to start immediately?**

1. **Open:** `PROMPTS_PLAIN_TEXT.txt`
2. **Copy** your desired prompt
3. **Paste** into ChatGPT/Claude
4. **Import** the JSON to your app

That's it! Read below for more details.

---

## 📁 All Files Overview

### ⚡ For Immediate Use

| File | Purpose | When to Use |
|------|---------|-------------|
| **`PROMPTS_PLAIN_TEXT.txt`** ⭐ | Clean prompts ready to copy | **START HERE** - Easiest option |
| **`PROMPTS_READY_TO_USE.md`** | Prompts + minimal instructions | Need quick guidance |
| **`COPY_PASTE_PROMPTS_SUMMARY.md`** | Quick overview & workflow | Want a summary first |

### 📖 Complete Guides

| File | Purpose | When to Use |
|------|---------|-------------|
| **`COPY_PASTE_PROMPTS_FOR_LLM.md`** | Full tutorial with examples | First-time users |
| **`QUICK_REFERENCE_AI_PROMPTS.md`** | Settings & usage reference | Using in-app generation |
| **`AI_PROMPTS_FOR_QUESTION_GENERATION.md`** | Technical implementation | Developers/admins |
| **`AI_GENERATION_FIX_SUMMARY.md`** | What was fixed & improved | Understanding changes |

---

## 🎯 Choose Your Path

### Path 1: External LLM (ChatGPT, Claude, etc.) ⭐ RECOMMENDED FOR BULK

**Best for:** Generating many questions quickly without API limits

**Steps:**
1. Open `PROMPTS_PLAIN_TEXT.txt`
2. Copy one of the three prompts
3. Paste into your preferred LLM
4. Copy JSON response
5. Import via Admin → Import/Export → Import JSON

**Advantages:**
- ✅ No API quota limits
- ✅ Use free ChatGPT/Claude
- ✅ Generate large batches
- ✅ Review before importing

**Files to use:**
- `PROMPTS_PLAIN_TEXT.txt` (the prompts)
- `COPY_PASTE_PROMPTS_FOR_LLM.md` (full guide)

---

### Path 2: In-App Generation

**Best for:** Quick generation of small batches (5-10 questions)

**Steps:**
1. Go to Admin → Generate Questions
2. Select type, topic, count, difficulty
3. Click Generate
4. Questions saved automatically

**Advantages:**
- ✅ Integrated workflow
- ✅ Automatic saving
- ✅ No copy-paste

**Limitations:**
- ⚠️ Subject to API quotas
- ⚠️ Better for smaller batches

**Files to use:**
- `QUICK_REFERENCE_AI_PROMPTS.md` (settings guide)
- `AI_PROMPTS_FOR_QUESTION_GENERATION.md` (how it works)

---

## 📝 The Three Question Types

### 1️⃣ MCQ (Multiple Choice - Text Based)
- **File:** All prompt files have this
- **Use for:** AI concepts, algorithms, theory
- **Example:** "What is the advantage of Transformers over RNNs?"
- **After import:** Ready to use immediately

### 2️⃣ Identify Personality
- **File:** All prompt files have this
- **Use for:** AI researchers, founders, leaders
- **Example:** "Which researcher won 2018 Turing Award for CNNs?"
- **After import:** Upload photos in Question Management

### 3️⃣ Identify Logo
- **File:** All prompt files have this
- **Use for:** AI companies, frameworks, tools
- **Example:** "Which company developed AlphaGo and AlphaFold?"
- **After import:** Upload logos in Question Management

---

## 🎓 Learning Path

### Beginner
1. Read `COPY_PASTE_PROMPTS_SUMMARY.md`
2. Try generating 5 MCQ questions using `PROMPTS_PLAIN_TEXT.txt`
3. Import and review

### Intermediate
1. Read `COPY_PASTE_PROMPTS_FOR_LLM.md`
2. Generate all three question types
3. Upload images for Logo/Personality questions
4. Build a complete quiz

### Advanced
1. Read `AI_PROMPTS_FOR_QUESTION_GENERATION.md`
2. Understand the backend implementation
3. Use in-app generation for custom needs
4. Read `AI_GENERATION_FIX_SUMMARY.md` to understand improvements

---

## 📊 Recommended Workflow

### Building a Complete AI Quiz (50 questions)

**Step 1: Generate MCQ Base (30 questions)**
- Use Prompt 1 from `PROMPTS_PLAIN_TEXT.txt`
- Topics: "LLMs" (10), "Neural Networks" (10), "AI Ethics" (10)
- Mix difficulties: easy (10), medium (15), hard (5)

**Step 2: Generate Personalities (10 questions)**
- Use Prompt 2 from `PROMPTS_PLAIN_TEXT.txt`
- Difficulty: medium
- Upload photos from Wikipedia/official sites

**Step 3: Generate Logos (10 questions)**
- Use Prompt 3 from `PROMPTS_PLAIN_TEXT.txt`
- Difficulty: easy-medium mix
- Download official logos from company websites

**Step 4: Import & Review**
- Import all JSON files
- Review questions in Question Management
- Upload all images
- Activate best questions

**Step 5: Test**
- Take a demo quiz
- Verify all questions work
- Check image loading

---

## 🔍 Quick Reference

### What's in Each Prompt?

**MCQ Prompt includes:**
- AI-focused requirements
- 4 options requirement
- Explanation requirement
- Difficulty guidelines
- Example format

**Personality Prompt includes:**
- Notable figures focus
- Era coverage (pioneers to modern)
- Achievement descriptions
- Diversity requirements
- Category coverage

**Logo Prompt includes:**
- Company/tool categories
- Established + emerging players
- Product descriptions
- Distribution requirements (30/30/30/10)
- Diversity requirements

---

## 💡 Pro Tips

### For Best Quality:
1. **Be specific** - "Transformer Architecture" > "AI"
2. **Start small** - Test with 5 questions first
3. **Review JSON** - Check quality before importing
4. **Mix difficulties** - Keep quizzes engaging
5. **Prepare images** - Have photos/logos ready

### For Bulk Generation:
1. **Use external LLM** - No API limits
2. **Generate in batches** - 10-15 at a time
3. **Save JSON files** - Keep backups
4. **Import together** - Save time

### For Image Questions:
1. **Use high quality** - Professional photos/logos
2. **Consistent sizing** - 512px recommended
3. **Official sources** - Company websites, Wikipedia
4. **Test display** - Check how they look in quiz

---

## 🆘 Troubleshooting

### Common Issues & Solutions

**Issue:** Can't find the prompts
- **Solution:** Open `PROMPTS_PLAIN_TEXT.txt` - easiest to use

**Issue:** LLM outputs markdown code blocks
- **Solution:** Remove ` ```json ` markers, keep only `[...]` content

**Issue:** JSON import fails
- **Solution:** Validate at jsonlint.com, ensure proper format

**Issue:** Questions are too generic
- **Solution:** Use more specific topics, increase difficulty

**Issue:** Need help with images
- **Solution:** See `QUICK_REFERENCE_AI_PROMPTS.md` section on images

---

## 📞 Support & Documentation

### Need Help?
- **Quick start:** `COPY_PASTE_PROMPTS_SUMMARY.md`
- **Full tutorial:** `COPY_PASTE_PROMPTS_FOR_LLM.md`
- **Technical info:** `AI_PROMPTS_FOR_QUESTION_GENERATION.md`
- **What changed:** `AI_GENERATION_FIX_SUMMARY.md`

### Want to Understand More?
- **How prompts work:** `AI_PROMPTS_FOR_QUESTION_GENERATION.md`
- **Backend changes:** See `api/admin/generate_questions.php`
- **Settings reference:** `QUICK_REFERENCE_AI_PROMPTS.md`

---

## ✅ Checklist for First Time Use

- [ ] Read `COPY_PASTE_PROMPTS_SUMMARY.md`
- [ ] Open `PROMPTS_PLAIN_TEXT.txt`
- [ ] Copy MCQ prompt
- [ ] Generate 5 test questions in ChatGPT
- [ ] Copy JSON response
- [ ] Go to Admin → Import/Export
- [ ] Import JSON
- [ ] Review questions in Question Management
- [ ] Try generating Personality questions
- [ ] Upload sample photos
- [ ] Take a test quiz
- [ ] Scale up to full quiz!

---

## 🎯 Next Steps

**You're ready to start!**

1. **Open:** `PROMPTS_PLAIN_TEXT.txt`
2. **Choose:** MCQ, Personality, or Logo prompt
3. **Customize:** Change count/topic/difficulty
4. **Generate:** Paste in ChatGPT/Claude
5. **Import:** Use Import JSON in admin panel
6. **Done!** ✅

---

**Happy Question Generating! 🚀**

**Last Updated:** December 14, 2024  
**Version:** 2.0  
**Status:** Production Ready
