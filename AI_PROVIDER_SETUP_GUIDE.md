# AI Provider API Key Setup Guide

This guide will help you obtain API keys for all supported AI providers in the question generation system.

---

## 🌟 Quick Comparison

| Provider | Free Tier | Best For | Speed | Quality |
|----------|-----------|----------|-------|---------|
| **Gemini** | ✅ Yes (60 req/min) | General use | ⚡ Fast | ⭐⭐⭐⭐ |
| **OpenRouter** | ✅ Yes (free models) | Multiple models | ⚡ Fast | ⭐⭐⭐⭐⭐ |
| **Groq** | ✅ Yes (limited) | Speed | 🚀 Very Fast | ⭐⭐⭐⭐ |
| **OpenAI** | ❌ No ($5 min) | Best quality | 🐌 Slower | ⭐⭐⭐⭐⭐ |
| **Anthropic** | ❌ No ($5 min) | Claude models | ⚡ Fast | ⭐⭐⭐⭐⭐ |
| **Hugging Face** | ✅ Yes (rate limited) | Open source | 🐌 Slower | ⭐⭐⭐ |
| **Together AI** | ✅ Yes ($25 credit) | Cost-effective | ⚡ Fast | ⭐⭐⭐⭐ |

**Recommended for beginners:** Start with **Gemini** (free, reliable) or **OpenRouter** (free models, flexible)

---

## 1️⃣ Google Gemini (Recommended - FREE)

### Why Choose Gemini?
- ✅ **100% FREE** with generous limits
- ✅ 60 requests per minute (free tier)
- ✅ 1,500 requests per day
- ✅ High-quality responses
- ✅ Official Google AI

### Steps to Get API Key:

1. **Visit Google AI Studio**
   - Go to: https://aistudio.google.com/

2. **Sign In**
   - Use your Google account
   - Accept terms of service

3. **Get API Key**
   - Click **"Get API Key"** button in the top right
   - Click **"Create API Key"**
   - Choose **"Create API key in new project"** (or select existing)
   - Copy your API key (starts with `AIza...`)

4. **Configure in config.php**
   ```php
   define('AI_PROVIDER', 'gemini');
   define('AI_API_KEY', 'AIzaSy...your-key-here');
   define('AI_MODEL', 'gemini-1.5-flash'); // or gemini-1.5-pro
   ```

### Available Models:
- `gemini-1.5-flash` - Fast, efficient (recommended)
- `gemini-1.5-pro` - More powerful, slower
- `gemini-2.0-flash-exp` - Experimental, latest features

---

## 2️⃣ OpenRouter (Recommended - FREE Models Available)

### Why Choose OpenRouter?
- ✅ **FREE models available** (Gemini, Llama, etc.)
- ✅ Access to 100+ AI models from one API
- ✅ Pay only for what you use
- ✅ No monthly subscription
- ✅ Switch models without changing code

### Steps to Get API Key:

1. **Visit OpenRouter**
   - Go to: https://openrouter.ai/

2. **Sign Up**
   - Click **"Sign In"** → **"Continue with Google"** (or email)
   - Complete registration

3. **Get API Key**
   - Go to **Keys** page: https://openrouter.ai/keys
   - Click **"Create Key"**
   - Give it a name (e.g., "AI Quizzer")
   - Copy the key (starts with `sk-or-v1-...`)

4. **Add Credits (Optional)**
   - Go to: https://openrouter.ai/credits
   - Add $5-10 for paid models (optional)
   - Many models are FREE!

5. **Configure in config.php**
   ```php
   define('AI_PROVIDER', 'openrouter');
   define('AI_API_KEY', 'sk-or-v1-...your-key-here');
   define('AI_MODEL', 'google/gemini-2.0-flash-exp:free'); // FREE!
   ```

### Free Models (Recommended):
- `google/gemini-2.0-flash-exp:free` - Latest Gemini, FREE
- `meta-llama/llama-3.2-11b-vision-instruct:free` - Meta's Llama, FREE
- `mistralai/mistral-7b-instruct:free` - Mistral, FREE
- `qwen/qwen-2-7b-instruct:free` - Qwen, FREE

### Popular Paid Models (Low Cost):
- `anthropic/claude-3.5-sonnet` - $3/M tokens
- `openai/gpt-4-turbo` - $10/M tokens
- `google/gemini-pro-1.5` - $3.50/M tokens

**Browse all models:** https://openrouter.ai/models

---

## 3️⃣ Groq (FREE - Very Fast)

### Why Choose Groq?
- ✅ **FREE tier available**
- ✅ **ULTRA FAST** inference (fastest in the market)
- ✅ 14,400 requests per day (free)
- ✅ Llama models

### Steps to Get API Key:

1. **Visit Groq Console**
   - Go to: https://console.groq.com/

2. **Sign Up**
   - Click **"Sign In"**
   - Use Google, GitHub, or email

3. **Get API Key**
   - Go to **API Keys**: https://console.groq.com/keys
   - Click **"Create API Key"**
   - Name it (e.g., "AI Quizzer")
   - Copy the key (starts with `gsk_...`)

4. **Configure in config.php**
   ```php
   define('AI_PROVIDER', 'groq');
   define('AI_API_KEY', 'gsk_...your-key-here');
   define('AI_MODEL', 'llama-3.3-70b-versatile');
   ```

### Available Models:
- `llama-3.3-70b-versatile` - Best quality (recommended)
- `llama-3.1-8b-instant` - Faster, lighter
- `mixtral-8x7b-32768` - Mixtral model

**Free Limits:** 14,400 requests/day, 30 requests/minute

---

## 4️⃣ OpenAI (Paid - Industry Standard)

### Why Choose OpenAI?
- ⭐ **Best quality** responses
- ⭐ GPT-4, GPT-3.5 Turbo
- ⭐ Industry standard
- ❌ No free tier ($5 minimum)

### Steps to Get API Key:

1. **Visit OpenAI Platform**
   - Go to: https://platform.openai.com/

2. **Sign Up**
   - Create account or sign in
   - Verify phone number

3. **Add Billing**
   - Go to: https://platform.openai.com/settings/organization/billing/overview
   - Add payment method
   - Add at least $5 credits

4. **Get API Key**
   - Go to: https://platform.openai.com/api-keys
   - Click **"Create new secret key"**
   - Name it, copy key (starts with `sk-...`)

5. **Configure in config.php**
   ```php
   define('AI_PROVIDER', 'openai');
   define('AI_API_KEY', 'sk-...your-key-here');
   define('AI_MODEL', 'gpt-3.5-turbo'); // or gpt-4-turbo
   ```

### Available Models:
- `gpt-3.5-turbo` - $0.50/$1.50 per M tokens (cheap)
- `gpt-4-turbo` - $10/$30 per M tokens (best)
- `gpt-4o` - $5/$15 per M tokens (balanced)

**Pricing:** https://openai.com/api/pricing/

---

## 5️⃣ Anthropic Claude (Paid - High Quality)

### Why Choose Anthropic?
- ⭐ **Claude 3.5** - Excellent reasoning
- ⭐ Large context windows
- ⭐ Good for complex questions
- ❌ No free tier ($5 minimum)

### Steps to Get API Key:

1. **Visit Anthropic Console**
   - Go to: https://console.anthropic.com/

2. **Sign Up**
   - Create account
   - Verify email

3. **Add Billing**
   - Go to **Billing**: https://console.anthropic.com/settings/billing
   - Add payment method
   - Add credits ($5 minimum)

4. **Get API Key**
   - Go to **API Keys**: https://console.anthropic.com/settings/keys
   - Click **"Create Key"**
   - Name it, copy key (starts with `sk-ant-...`)

5. **Configure in config.php**
   ```php
   define('AI_PROVIDER', 'anthropic');
   define('AI_API_KEY', 'sk-ant-...your-key-here');
   define('AI_MODEL', 'claude-3-5-haiku-20241022');
   ```

### Available Models:
- `claude-3-5-haiku-20241022` - Fastest, cheapest ($1/$5 per M tokens)
- `claude-3-5-sonnet-20241022` - Best quality ($3/$15 per M tokens)
- `claude-3-opus-20240229` - Most powerful ($15/$75 per M tokens)

**Pricing:** https://www.anthropic.com/pricing

---

## 6️⃣ Hugging Face (FREE - Open Source)

### Why Choose Hugging Face?
- ✅ **100% FREE** (rate limited)
- ✅ Open-source models
- ✅ No credit card required
- ⚠️ Slower inference
- ⚠️ Rate limits can be restrictive

### Steps to Get API Key:

1. **Visit Hugging Face**
   - Go to: https://huggingface.co/

2. **Sign Up**
   - Click **"Sign Up"**
   - Use email or OAuth

3. **Get API Token**
   - Go to: https://huggingface.co/settings/tokens
   - Click **"New token"**
   - Name: "AI Quizzer"
   - Type: **Read**
   - Click **"Generate"**
   - Copy token (starts with `hf_...`)

4. **Configure in config.php**
   ```php
   define('AI_PROVIDER', 'huggingface');
   define('AI_API_KEY', 'hf_...your-token-here');
   define('AI_MODEL', 'meta-llama/Meta-Llama-3-8B-Instruct');
   ```

### Available Models:
- `meta-llama/Meta-Llama-3-8B-Instruct` - Good quality
- `mistralai/Mistral-7B-Instruct-v0.2` - Fast
- `google/flan-t5-xxl` - Reliable

**Note:** Free tier has rate limits and may require model warm-up time.

---

## 7️⃣ Together AI (FREE Credits)

### Why Choose Together AI?
- ✅ **$25 FREE credits** on signup
- ✅ 60+ open-source models
- ✅ Fast inference
- ✅ Cost-effective ($0.20-$0.90 per M tokens)

### Steps to Get API Key:

1. **Visit Together AI**
   - Go to: https://api.together.xyz/

2. **Sign Up**
   - Click **"Get Started"**
   - Sign up with Google or email

3. **Get FREE Credits**
   - New accounts get $25 free credits
   - No credit card required initially

4. **Get API Key**
   - Go to: https://api.together.xyz/settings/api-keys
   - Click **"Create API Key"**
   - Name it, copy key (starts with `...`)

5. **Configure in config.php**
   ```php
   define('AI_PROVIDER', 'together');
   define('AI_API_KEY', 'your-key-here');
   define('AI_MODEL', 'meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo');
   ```

### Available Models:
- `meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo` - Fast, cheap
- `meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo` - Better quality
- `mistralai/Mixtral-8x7B-Instruct-v0.1` - MoE model
- `Qwen/Qwen2.5-72B-Instruct-Turbo` - High quality

**Pricing:** https://api.together.xyz/pricing

---

## 🎯 Recommended Setup for Your Quiz App

### Option 1: 100% Free (Recommended for Testing)
```php
// Use Gemini - reliable and free
define('AI_PROVIDER', 'gemini');
define('AI_API_KEY', 'AIzaSy...your-gemini-key');
define('AI_MODEL', 'gemini-1.5-flash');
```

### Option 2: Best Free Experience (Recommended)
```php
// Use OpenRouter with free models
define('AI_PROVIDER', 'openrouter');
define('AI_API_KEY', 'sk-or-v1-...your-key');
define('AI_MODEL', 'google/gemini-2.0-flash-exp:free');
```

### Option 3: Ultra Fast (Free)
```php
// Use Groq for fastest responses
define('AI_PROVIDER', 'groq');
define('AI_API_KEY', 'gsk_...your-key');
define('AI_MODEL', 'llama-3.3-70b-versatile');
```

### Option 4: Best Quality (Paid)
```php
// Use OpenRouter with Claude
define('AI_PROVIDER', 'openrouter');
define('AI_API_KEY', 'sk-or-v1-...your-key');
define('AI_MODEL', 'anthropic/claude-3.5-sonnet');
```

---

## 🔒 Security Best Practices

1. **Never commit API keys to Git**
   - config.php is already in .gitignore ✅
   
2. **Use environment variables (optional)**
   ```php
   define('AI_API_KEY', getenv('AI_API_KEY') ?: '');
   ```

3. **Rotate keys regularly**
   - Change keys every 3-6 months

4. **Monitor usage**
   - Check provider dashboards for unexpected usage

5. **Set spending limits**
   - Configure billing alerts on paid providers

---

## 📊 Usage Tracking

Each provider has a dashboard to track usage:

- **Gemini:** https://aistudio.google.com/app/apikey
- **OpenRouter:** https://openrouter.ai/activity
- **Groq:** https://console.groq.com/usage
- **OpenAI:** https://platform.openai.com/usage
- **Anthropic:** https://console.anthropic.com/settings/usage
- **Hugging Face:** https://huggingface.co/settings/tokens
- **Together AI:** https://api.together.xyz/usage

---

## 🆘 Troubleshooting

### "Invalid API Key" Error
- ✅ Check key is copied correctly (no extra spaces)
- ✅ Verify provider name matches (case-sensitive)
- ✅ Ensure key hasn't expired or been revoked

### "Quota Exceeded" Error
- ✅ Wait for quota reset (usually daily)
- ✅ Switch to different provider
- ✅ Upgrade to paid tier

### "Model Not Found" Error
- ✅ Check model name spelling
- ✅ Verify model is available for your provider
- ✅ Some models require waitlist access

### Slow Responses
- ✅ Try Groq for fastest inference
- ✅ Use smaller models (e.g., 8B instead of 70B)
- ✅ Check your internet connection

---

## 💡 Pro Tips

1. **Start with free tiers** - Test with Gemini or OpenRouter free models
2. **Use OpenRouter** - Single API for multiple models
3. **Monitor costs** - Set up billing alerts on paid providers
4. **Batch requests** - Generate multiple questions at once to save on API calls
5. **Cache results** - Store generated questions in database (already implemented ✅)
6. **Fallback providers** - Ready to switch if one provider has issues

---

## 📚 Additional Resources

- **OpenRouter Models:** https://openrouter.ai/models
- **Model Comparison:** https://artificialanalysis.ai/
- **LLM Benchmarks:** https://huggingface.co/spaces/lmsys/chatbot-arena-leaderboard

---

## 🎉 You're Ready!

Choose your preferred provider, get your API key, and start generating quiz questions! 

**Need help?** Check the troubleshooting section or contact support.
