# AI Question Generation Setup

The AI Question Generator feature allows you to automatically create quiz questions using either **Google Gemini** or **OpenRouter**.

## Option 1: OpenRouter (Recommended for Flexibility)

OpenRouter allows you to access many different AI models (including free ones like Gemini Flash Lite, Llama 3, etc.) with a single API key.

1.  **Get an API Key**:
    -   Go to **[OpenRouter.ai](https://openrouter.ai/)**.
    -   Sign up and create an API key.
    -   (Optional) Add credits if you want to use paid models.

2.  **Configure `api/config.php`**:
    ```php
    // AI Configuration
    define('AI_PROVIDER', 'openrouter');
    define('AI_API_KEY', 'sk-or-v1-...'); // Your OpenRouter Key
    define('AI_MODEL', 'google/gemini-2.0-flash-lite-preview-02-05:free'); // Or any other model ID
    ```

    *Popular Free Models on OpenRouter:*
    -   `google/gemini-2.0-flash-lite-preview-02-05:free`
    -   `meta-llama/llama-3-8b-instruct:free`
    -   `mistralai/mistral-7b-instruct:free`

## Option 2: Google Gemini (Direct)

If you prefer to connect directly to Google without a middleman.

1.  **Get an API Key**:
    -   Go to **[Google AI Studio](https://aistudio.google.com/app/apikey)**.
    -   Create an API key.

2.  **Configure `api/config.php`**:
    ```php
    // AI Configuration
    define('AI_PROVIDER', 'gemini');
    define('AI_API_KEY', 'AIzaSy...'); // Your Google API Key
    define('AI_MODEL', 'gemini-1.5-flash'); // Model name
    ```

## 3. Test It

1.  Log in to your Admin Dashboard.
2.  Go to the **Questions** page.
3.  Click on **"AI Generator"**.
4.  Enter a topic (e.g., "History of Computers") and click **Generate**.
5.  The new questions will appear in the **Drafts** tab.

## Troubleshooting

-   **"AI API Key is not configured"**: You didn't save the key in `config.php`.
-   **"OpenRouter API Error"**: Check your credit balance or if the model you selected is currently available.
-   **"Failed to parse AI response"**: The AI generated malformed JSON. Try again with a simpler topic or a smarter model.
