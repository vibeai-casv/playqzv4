<?php
// Production Database Configuration
// IMPORTANT: Update these values with your actual hosting credentials

define('DB_HOST', 'localhost');
define('DB_NAME', 'aiqz'); // Local XAMPP database
define('DB_USER', 'root'); // Default XAMPP user
define('DB_PASS', ''); // Default XAMPP password (empty)
define('DB_CHARSET', 'utf8mb4');

// CORS configuration - Allow localhost for development
define('ALLOWED_ORIGIN', '*'); // Allow all origins in development

// Development settings
define('ENVIRONMENT', 'development');
define('DEBUG_MODE', true);
define('LOG_ERRORS', true);

// Session configuration
define('SESSION_LIFETIME', 86400); // 24 hours
define('SESSION_SECURE', false); // Don't require HTTPS in development
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Lax'); // Less strict for development

// File upload settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_UPLOAD_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// AI Configuration
define('AI_PROVIDER', 'gemini'); // 'gemini' or 'openrouter'
define('AI_API_KEY', 'AIzaSyC0faiIeIWEqYu1Y_RP90n2ydiW3Zy9iNo'); // Put your API key here
define('AI_MODEL', 'gemini-1.5-flash'); // Model to use
?>
