# AI Quiz Application API Documentation

## 📋 Overview

This is the API documentation for the AI Quiz Application (PlayQzV4), now powered by a **PHP/MySQL backend**.

**Base URL:** `http://localhost:8000/api` (Local Development)

## 🚀 Quick Start

### Authentication

All authenticated endpoints require a JWT-like token (session token) in the Authorization header:

```bash
Authorization: Bearer <your-token>
```

Get your token by:
1. Signing up: `POST /auth/signup.php`
2. Logging in: `POST /auth/login.php`

## 📚 API Endpoints

### 🔐 Authentication

- **POST** `/auth/signup.php`
  - Register a new user.
  - Body: `{ email, password, name, phone, ... }`
- **POST** `/auth/login.php`
  - Login with email/password.
  - Body: `{ email, password }`
- **GET** `/auth/me.php`
  - Get current user profile.

### 👤 User

- **GET** `/user/stats.php`
  - Get user statistics and recent attempts.

### 🎯 Quiz

- **POST** `/quiz/generate.php`
  - Generate a new quiz.
  - Body: `{ numQuestions, difficulty, categories, ... }`
- **GET** `/quiz/get.php?id={attemptId}`
  - Get quiz attempt details and questions.
- **POST** `/quiz/submit.php`
  - Submit quiz answers.
  - Body: `{ attemptId, responses: [...] }`
- **GET** `/quiz/history.php`
  - Get quiz history for the current user.

### ❓ Questions (Admin)

- **GET** `/questions/list.php`
  - List questions with filtering.
- **POST** `/questions/create.php`
  - Create a new question.
- **POST** `/questions/update.php`
  - Update an existing question.
- **POST** `/questions/delete.php`
  - Delete a question.

### 👑 Admin

- **GET** `/admin/users.php`
  - List all users (paginated).
- **GET** `/admin/analytics.php` (Planned)
  - Get system-wide analytics.

## 🛠️ Setup

1. **Database**: Import `api/schema.sql` into your MySQL database (`aiqz`).
2. **Config**: Update `api/config.php` with your database credentials.
3. **Server**: Run the PHP built-in server:
   ```bash
   cd api
   php -S localhost:8000
   ```
4. **Client**: Run the Vite dev server:
   ```bash
   cd client
   npm run dev
   ```

## 📝 Notes

- All responses are in JSON format.
- Errors follow the format: `{ "error": "Error message" }`.
- Dates are typically in ISO 8601 format or MySQL timestamp format.
