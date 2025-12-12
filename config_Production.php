<?php
// Production Database Configuration
// IMPORTANT: Update these values with your actual hosting credentials

define('DB_HOST', 'localhost'); // Your MySQL host
define('DB_NAME', 'rcdzrtua_aiquiz'); // Production database name
define('DB_USER', 'rcdzrtua_aiquiz'); // Production database user
define('DB_PASS', 'Aiquiz@mpm'); // CHANGE THIS!
define('DB_CHARSET', 'utf8mb4');

// CORS configuration
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');

// Production settings
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);
define('LOG_ERRORS', true);

// Session configuration
define('SESSION_LIFETIME', 86400); // 24 hours
define('SESSION_SECURE', true); // Require HTTPS
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');

// File upload settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_UPLOAD_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// AI Configuration
define('AI_PROVIDER', 'gemini');
define('AI_API_KEY', 'AIzaSyBAEQMkw01uCSQ1XFin3H4_D6DJ7HeHM7I');
define('AI_MODEL', 'gemini-2.0-flash');

?>
