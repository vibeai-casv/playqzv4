# PlayQz v4 Constitution

## Core Principles
1.  **Backend Security:** All MySQL interactions MUST use PDO prepared statements to prevent SQL injection.
2.  **Frontend State:** Use Zustand (`useAuth`, `useQuizStore`) for all global state management.
3.  **API Standards:** All endpoints must return JSON in the format: `{ "success": bool, "data": mixed, "error": string }`.
4.  **Feature Integrity:** Ensure existing features are not altered or broken when implementing new changes unless explicitly instructed.
5.  **Media Handling:** Preserve **Original Filenames** in `/uploads`. Absolutely no hashing or renaming to random strings. Images for logo/personality questions must be uploaded with their descriptive source names (e.g., `SamAltman.png`).
    *   In `admin/bulk-edit`, when uploading files, system changes must not occur to the file name. Use the exact same filename of the selected file during upload.
6.  **Answer Randomization:** All quiz-taking interfaces and API endpoints must ensure that answer choices (`options`) are shuffled to prevent the correct answer from appearing at a fixed position.

## Admin & Access
- **Super Admin Credentials:** 
    - **Email:** `vibeaicasv@gmail.com`
    - **Password:** `password`

## AI Content Generation
- **Validation:** Gemini-generated content must be strictly validated for schema integrity (correct JSON structure, 4 options, 1 correct answer) before database insertion.
- **Workflow:** Generated questions must be initially saved with `status = 'draft'` to allow administrative review.
- **Randomization:** Generated content must have the `options` array shuffled so that the correct answer is not consistently provided in the same position.
