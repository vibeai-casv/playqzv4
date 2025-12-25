# PlayQz v4 - Project Architecture & Context

## 1. Project Overview
**PlayQz v4** is a high-performance, AI-driven quiz application designed for dynamic learning and engagement. It combines a sleek, modern React frontend with a scalable PHP/MySQL backend. The project's unique value proposition is its integration with the **Google Gemini API**, allowing for automated, high-quality quiz generation across any topic.

Initially migrated from a Supabase backend to a custom PHP implementation, PlayQz v4 offers complete control over data sovereignty, deployment paths, and media management.

---

## 2. Core Technology Stack

### Frontend (Client-side)
- **Framework**: React 18 with Vite for ultra-fast builds.
- **Language**: TypeScript for type-safe development.
- **Styling**: Tailwind CSS for a premium, responsive UI.
- **State Management**: Zustand (Stores: `useAuth`, `useQuizStore`).
- **Networking**: Axios with centralized interceptors for token management.
- **Visualization**: Recharts for performance analytics.

### Backend (Server-side)
- **Language**: PHP 8.x (Custom REST API architecture).
- **Database**: MySQL 8.x using PDO for secure, prepared SQL execution.
- **AI Integration**: Google Gemini API for automated question generation.
- **Media Handling**: Custom PHP filesystem logic for managing original images.

---

## 3. Main Features

### For Users
- **Dynamic Quiz Generation**: Create custom quizzes based on category, difficulty, and question count.
- **Real-time Feedback**: Immediate results and performance breakdowns after quiz completion.
- **Performance Dashboard**: Track scores, accuracy, and time spent across different topics.
- **Responsive Experience**: Optimized for both high-end desktop displays and mobile devices.

### For Administrators
- **AI Question Generator**: Generate hundreds of questions instantly using AI prompts.
- **Bulk Edit Suite**: A powerful, spreadsheet-like interface for managing large question banks.
- **Media Library**: Centralized management for question images (Logo Identification, Personality ID).
- **Advanced Import/Export**: Migrate content using JSON or ZIP bundles (including images).
- **System Diagnostics**: Built-in tools for verifying database integrity and API connectivity.

---

## 4. Project Structure

```text
📁 playqzv4/ (Root)
├── 📁 client/                # React Frontend Source
│   ├── 📁 src/
│   │   ├── 📁 components/    # Reusable UI & Layouts
│   │   ├── 📁 hooks/         # Custom Business Logic
│   │   ├── 📁 pages/         # Route-level components
│   │   ├── 📁 stores/        # Zustand Global State
│   │   └── 📄 api.ts         # Central API Client
│   └── 📄 vite.config.ts     # Build Configuration
├── 📁 api/                   # PHP Backend API
│   ├── 📁 admin/             # Management Endpoints
│   ├── 📁 auth/              # JWT/Session Login
│   ├── 📁 questions/         # Question CRUD & Import
│   ├── 📁 quiz/              # Quiz Engine Logic
│   ├── 📁 media/             # Upload & Library
│   └── 📄 db.php             # Database Connection (PDO)
├── 📁 uploads/               # Permanent Image Storage
├── 📄 deploy.ps1             # PowerShell Deployment Automation
└── 📄 ARCHITECTURE.md        # Current Project Documentation
```

---

## 5. Deployment Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          LOCAL DEVELOPMENT                                  │
│                         (Windows Machine)                                   │
│                                                                             │
│  📁 e:\projects\playqzv4\                                                   │
│  │                                                                          │
│  ├── 📁 client/                    Frontend (React + Vite)                 │
│  │   └── dist/                      ⚙️ Built with: npm run build           │
│  │                                                                          │
│  ├── 📁 api/                       Backend (PHP)                           │
│  │                                                                          │
│  └── 📄 deploy.ps1                 🚀 Deployment Script                    │
│                                                                             │
└──────────────────────────────┬──────────────────────────────────────────────┘
                               │
                               │  🔐 SSH/SCP Upload
                               │  (Automated by deploy.ps1)
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                      PRODUCTION SERVER                                      │
│                      aiquiz.vibeai.cv                                       │
│                                                                             │
│  🌐 Web Server (Apache/Nginx)                                               │
│  ├── /var/www/aiquiz.vibeai.cv/public/     # Frontend                       │
│  ├── /var/www/aiquiz.vibeai.cv/api/        # Backend API                    │
│  └── /var/www/aiquiz.vibeai.cv/uploads/    # Media                          │
│                                                                             │
│  🗄️  MySQL Database (aiqz_production)                                       │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 6. Security & Data Flow

- **Request Security**: All communication is over HTTPS via TLS 1.3.
- **Authentication**: Bearer tokens are used for all protected routes, stored in `localStorage` and managed by the `useAuth` hook.
- **Database Safety**: 100% of queries use PDO prepared statements to prevent SQL Injection.
- **Role-Based Access (RBAC)**: Users are categorized into `admin` and `user` roles with distinct UI and API access permissions.

## 7. Operational Workflows

### AI Question Generation
1. Admin specifies topic and parameters in the UI.
2. Frontend calls `/api/admin/generate_questions.php`.
3. Backend sends structured prompt to Gemini AI.
4. AI returns JSON-formatted questions.
5. Backend validates and inserts questions into the `questions` table.

### Content Migration
- **JSON Export**: Downloads the current filtered question set.
- **Bundle Export**: Creates a `.zip` containing questions (JSON) and all associated images from `uploads/`.
- **Import**: Reverses the process, ensuring no duplicate entries and correct media linking.

---

## 8. Detailed Feature Breakdown

### A. Specialized Question Types
PlayQz v4 supports various question formats, with a focus on visual identification:
- **Logo Identification**: A visual challenge where the user must identify a brand or entity based on its logo image. The `image_url` field links to the logo file in `uploads/`.
- **Personality Identification**: Questions featuring images of famous individuals. Users must select the correct name from multiple choices. 
- **Dynamic Shuffling**: To ensure integrity, answer options are randomized on the frontend during each quiz attempt, preventing pattern memorization.

### B. Image Management & Editing
- **Adding Images**: Admins can upload images via the "Media Library" or directly within the "Edit Question" modal. 
- **Naming Convention**: The system preserves the **Original Filename** (e.g., `Tesla_Logo.png`) to improve administrative clarity and SEO, moving away from hashed filenames.
- **Linking**: Images are stored in the `uploads/` directory, and their relative paths are stored in the `questions.image_url` column.
- **Bulk Editing**: The Bulk Edit interface allows admins to quickly assign or change images for dozens of questions simultaneously by selecting from a visual media picker.

### C. Data Portability: JSON vs. Bundle
- **JSON Import/Export**: Best for quick text-based updates. It exports all question metadata (text, options, types) but requires images to already exist on the target server.
- **Bundle (ZIP) Workflow**: The "Gold Standard" for migration.
    - **Export**: Generates a ZIP containing a `questions.json` file and a folder with every image referenced by those questions.
    - **Import**: The system extracts the ZIP, moves images to the `uploads/` directory, and intelligently updates the database paths. It includes **collision detection** to avoid duplicate questions.

### D. Quiz Lifecycle: Config to Completion
#### 1. Configuration (`QuizConfig.tsx`)
Users customize their experience before starting:
- **Category Selection**: Choose from AI-generated or manually curated topics.
- **Difficulty Tuning**: Filter questions by Easy, Medium, or Hard.
- **Volume Control**: Determine the exact number of questions for the session.
- **State Management**: The `useQuizStore` initializes the session with these parameters.

#### 2. Taking the Quiz (`Quiz.tsx`)
- **Active Session**: A progress bar tracks the user's position.
- **Timer Engine**: Tracks time spent per question and for the total quiz.
- **User Interface**: Clean, focused design with large targets for mobile-friendly interaction.
- **Submission**: On completion, the frontend sends a batch of responses to `/api/quiz/submit.php`, which calculates the final score, updates the user's profile stats, and logs the activity.

---
*Last Updated: December 2025*
