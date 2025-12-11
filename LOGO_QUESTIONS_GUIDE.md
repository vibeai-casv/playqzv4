# Logo Identification Questions - Complete Guide

## Overview
This guide explains how to create and manage logo identification questions in your quiz application. Logo questions display an actual logo image and ask users to identify the brand/company.

---

## 📁 File Structure

### Backend (PHP API)
- **`api/media/upload.php`** - Handles logo image uploads
- **`api/media/list.php`** - Lists media files from library
- **`public/uploads/logo/`** - Directory where logo images are stored

### Frontend (React)
- **`client/src/components/LogoUploader.tsx`** - Component for uploading and selecting logos
- **`client/src/components/admin/MediaPicker.tsx`** - Alternative media picker component
- **`client/src/hooks/useAdmin.ts`** - Contains `uploadMedia()` and `fetchMedia()` functions

### Database
- **`media_library`** table - Stores metadata about uploaded images
- **`questions`** table - Stores questions with `media_id` reference

---

## 🎯 How Logo Questions Work

### Database Schema
```sql
-- Questions table has these relevant fields:
question_type = 'image_identify_logo'  -- Identifies this as a logo question
media_id = 'uuid-of-logo-image'        -- Links to media_library table
image_url = '/uploads/logo/filename.png' -- Direct URL to image
options = JSON array of 4 brand names
correct_answer = 'Brand Name'
```

### Question Example
```json
{
  "question_text": "Identify the brand from this logo:",
  "question_type": "image_identify_logo",
  "media_id": "abc-123-def-456",
  "image_url": "/uploads/logo/nike_logo.png",
  "options": ["Adidas", "Nike", "Puma", "Reebok"],
  "correct_answer": "Nike",
  "explanation": "The Nike Swoosh is one of the most recognizable logos worldwide.",
  "difficulty": "easy",
  "category": "Sports Brands"
}
```

---

## 📤 Uploading Logo Images

### Method 1: Using the Admin Panel

1. Navigate to **Admin → Questions → Create Question**
2. Select question type: **"Image Identify Logo"**
3. Use the **LogoUploader** component:
   - **Drag and drop** a logo image, OR
   - **Click to browse** and select a file
   - **Browse Media Library** to reuse existing logos

### Method 2: Using API Directly

```javascript
// Upload a logo via API
const formData = new FormData();
formData.append('file', logoFile);
formData.append('type', 'logo');
formData.append('description', 'Nike Swoosh Logo');

const response = await fetch('/api/media/upload.php', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`
  },
  body: formData
});

const result = await response.json();
// result.media.id - Use this as media_id in question
// result.media.url - Use this as image_url in question
```

### Supported Image Formats
- **JPG/JPEG** - Best for photographs
- **PNG** - Best for logos with transparency
- **GIF** - Animated logos
- **WebP** - Modern format, smaller file sizes
- **SVG** - Vector graphics, scalable

### File Requirements
- **Maximum size**: 5MB
- **Recommended size**: 500x500px to 1000x1000px
- **Aspect ratio**: Square (1:1) works best
- **Background**: Transparent PNG recommended for logos

---

## 🔨 Creating Logo Questions

### Step-by-Step Process

1. **Upload the Logo**
   ```
   - Use LogoUploader component
   - Save the returned media_id and url
   ```

2. **Create the Question**
   ```javascript
   const question = {
     question_text: "Identify the brand from this logo:",
     question_type: "image_identify_logo",
     media_id: "saved-media-id",
     image_url: "/uploads/logo/filename.png",
     options: ["Option A", "Option B", "Option C", "Option D"],
     correct_answer: "Option B",
     explanation: "Brief explanation about the logo",
     difficulty: "easy", // or "medium", "hard"
     category: "Technology", // or any category
     points: 10,
     time_limit_seconds: 30
   };
   ```

3. **Submit via API**
   ```javascript
   await createQuestion(question);
   ```

---

## 🤖 Generating Logo Questions with AI

### Prompt for LLMs (ChatGPT, Claude, Gemini, etc.)

```markdown
Generate 20 logo identification quiz questions in JSON format.

For each question, provide:
{
  "brand_name": "Nike",
  "logo_description": "A curved swoosh symbol in black, suggesting motion and speed",
  "question_text": "Identify the brand from this logo:",
  "options": ["Adidas", "Nike", "Puma", "Reebok"],
  "correct_answer": "Nike",
  "explanation": "The Nike Swoosh, designed in 1971, represents the wing of Nike, the Greek goddess of victory.",
  "difficulty": "easy",
  "category": "Sports Brands",
  "search_query": "Nike logo official PNG"
}

Requirements:
- Mix of difficulties: 40% easy, 40% medium, 20% hard
- Categories: Technology, Automotive, Food & Beverage, Fashion, Sports, Entertainment, Finance, Retail
- Options must be from the same category (all sports brands, all tech companies, etc.)
- Include globally recognized brands
- Provide search_query to help find the logo image

Difficulty Guidelines:
- Easy: Very famous logos (Apple, McDonald's, Nike, Coca-Cola)
- Medium: Well-known but less iconic (Spotify, Airbnb, Slack)
- Hard: Industry-specific, rebranded, or regional brands
```

### Processing AI-Generated Questions

1. **Get the JSON output** from the LLM
2. **Download logo images** using the `search_query` field
3. **Upload each logo** via the media upload API
4. **Create questions** with the media_id from uploads
5. **Bulk import** using the questions API

---

## 📥 Bulk Import Process

### Step 1: Prepare Logo Images
```bash
# Create a folder with all logo images
logos/
  ├── nike.png
  ├── apple.png
  ├── mcdonalds.png
  └── ...
```

### Step 2: Create Import Script
```javascript
// import-logos.js
const logos = [
  { file: 'nike.png', brand: 'Nike', category: 'Sports' },
  { file: 'apple.png', brand: 'Apple', category: 'Technology' },
  // ... more logos
];

for (const logo of logos) {
  // 1. Upload logo
  const media = await uploadLogo(logo.file);
  
  // 2. Create question
  await createQuestion({
    question_text: "Identify the brand from this logo:",
    question_type: "image_identify_logo",
    media_id: media.id,
    image_url: media.url,
    // ... rest of question data
  });
}
```

---

## 🎨 Best Practices

### Logo Image Quality
✅ **DO:**
- Use high-resolution images (at least 500x500px)
- Use transparent backgrounds for PNG files
- Crop logos to remove excess whitespace
- Use official brand logos from brand websites
- Maintain aspect ratio when resizing

❌ **DON'T:**
- Use low-quality or pixelated images
- Include watermarks or copyright text
- Use screenshots of logos
- Distort or modify brand logos
- Use unofficial or fan-made versions

### Question Design
✅ **DO:**
- Keep question text simple: "Identify the brand from this logo:"
- Use 4 options from the same industry/category
- Make distractors plausible (similar brands)
- Provide educational explanations
- Tag with relevant categories

❌ **DON'T:**
- Mix unrelated brands (e.g., Nike with Microsoft)
- Use obvious wrong answers
- Make questions too easy or too hard
- Forget to add explanations
- Use outdated logos

### Difficulty Calibration

**Easy (30-40% of questions)**
- Globally recognized brands
- Simple, iconic logos
- Household names
- Examples: Apple, Nike, McDonald's, Coca-Cola

**Medium (40-50% of questions)**
- Well-known but less iconic
- Industry leaders
- Popular consumer brands
- Examples: Spotify, Airbnb, Slack, Dropbox

**Hard (10-20% of questions)**
- Industry-specific brands
- Regional brands
- Rebranded companies
- Subtle logo variations
- Examples: Enterprise software, B2B companies

---

## 🔍 Finding Logo Images

### Recommended Sources

1. **Official Brand Websites**
   - Press kits / Media resources
   - Usually have high-quality logos
   - Example: `apple.com/newsroom/media-assets`

2. **Wikimedia Commons**
   - Free, public domain logos
   - URL: `commons.wikimedia.org`

3. **Brands of the World**
   - Large logo database
   - URL: `brandsoftheworld.com`

4. **Seeklogo**
   - Vector logos in various formats
   - URL: `seeklogo.com`

5. **Google Images**
   - Search: `[brand name] logo PNG transparent`
   - Filter: Tools → Size → Large
   - Filter: Tools → Type → PNG

### Copyright Considerations
- Use logos for **educational purposes** (fair use)
- Don't modify or distort brand logos
- Credit brands in explanations
- Don't use for commercial purposes without permission

---

## 🧪 Testing Logo Questions

### Checklist Before Publishing

- [ ] Logo image loads correctly
- [ ] Image is clear and recognizable
- [ ] All 4 options are from the same category
- [ ] Correct answer is accurate
- [ ] Explanation is informative
- [ ] Difficulty level is appropriate
- [ ] Category is correctly tagged
- [ ] No spelling errors in brand names
- [ ] Time limit is reasonable (20-60 seconds)

### Quality Assurance
```javascript
// Test a logo question
const testQuestion = {
  id: "question-id",
  question_type: "image_identify_logo",
  media_id: "media-id",
  image_url: "/uploads/logo/test.png"
};

// Verify:
// 1. Image URL is accessible
fetch(testQuestion.image_url).then(r => console.log('Image OK:', r.ok));

// 2. Media ID exists in database
const media = await fetchMedia({ id: testQuestion.media_id });
console.log('Media exists:', !!media);
```

---

## 📊 Analytics for Logo Questions

### Track Performance
- **Usage count**: How many times the question was used
- **Correct rate**: Percentage of correct answers
- **Average time**: Time users take to answer
- **Difficulty accuracy**: Does actual performance match difficulty rating?

### Optimize Questions
- If correct rate > 90%: Consider making it "easy" or adding harder distractors
- If correct rate < 30%: Consider making it "hard" or improving logo clarity
- If average time > 60s: Logo might be unclear or options too similar

---

## 🚀 Quick Start Example

```javascript
// Complete example: Upload logo and create question

// 1. Upload logo
const logoFile = document.getElementById('logo-input').files[0];
const { uploadMedia } = useAdmin();
const mediaResponse = await uploadMedia(logoFile, 'logo');

// 2. Create question
const { createQuestion } = useAdmin();
await createQuestion({
  question_text: "Identify the brand from this logo:",
  question_type: "image_identify_logo",
  media_id: mediaResponse.media.id,
  image_url: mediaResponse.media.url,
  options: ["Adidas", "Nike", "Puma", "Reebok"],
  correct_answer: "Nike",
  explanation: "The Nike Swoosh is one of the most iconic logos in sports.",
  difficulty: "easy",
  category: "Sports Brands",
  points: 10,
  time_limit_seconds: 30
});

console.log('Logo question created successfully!');
```

---

## 🛠️ Troubleshooting

### Image Not Displaying
- **Check file path**: Ensure `/uploads/logo/` directory exists
- **Check permissions**: Directory should be writable (755)
- **Check URL**: Verify image URL is accessible
- **Check file size**: Must be under 5MB

### Upload Fails
- **File type**: Only JPG, PNG, GIF, WebP, SVG allowed
- **File size**: Maximum 5MB
- **Authentication**: Must be logged in as admin
- **Server space**: Check available disk space

### Question Not Saving
- **Required fields**: Ensure all required fields are filled
- **Media ID**: Must be a valid UUID from media_library
- **Options**: Must be a valid JSON array with 4 items
- **Correct answer**: Must match one of the options exactly

---

## 📚 Additional Resources

- **Database Schema**: See `api/schema.sql`
- **API Documentation**: See `api/README.md`
- **Component Usage**: See `client/src/components/LogoUploader.tsx`
- **Admin Hook**: See `client/src/hooks/useAdmin.ts`

---

## 💡 Tips for Success

1. **Start small**: Begin with 10-20 well-known logos
2. **Test thoroughly**: Verify each question before publishing
3. **Gather feedback**: See which questions users struggle with
4. **Update regularly**: Add new brands and remove outdated ones
5. **Balance difficulty**: Mix easy, medium, and hard questions
6. **Categorize well**: Proper categories help with quiz generation
7. **Write good explanations**: Users learn from explanations

---

**Happy Quiz Creating! 🎉**
