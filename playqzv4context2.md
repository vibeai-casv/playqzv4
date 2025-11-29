# PlayQZ v4 - Project Context Documentation

**Last Updated**: November 29, 2025  
**Version**: 4.0.0  
**Repository**: https://github.com/vibeai-casv/playqzv4.git

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [Technology Stack](#technology-stack)
4. [Project Structure](#project-structure)
5. [Database Schema](#database-schema)
6. [API Endpoints](#api-endpoints)
7. [Frontend Components](#frontend-components)
8. [Authentication & Authorization](#authentication--authorization)
9. [Key Features](#key-features)
10. [Migration from v3](#migration-from-v3)
11. [Development Setup](#development-setup)
12. [Deployment](#deployment)
13. [Known Issues & Future Enhancements](#known-issues--future-enhancements)

---

## 📖 Project Overview

**PlayQZ v4** is an AI-powered quiz platform designed for creating, managing, and taking interactive quizzes. The platform features a modern AI/Cyber-themed interface with comprehensive admin controls and user analytics.

### Purpose
- Provide an engaging quiz-taking experience with AI-generated questions
- Enable administrators to manage questions, users, and media
- Track user performance and generate detailed analytics
- Support multiple question types including text MCQ, image-based identification, and true/false

### Target Users
1. **End Users**: Take quizzes, track progress, view results
2. **Administrators**: Manage content, users, and system settings
3. **Content Creators**: Generate and curate quiz questions

---

## 🏗️ Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Client Layer                          │
│  ┌──────────────────────────────────────────────────────┐   │
│  │   React 19 + Vite + TypeScript                       │   │
│  │   - User Dashboard                                   │   │
│  │   - Admin Panel                                      │   │
│  │   - Quiz Interface                                   │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕ HTTP/REST
┌─────────────────────────────────────────────────────────────┐
│                      API Layer (PHP 8+)                      │
│  ┌──────────────┬──────────────┬──────────────────────┐    │
│  │ Auth         │ Quiz         │ Admin                │    │
│  │ - Login      │ - Generate   │ - Users              │    │
│  │ - Signup     │ - Submit     │ - Questions          │    │
│  │ - Session    │ - History    │ - Media              │    │
│  └──────────────┴──────────────┴──────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                            ↕ PDO
┌─────────────────────────────────────────────────────────────┐
│                     Database Layer (MySQL)                   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Tables: users, profiles, questions, quiz_attempts,  │   │
│  │  quiz_responses, media_library, activity_logs        │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Design Patterns

1. **MVC Pattern**: Separation of concerns between data, logic, and presentation
2. **RESTful API**: Stateless communication using standard HTTP methods
3. **Repository Pattern**: Database abstraction through PDO
4. **State Management**: Zustand for client-side state
5. **Component-Based Architecture**: Reusable React components

---

## 💻 Technology Stack

### Frontend
- **Framework**: React 19.2.0
- **Build Tool**: Vite 7.2.4
- **Language**: TypeScript 5.9.3
- **Styling**: Tailwind CSS 4.1.17
- **State Management**: Zustand 5.0.8
- **Form Handling**: React Hook Form 7.66.1 + Zod 4.1.13
- **Routing**: React Router DOM 7.9.6
- **Charts**: Recharts 3.5.0
- **Icons**: Lucide React 0.555.0
- **Notifications**: Sonner 2.0.7
- **Animations**: Canvas Confetti 1.9.3

### Backend
- **Language**: PHP 8+
- **Database**: MySQL 8.0+
- **Database Driver**: PDO (PHP Data Objects)
- **Authentication**: JWT-like token system with sessions table
- **File Upload**: Native PHP file handling
- **CORS**: Custom implementation

### Development Tools
- **Package Manager**: npm
- **Linting**: ESLint 9.39.1
- **Type Checking**: TypeScript
- **Version Control**: Git
- **CI/CD**: GitHub Actions (configured)

---

## 📁 Project Structure

```
playqzv4/
├── api/                          # PHP Backend
│   ├── admin/                    # Admin endpoints
│   │   ├── analytics.php         # System analytics
│   │   ├── toggle_user.php       # Enable/disable users
│   │   ├── user_activity.php     # User activity tracking
│   │   └── users.php             # User management
│   ├── analytics/                # Analytics endpoints
│   │   └── logs.php              # Activity logs
│   ├── auth/                     # Authentication
│   │   ├── login.php             # User login
│   │   ├── me.php                # Current user profile
│   │   └── signup.php            # User registration
│   ├── media/                    # Media management
│   │   ├── delete.php            # Delete media
│   │   ├── list.php              # List media files
│   │   └── upload.php            # Upload media
│   ├── profile/                  # User profile
│   │   └── update.php            # Update profile
│   ├── questions/                # Question management
│   │   ├── create.php            # Create question
│   │   ├── delete.php            # Delete question
│   │   ├── list.php              # List questions
│   │   ├── stats.php             # Question statistics
│   │   └── update.php            # Update question
│   ├── quiz/                     # Quiz operations
│   │   ├── generate.php          # Generate quiz
│   │   ├── get.php               # Get quiz details
│   │   ├── history.php           # Quiz history
│   │   └── submit.php            # Submit quiz
│   ├── user/                     # User operations
│   │   └── stats.php             # User statistics
│   ├── config.php                # Configuration
│   ├── db.php                    # Database connection
│   ├── utils.php                 # Utility functions
│   ├── schema.sql                # Database schema
│   └── index.php                 # API entry point
│
├── client/                       # React Frontend
│   ├── public/                   # Static assets
│   │   └── aiqmpm.png            # Logo
│   ├── src/
│   │   ├── components/           # React components
│   │   │   ├── admin/            # Admin components
│   │   │   │   ├── AIGenerator.tsx
│   │   │   │   ├── MediaPicker.tsx
│   │   │   │   └── QuestionEditor.tsx
│   │   │   ├── auth/             # Auth components
│   │   │   │   ├── AIDisclaimerModal.tsx
│   │   │   │   ├── AuthLayout.tsx
│   │   │   │   ├── LoginForm.tsx
│   │   │   │   └── SignupForm.tsx
│   │   │   ├── layout/           # Layout components
│   │   │   │   ├── Footer.tsx
│   │   │   │   ├── Header.tsx
│   │   │   │   ├── Layout.tsx
│   │   │   │   └── Sidebar.tsx
│   │   │   └── ui/               # UI components
│   │   │       ├── AnimatedCounter.tsx
│   │   │       ├── Modal.tsx
│   │   │       ├── ProgressBar.tsx
│   │   │       └── Skeleton.tsx
│   │   ├── hooks/                # Custom hooks
│   │   │   ├── useAdmin.ts       # Admin operations
│   │   │   ├── useAuth.ts        # Authentication
│   │   │   ├── useQuiz.ts        # Quiz operations
│   │   │   └── useUserStats.ts   # User statistics
│   │   ├── lib/                  # Libraries
│   │   │   ├── api.ts            # Axios instance
│   │   │   ├── utils.ts          # Utility functions
│   │   │   └── validations/      # Zod schemas
│   │   ├── pages/                # Page components
│   │   │   ├── admin/            # Admin pages
│   │   │   │   ├── ActivityLogs.tsx
│   │   │   │   ├── Dashboard.tsx
│   │   │   │   ├── Media.tsx
│   │   │   │   ├── Questions.tsx
│   │   │   │   ├── SystemTools.tsx
│   │   │   │   └── Users.tsx
│   │   │   ├── auth/             # Auth pages
│   │   │   │   ├── Login.tsx
│   │   │   │   └── Signup.tsx
│   │   │   └── user/             # User pages
│   │   │       ├── Dashboard.tsx
│   │   │       ├── History.tsx
│   │   │       ├── Profile.tsx
│   │   │       ├── QuizConfig.tsx
│   │   │       ├── QuizResults.tsx
│   │   │       └── TakeQuiz.tsx
│   │   ├── stores/               # State management
│   │   │   └── quizStore.ts      # Quiz state (Zustand)
│   │   ├── types/                # TypeScript types
│   │   │   └── index.ts
│   │   ├── App.tsx               # Main app component
│   │   ├── main.tsx              # Entry point
│   │   └── index.css             # Global styles
│   ├── .env                      # Environment variables
│   ├── package.json              # Dependencies
│   ├── tsconfig.json             # TypeScript config
│   └── vite.config.ts            # Vite config
│
├── qbank/                        # Question bank (JSON)
│   ├── question2.json
│   └── question3.json
│
├── supabase/                     # Legacy (for reference)
│   └── migrations/               # Old Supabase migrations
│
├── .gitignore                    # Git ignore rules
├── README.md                     # Project README
└── DEPLOYMENT.md                 # Deployment guide
```

---

## 🗄️ Database Schema

### Core Tables

#### 1. **users**
```sql
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    disabled BOOLEAN DEFAULT FALSE,
    disabled_reason TEXT
);
```

#### 2. **profiles**
```sql
CREATE TABLE profiles (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    avatar_url TEXT,
    bio TEXT,
    stats JSON,
    terms_accepted BOOLEAN DEFAULT FALSE,
    terms_accepted_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 3. **questions**
```sql
CREATE TABLE questions (
    id VARCHAR(36) PRIMARY KEY,
    question_text TEXT NOT NULL,
    question_type ENUM('text_mcq', 'image_identify_logo', 'image_identify_person', 'true_false') NOT NULL,
    options JSON,
    correct_answer VARCHAR(255) NOT NULL,
    explanation TEXT,
    difficulty ENUM('easy', 'medium', 'hard') NOT NULL,
    category VARCHAR(100),
    tags JSON,
    image_url TEXT,
    points INT DEFAULT 10,
    time_limit_seconds INT DEFAULT 30,
    status ENUM('active', 'inactive', 'draft') DEFAULT 'active',
    ai_generated BOOLEAN DEFAULT FALSE,
    created_by VARCHAR(36),
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 4. **quiz_attempts**
```sql
CREATE TABLE quiz_attempts (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    config JSON NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT DEFAULT 0,
    score DECIMAL(5,2) DEFAULT 0,
    time_limit_seconds INT,
    time_spent_seconds INT DEFAULT 0,
    status ENUM('in_progress', 'completed', 'abandoned', 'expired') DEFAULT 'in_progress',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 5. **quiz_responses**
```sql
CREATE TABLE quiz_responses (
    id VARCHAR(36) PRIMARY KEY,
    attempt_id VARCHAR(36) NOT NULL,
    question_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    user_answer VARCHAR(255),
    is_correct BOOLEAN NOT NULL,
    time_spent_seconds INT DEFAULT 0,
    points_awarded INT DEFAULT 0,
    max_points INT DEFAULT 10,
    question_position INT,
    skipped BOOLEAN DEFAULT FALSE,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 6. **media_library**
```sql
CREATE TABLE media_library (
    id VARCHAR(36) PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255),
    url TEXT NOT NULL,
    type ENUM('logo', 'personality', 'other') NOT NULL,
    mime_type VARCHAR(100),
    size_bytes INT,
    description TEXT,
    uploaded_by VARCHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 7. **activity_logs**
```sql
CREATE TABLE activity_logs (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36),
    activity_type VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    metadata JSON,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

#### 8. **sessions**
```sql
CREATE TABLE sessions (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Indexes
```sql
-- Performance optimization indexes
CREATE INDEX idx_questions_category ON questions(category);
CREATE INDEX idx_questions_difficulty ON questions(difficulty);
CREATE INDEX idx_questions_status ON questions(status);
CREATE INDEX idx_quiz_attempts_user ON quiz_attempts(user_id);
CREATE INDEX idx_quiz_attempts_status ON quiz_attempts(status);
CREATE INDEX idx_quiz_responses_attempt ON quiz_responses(attempt_id);
CREATE INDEX idx_activity_logs_user ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_type ON activity_logs(activity_type);
CREATE INDEX idx_sessions_token ON sessions(token);
```

---

## 🔌 API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/auth/signup.php` | Register new user | No |
| POST | `/auth/login.php` | User login | No |
| GET | `/auth/me.php` | Get current user | Yes |

### Quiz Operations

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/quiz/generate.php` | Generate new quiz | Yes |
| GET | `/quiz/get.php?id={id}` | Get quiz details | Yes |
| POST | `/quiz/submit.php` | Submit quiz answers | Yes |
| GET | `/quiz/history.php` | Get quiz history | Yes |

### Question Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/questions/list.php` | List questions | Admin |
| POST | `/questions/create.php` | Create question | Admin |
| POST | `/questions/update.php` | Update question | Admin |
| POST | `/questions/delete.php` | Delete question | Admin |
| GET | `/questions/stats.php` | Question statistics | Yes |

### User Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/admin/users.php` | List users | Admin |
| GET | `/admin/user_activity.php` | User activity | Admin |
| POST | `/admin/toggle_user.php` | Enable/disable user | Admin |
| POST | `/profile/update.php` | Update profile | Yes |
| GET | `/user/stats.php` | User statistics | Yes |

### Media Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/media/list.php` | List media files | Admin |
| POST | `/media/upload.php` | Upload media | Admin |
| POST | `/media/delete.php` | Delete media | Admin |

### Analytics

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/admin/analytics.php` | System analytics | Admin |
| GET | `/analytics/logs.php` | Activity logs | Admin |

---

## 🎨 Frontend Components

### Component Hierarchy

```
App
├── Layout
│   ├── Header
│   ├── Sidebar (Admin only)
│   └── Footer
├── Routes
│   ├── Auth
│   │   ├── Login
│   │   └── Signup (with AIDisclaimerModal)
│   ├── User
│   │   ├── Dashboard
│   │   ├── QuizConfig
│   │   ├── TakeQuiz
│   │   ├── QuizResults
│   │   ├── History
│   │   └── Profile
│   └── Admin
│       ├── Dashboard
│       ├── Users
│       ├── Questions (with QuestionEditor, AIGenerator)
│       ├── Media (with MediaPicker)
│       ├── ActivityLogs
│       └── SystemTools
```

### Key Component Features

#### User Dashboard
- **Animated Statistics**: Total quizzes, average score, completion rate
- **Performance Chart**: Score progression over time (Recharts)
- **Recent History Table**: Latest quiz attempts with filtering
- **Quick Actions**: Profile update, history review

#### Quiz Interface (TakeQuiz)
- **Progress Bar**: Visual quiz progress
- **Timer**: Countdown with auto-submit
- **Question Display**: Supports text, images, MCQ, true/false
- **Navigation**: Previous/Next question, review mode
- **Auto-save**: Responses saved in real-time

#### Quiz Results
- **Score Display**: Percentage with trophy/alert icon
- **Confetti Animation**: For scores > 80%
- **Performance Breakdown**: Category-wise analysis (Bar chart)
- **Question Review**: Filter by correct/incorrect
- **Share Functionality**: Share results modal

#### Admin Dashboard
- **System Overview**: User count, question count, quiz attempts
- **Analytics Charts**: Daily stats, category distribution
- **Recent Activity**: Latest user actions
- **Quick Stats**: Registration trends

#### Question Editor
- **Rich Form**: Question text, type, options, answer
- **Image Upload**: Support for visual questions
- **Validation**: Zod schema validation
- **Preview Mode**: See question as users will

---

## 🔐 Authentication & Authorization

### Authentication Flow

```
1. User submits credentials → /auth/login.php
2. Server validates credentials
3. Server generates unique token
4. Token stored in sessions table with expiry
5. Token returned to client
6. Client stores token in localStorage
7. Client includes token in Authorization header for subsequent requests
```

### Session Management

- **Token Storage**: MySQL sessions table
- **Token Format**: UUID v4
- **Expiry**: Configurable (default: 7 days)
- **Refresh**: Automatic on valid requests
- **Logout**: Token removed from database

### Authorization Levels

1. **Public**: Login, Signup
2. **Authenticated User**: Quiz operations, profile management
3. **Admin**: User management, question CRUD, media management, analytics

### Middleware

```php
// utils.php - authenticate()
function authenticate($pdo) {
    $token = extractBearerToken();
    $session = validateToken($pdo, $token);
    
    if (!$session || isExpired($session)) {
        jsonResponse(['error' => 'Unauthorized'], 401);
    }
    
    return $session; // Contains user_id, email, role
}
```

---

## ✨ Key Features

### 1. Quiz Generation
- **Dynamic Quiz Creation**: Select categories, difficulty, question count
- **Smart Question Selection**: Random selection from filtered pool
- **Time Limits**: Configurable per quiz
- **Mixed Difficulty**: Option to mix difficulty levels

### 2. Question Types
- **Text MCQ**: Multiple choice with 4 options
- **Image Identification**: Logo/personality recognition
- **True/False**: Binary questions
- **Explanation Support**: Optional explanations for answers

### 3. User Experience
- **AI/Cyber Theme**: Dark mode with indigo/purple accents
- **Responsive Design**: Mobile-first approach
- **Animations**: Smooth transitions, confetti celebrations
- **Real-time Feedback**: Instant answer validation
- **Progress Tracking**: Visual progress indicators

### 4. Admin Capabilities
- **User Management**: View, enable/disable users
- **Question Bank**: CRUD operations on questions
- **Media Library**: Upload and manage images
- **Analytics Dashboard**: System-wide statistics
- **Activity Monitoring**: Detailed activity logs

### 5. Analytics & Reporting
- **User Statistics**: Personal performance metrics
- **Category Analysis**: Performance by topic
- **Time Tracking**: Time spent per question/quiz
- **Trend Analysis**: Performance over time
- **Leaderboards**: (Planned feature)

### 6. Security Features
- **Password Hashing**: bcrypt with salt
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Input sanitization
- **CORS Configuration**: Controlled cross-origin requests
- **Session Management**: Secure token-based auth

---

## 🔄 Migration from v3

### Major Changes

#### 1. Backend Migration
**From**: Supabase (PostgreSQL + Edge Functions)  
**To**: PHP 8 + MySQL

**Reasons**:
- Greater control over backend logic
- Reduced dependency on third-party services
- Cost optimization
- Easier deployment options

#### 2. Database Changes
- PostgreSQL → MySQL
- Supabase Auth → Custom JWT-like system
- Supabase Storage → Local file storage
- RPC functions → PHP endpoints

#### 3. Authentication Overhaul
- Removed: Supabase Auth SDK
- Added: Custom session-based authentication
- Token storage in MySQL sessions table
- Manual password hashing with bcrypt

#### 4. File Upload System
- Removed: Supabase Storage buckets
- Added: PHP file upload to local/server storage
- Direct URL serving from uploads directory

#### 5. Real-time Features
- Removed: Supabase Realtime subscriptions
- Alternative: Polling-based updates (can be enhanced with WebSockets)

### Migration Checklist

- [x] Database schema conversion (PostgreSQL → MySQL)
- [x] Authentication system rewrite
- [x] API endpoints implementation
- [x] Frontend API client update (Supabase SDK → Axios)
- [x] File upload system
- [x] Session management
- [x] Admin panel functionality
- [x] User dashboard
- [x] Quiz generation logic
- [x] Results calculation
- [ ] Email notifications (future)
- [ ] WebSocket real-time updates (future)

---

## 🛠️ Development Setup

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Node.js 18+ and npm
- Git

### Backend Setup

1. **Clone Repository**
```bash
git clone https://github.com/vibeai-casv/playqzv4.git
cd playqzv4
```

2. **Database Setup**
```bash
# Create database
mysql -u root -p
CREATE DATABASE playqz;
exit;

# Import schema
mysql -u root -p playqz < api/schema.sql
```

3. **Configure Environment**
```bash
# Edit api/config.php
DB_HOST = 'localhost'
DB_NAME = 'playqz'
DB_USER = 'your_user'
DB_PASS = 'your_password'
```

4. **Start PHP Server**
```bash
cd api
php -S localhost:8000
```

### Frontend Setup

1. **Install Dependencies**
```bash
cd client
npm install
```

2. **Configure Environment**
```bash
# Create .env file
VITE_API_URL=http://localhost:8000/api
```

3. **Start Development Server**
```bash
npm run dev
```

4. **Access Application**
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api

### Build for Production

```bash
cd client
npm run build
# Output in client/dist/
```

---

## 🚀 Deployment

### Frontend Deployment (Vercel)

1. **Connect Repository**
   - Link GitHub repository to Vercel
   - Set root directory: `client`

2. **Configure Build**
   - Build Command: `npm run build`
   - Output Directory: `dist`
   - Install Command: `npm install`

3. **Environment Variables**
   ```
   VITE_API_URL=https://your-api-domain.com/api
   ```

### Backend Deployment

#### Option 1: Shared Hosting (cPanel)
1. Upload `api/` folder to public_html
2. Create MySQL database via cPanel
3. Import schema.sql
4. Update config.php with database credentials
5. Ensure PHP 8+ is enabled

#### Option 2: VPS (Ubuntu)
```bash
# Install LAMP stack
sudo apt update
sudo apt install apache2 mysql-server php8.1 php8.1-mysql

# Configure Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Deploy code
git clone https://github.com/vibeai-casv/playqzv4.git /var/www/html/playqz
cd /var/www/html/playqz/api

# Setup database
mysql -u root -p < schema.sql

# Set permissions
sudo chown -R www-data:www-data /var/www/html/playqz
sudo chmod -R 755 /var/www/html/playqz
```

#### Option 3: Docker
```dockerfile
# Dockerfile for backend
FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY api/ /var/www/html/
EXPOSE 80
```

### Database Deployment

1. **Create Production Database**
2. **Import Schema**: `mysql -u user -p database < api/schema.sql`
3. **Create Admin User**: Run SQL to insert admin user
4. **Configure Backups**: Set up automated MySQL backups

---

## 🐛 Known Issues & Future Enhancements

### Known Issues

1. **Canvas Confetti**: May not install due to network issues
   - Workaround: Manual installation or CDN fallback

2. **File Upload Size**: Limited by PHP configuration
   - Solution: Adjust `upload_max_filesize` in php.ini

3. **Session Cleanup**: No automatic cleanup of expired sessions
   - Solution: Implement cron job for cleanup

4. **Real-time Updates**: No WebSocket support
   - Current: Polling-based updates
   - Future: Implement WebSocket server

### Future Enhancements

#### Short-term (v4.1)
- [ ] Email notifications (quiz completion, results)
- [ ] Password reset functionality
- [ ] User profile avatars
- [ ] Export quiz results to PDF
- [ ] Question import from CSV/Excel

#### Medium-term (v4.2)
- [ ] Leaderboards and achievements
- [ ] Quiz sharing and public quizzes
- [ ] Question difficulty auto-adjustment
- [ ] Advanced analytics (heatmaps, trends)
- [ ] Multi-language support

#### Long-term (v5.0)
- [ ] AI question generation (GPT integration)
- [ ] Live multiplayer quizzes
- [ ] Video/audio question support
- [ ] Mobile apps (React Native)
- [ ] API rate limiting and caching
- [ ] WebSocket real-time updates
- [ ] Advanced role-based permissions
- [ ] Question versioning and history

### Performance Optimizations

1. **Database**
   - Add more indexes for frequently queried columns
   - Implement query caching
   - Consider read replicas for scaling

2. **API**
   - Implement response caching (Redis)
   - Add API rate limiting
   - Optimize N+1 queries

3. **Frontend**
   - Implement code splitting
   - Add service worker for offline support
   - Optimize bundle size
   - Lazy load images

---

## 📊 Statistics

### Project Metrics
- **Total Files**: 138
- **Lines of Code**: ~26,653
- **Components**: 40+
- **API Endpoints**: 25+
- **Database Tables**: 8
- **Supported Question Types**: 4

### Technology Breakdown
- **Frontend**: React, TypeScript, Tailwind CSS
- **Backend**: PHP 8, MySQL
- **State Management**: Zustand
- **Form Handling**: React Hook Form + Zod
- **Charts**: Recharts
- **Icons**: Lucide React

---

## 🤝 Contributing

### Development Workflow
1. Create feature branch from `main`
2. Make changes with descriptive commits
3. Test thoroughly (manual + automated)
4. Submit pull request
5. Code review and merge

### Coding Standards
- **PHP**: PSR-12 coding standard
- **TypeScript**: ESLint + Prettier
- **Commits**: Conventional commits format
- **Documentation**: Update relevant docs

---

## 📝 License

This project is proprietary software owned by VibeAI CASV.

---

## 📞 Support

For issues, questions, or contributions:
- **Repository**: https://github.com/vibeai-casv/playqzv4
- **Issues**: https://github.com/vibeai-casv/playqzv4/issues

---

**Last Updated**: November 29, 2025  
**Maintained by**: VibeAI CASV Team  
**Version**: 4.0.0
