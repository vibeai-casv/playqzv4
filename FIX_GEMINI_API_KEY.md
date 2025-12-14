# 🔑 Fix Gemini API Key Error

## Current Error

```
Gemini API Error: API key expired. Please renew the API key.
```

---

## 🎯 Solution: Get a New Free API Key

### Step 1: Visit Google AI Studio

Go to: **https://aistudio.google.com/app/apikey**

### Step 2: Sign in with Google Account

Use any Google account (Gmail)

### Step 3: Create API Key

1. Click **"Create API Key"** or **"Get API key"**
2. Select **"Create API key in new project"** (or use existing project)
3. **Copy the generated key** (it looks like: `AIzaSy...`)

⚠️ **IMPORTANT:** Save it immediately - you might not see it again!

---

## 🔧 Update Configuration

### For Local Development:

Edit: `E:\projects\playqzv4\api\config.php`

**Line 31:**
```php
define('AI_API_KEY', 'YOUR_NEW_API_KEY_HERE');
```

Replace `'AIzaSyBAEQMkw01uCSQ1XFin3H4_D6DJ7HeHM7I'` with your new key.

---

### For Production (AIQ3):

Edit on server: `/aiq3/api/config.php`

**Update:**
```php
// AI Configuration
define('AI_PROVIDER', 'gemini');
define('AI_API_KEY', 'YOUR_NEW_API_KEY_HERE');
define('AI_MODEL', 'gemini-1.5-flash');
```

---

## ✅ Test the New Key

### 1. Save config.php

### 2. Test in your local app:
- Go to: http://localhost:5173/aiq3/admin/questions
- Click: **"Generate Questions"**
- Enter: Topic, count, difficulty
- Click: **"Generate"**

### 3. Should see:
```
✓ Successfully generated and saved X questions as drafts
```

---

## 🆓 About Google AI Studio API Keys

### **Free Tier:**
- ✅ **15 requests per minute**
- ✅ **1500 requests per day**
- ✅ **1 million tokens per minute**
- ✅ **Completely FREE!**

### **Models Available:**
- `gemini-1.5-flash` **(recommended)** - Fast and efficient
- `gemini-1.5-pro` - More capable, slower
- `gemini-2.0-flash-exp` - Experimental, latest

---

## 🔄 Alternative: Use OpenRouter (Optional)

If you want to try different models:

### Step 1: Get OpenRouter Key

Go to: **https://openrouter.ai/keys**

1. Sign up/Login
2. Create API key
3. Get **$1 free credit**

### Step 2: Update config.php

```php
// AI Configuration
define('AI_PROVIDER', 'openrouter');  // ← Change this
define('AI_API_KEY', 'YOUR_OPENROUTER_KEY');  // ← OpenRouter key
define('AI_MODEL', 'google/gemini-2.0-flash-lite-preview-02-05:free');  // Free model
```

**Free models on OpenRouter:**
- `google/gemini-2.0-flash-lite-preview-02-05:free`
- `mistralai/mistral-7b-instruct:free`
- `meta-llama/llama-3.2-1b-instruct:free`

---

## 🐛 If Still Getting Errors

### Error: "Quota exceeded"
**Solution:** Wait a few minutes, or use a different Google account for a new key

### Error: "API key not valid"
**Solution:** Make sure you copied the entire key (starts with 'AIzaSy' for Gemini)

### Error: "Permission denied"
**Solution:** Make sure the key is for Generative AI API, not other Google APIs

### Error in console (F12)
**Check:** The exact error message - it will tell you what's wrong

---

## 📊 Current Configuration

Your current setup:
```php
AI_PROVIDER: gemini
AI_API_KEY: AIzaSyBAEQMkw01uCSQ1XFin3H4_D6DJ7HeHM7I (expired)
AI_MODEL: gemini-1.5-flash
```

**This key is expired/invalid**. Get a new one from Google AI Studio!

---

##  Quick Summary

1. **Go to:** https://aistudio.google.com/app/apikey
2. **Create** new API key
3. **Copy** the key
4. **Edit:** `api/config.php` line 31
5. **Replace** old key with new key
6. **Save** file
7. **Test** question generation

---

## 🎯 Expected Result

After updating the key:

```
Topic: "Machine Learning"
Count: 5
Difficulty: Medium

Result:
✓ Successfully generated and saved 5 questions as drafts
```

Questions will appear in **Admin → Questions** (marked as inactive/drafts).

---

## 📝 Notes

- **Free quota resets daily**
- **Keys don't expire unless you delete them**
- **You can have multiple keys**
- **Each Google account can create multiple projects with keys**

---

**Get your new key from Google AI Studio and update config.php!** 🚀

Links:
- **Google AI Studio:** https://aistudio.google.com/app/apikey
- **OpenRouter (alternative):** https://openrouter.ai/keys
