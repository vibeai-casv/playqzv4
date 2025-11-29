# PlayQz v4 Project Context

## 1. Project Overview
**PlayQz v4** is a modern, AI-powered quiz application designed for students, professionals, and hobbyists. It features a robust quiz generation engine, user progress tracking, and an administrative dashboard for content management.

**Current Status**: The project is in the final stages of migrating from a Supabase backend to a custom PHP API with a MySQL database. The frontend is built with React (Vite) and TypeScript.

## 2. Technology Stack

### Frontend
- **Framework**: React 18 (Vite)
- **Language**: TypeScript
- **Styling**: Tailwind CSS
- **State Management**: Zustand (`useAuth`, `useQuizStore`)
- **Routing**: React Router DOM
- **HTTP Client**: Axios
- **Icons**: Lucide React
- **Charts**: Recharts
- **Forms**: React Hook Form + Zod

### Backend
- **Language**: PHP 8.x
- **Database**: MySQL 8.x
- **Driver**: PDO (PHP Data Objects)
- **Server**: Built-in PHP server (Development) / Apache/Nginx (Production)

### Authentication
- **Method**: Custom Token-based Authentication
- **Storage**: `users` and `sessions` tables in MySQL
- **Token Handling**: Bearer token in Authorization header, stored in localStorage

## 3. Architecture

The project follows a decoupled client-server architecture.

### Client (`/client`)
- **`src/pages`**: Contains route components (User Dashboard, Quiz, Admin Panel, etc.).
- **`src/components`**: Reusable UI components (Auth forms, Quiz cards, Modals).
- **`src/hooks`**: Custom hooks for business logic (`useAuth`, `useAdmin`, `useUserStats`).
- **`src/stores`**: Global state management using Zustand.
- **`src/lib`**: Utilities, API client configuration, and validation schemas.

### API (`/api`)
- **Entry Point**: `index.php` (handles CORS and basic routing if needed, though mostly direct file access).
- **Configuration**: `config.php` (DB credentials), `db.php` (PDO connection), `utils.php` (Helper functions).
- **Modules**:
    - `auth/`: Login, Signup, Me (Profile).
    - `questions/`: List, Create, Update, Delete, Stats.
    - `quiz/`: Generate, Submit, Get Attempt, History.
    - `media/`: List, Upload, Delete.
    - `user/`: Stats.
    - `analytics/`: Logs.

## 4. Database Schema

### Tables
1.  **`users`**: Core authentication data (ID, email, password hash).
2.  **`profiles`**: User details (Name, phone, role, category, bio, stats).
3.  **`sessions`**: Active user sessions (Token, expiry).
4.  **`questions`**: Quiz questions (Text, type, options, correct answer, difficulty, category).
5.  **`media_library`**: Uploaded images for questions/branding.
6.  **`quiz_attempts`**: Records of quizzes taken by users (Score, status, time spent).
7.  **`quiz_responses`**: Individual answers to questions within an attempt.
8.  **`user_activity_logs`**: Audit trail of user actions (Login, quiz start, etc.).

### Key Relationships
- `profiles.id` -> `users.id` (1:1)
- `quiz_attempts.user_id` -> `profiles.id` (M:1)
- `quiz_responses.attempt_id` -> `quiz_attempts.id` (M:1)
- `questions.media_id` -> `media_library.id` (M:1)

## 5. API Endpoints

### Authentication
- `POST /auth/signup.php`: Register new user.
- `POST /auth/login.php`: Authenticate user.
- `GET /auth/me.php`: Get current user profile.

### Questions
- `GET /questions/list.php`: Fetch questions with filters.
- `POST /questions/create.php`: Add new question (Admin).
- `POST /questions/update.php`: Edit question (Admin).
- `POST /questions/delete.php`: Remove question (Admin).
- `GET /questions/stats.php`: Get question counts by category.

### Quiz
- `POST /quiz/generate.php`: Create a new quiz attempt based on config.
- `GET /quiz/get.php`: Fetch a specific quiz attempt and its questions.
- `POST /quiz/submit.php`: Submit quiz answers and calculate score.
- `GET /quiz/history.php`: Get user's past quiz attempts.

### Media
- `POST /media/upload.php`: Upload image file.
- `GET /media/list.php`: List available media.
- `POST /media/delete.php`: Delete media file.

### User & Analytics
- `GET /user/stats.php`: Get user performance statistics.
- `POST /profile/update.php`: Update user profile details.
- `GET /analytics/logs.php`: Fetch system activity logs (Admin).

## 6. Frontend Architecture

### Key Pages
- **User**:
    - `Dashboard`: Overview of stats, recent quizzes, and quick actions.
    - `QuizConfig`: Setup for a new quiz (Category, difficulty, etc.).
    - `Quiz`: Active quiz interface.
    - `History`: List of past attempts.
    - `Profile`: User settings and profile management.
- **Admin**:
    - `Dashboard`: System overview.
    - `Questions`: Question bank management.
    - `Media`: Image library management.
    - `Users`: User management (Placeholder/In-progress).
    - `ActivityLogs`: System audit logs.

### State Management
- **`useAuth`**: Manages user session, login/signup/logout, and profile data.
- **`useQuizStore`**: Manages active quiz state (current question, answers, timer).

## 7. Key Features

- **Dynamic Quiz Generation**: Users can generate quizzes based on selected categories and difficulty levels.
- **Real-time Feedback**: Immediate feedback on answers (if configured).
- **Performance Tracking**: Detailed statistics on scores, completion rates, and time spent.
- **Media Support**: Questions can include images (logos, personalities).
- **Role-Based Access**: Admin interface for managing content and viewing logs.
- **Responsive Design**: Fully responsive UI for desktop and mobile.

## 8. Pending Items / Future Work
- **OAuth Integration**: Google Login is currently a placeholder.
- **Phone Authentication**: OTP login is not yet implemented.
- **Advanced Analytics**: More detailed charts and insights for admins.
- **User Management**: Admin ability to ban/unban users is partially implemented.
