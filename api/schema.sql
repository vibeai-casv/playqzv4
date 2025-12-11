-- MySQL Schema for AI Quiz App
-- Converted from Supabase PostgreSQL schema

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users Table (Replacing Supabase Auth)
CREATE TABLE IF NOT EXISTS `users` (
    `id` CHAR(36) PRIMARY KEY,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.5 Sessions Table
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` CHAR(36) PRIMARY KEY,
    `user_id` CHAR(36) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Profiles Table
CREATE TABLE IF NOT EXISTS `profiles` (
    `id` CHAR(36) PRIMARY KEY,
    `email` VARCHAR(255),
    `phone` VARCHAR(20),
    `name` VARCHAR(255) NOT NULL,
    `institution` VARCHAR(255),
    `district` VARCHAR(100),
    `course_of_study` VARCHAR(255),
    `class_level` VARCHAR(50),
    `category` ENUM('student', 'professional', 'educator', 'hobbyist') DEFAULT 'student',
    `role` ENUM('user', 'admin') DEFAULT 'user' NOT NULL,
    `bio` TEXT,
    `avatar` TEXT,
    `preferences` JSON,
    `stats` JSON,
    `disabled` TINYINT(1) DEFAULT 0,
    `disabled_reason` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login_at` TIMESTAMP NULL,
    FOREIGN KEY (`id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Media Library Table
CREATE TABLE IF NOT EXISTS `media_library` (
    `id` CHAR(36) PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) NOT NULL,
    `url` TEXT NOT NULL,
    `type` ENUM('logo', 'personality', 'question_image', 'avatar') NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `size_bytes` INT NOT NULL,
    `description` TEXT,
    `metadata` JSON,
    `uploaded_by` CHAR(36) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`uploaded_by`) REFERENCES `profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Questions Table
CREATE TABLE IF NOT EXISTS `questions` (
    `id` CHAR(36) PRIMARY KEY,
    `question_text` TEXT NOT NULL,
    `question_type` ENUM('text_mcq', 'image_identify_logo', 'image_identify_person', 'true_false', 'short_answer') NOT NULL,
    `options` JSON,
    `correct_answer` TEXT NOT NULL,
    `image_url` TEXT,
    `media_id` CHAR(36) NULL,
    `explanation` TEXT,
    `hint` TEXT,
    `difficulty` ENUM('easy', 'medium', 'hard') NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `subcategory` VARCHAR(100),
    `tags` JSON,
    `points` INT DEFAULT 10 NOT NULL,
    `time_limit_seconds` INT DEFAULT 60,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_verified` TINYINT(1) DEFAULT 0,
    `usage_count` INT DEFAULT 0,
    `correct_count` INT DEFAULT 0,
    `ai_generated` TINYINT(1) DEFAULT 0,
    `ai_prompt` TEXT,
    `is_demo` TINYINT(1) DEFAULT 0,
    `created_by` CHAR(36) NOT NULL,
    `verified_by` CHAR(36) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`media_id`) REFERENCES `media_library`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`) REFERENCES `profiles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`verified_by`) REFERENCES `profiles`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Quiz Attempts Table
CREATE TABLE IF NOT EXISTS `quiz_attempts` (
    `id` CHAR(36) PRIMARY KEY,
    `user_id` CHAR(36) NOT NULL,
    `config` JSON NOT NULL,
    `quiz_hash` VARCHAR(255),
    `question_ids` JSON NOT NULL,
    `status` ENUM('in_progress', 'completed', 'abandoned', 'expired') DEFAULT 'in_progress' NOT NULL,
    `score` DECIMAL(5,2),
    `correct_answers` INT DEFAULT 0,
    `total_questions` INT NOT NULL,
    `time_spent_seconds` INT,
    `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completed_at` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NULL,
    `accuracy_rate` DECIMAL(5,2),
    `average_time_per_question` DECIMAL(6,2),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `profiles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Quiz Responses Table
CREATE TABLE IF NOT EXISTS `quiz_responses` (
    `id` CHAR(36) PRIMARY KEY,
    `attempt_id` CHAR(36) NOT NULL,
    `question_id` CHAR(36) NOT NULL,
    `user_id` CHAR(36) NOT NULL,
    `user_answer` TEXT NOT NULL,
    `is_correct` TINYINT(1) NOT NULL,
    `time_spent_seconds` INT,
    `answered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `points_awarded` INT DEFAULT 0,
    `max_points` INT,
    `question_position` INT,
    `skipped` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `profiles`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_attempt_question` (`attempt_id`, `question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. User Activity Logs Table
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
    `id` CHAR(36) PRIMARY KEY,
    `user_id` CHAR(36) NULL,
    `activity_type` ENUM(
        'login', 'logout', 'signup', 'profile_updated', 'quiz_started', 
        'quiz_completed', 'quiz_abandoned', 'password_changed', 'email_changed', 
        'account_disabled', 'account_enabled', 'question_created', 'question_updated', 
        'media_uploaded', 'achievement_earned', 'settings_changed'
    ) NOT NULL,
    `description` TEXT NOT NULL,
    `related_entity_type` VARCHAR(50),
    `related_entity_id` CHAR(36),
    `metadata` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `device_info` JSON,
    `country_code` VARCHAR(10),
    `city` VARCHAR(100),
    `success` TINYINT(1) DEFAULT 1,
    `error_message` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `profiles`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
