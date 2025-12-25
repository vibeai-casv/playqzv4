<?php
// Production Database Configuration
// IMPORTANT: Update these values with your actual production credentials
// DO NOT commit this file to version control!

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
define('ERROR_LOG_PATH', '/var/log/aiquiz/errors.log');

// Session configuration
define('SESSION_LIFETIME', 86400); // 24 hours
define('SESSION_SECURE', true); // Require HTTPS
define('SESSION_HTTPONLY', true);
define('SESSION_SAMESITE', 'Strict');

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File upload settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_UPLOAD_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('UPLOAD_PATH', '/var/www/aiquiz.vibeai.cv/uploads');

// AI Configuration
define('AI_PROVIDER', 'gemini');
define('AI_API_KEY', 'AIzaSyBAEQMkw01uCSQ1XFin3H4_D6DJ7HeHM7I');
define('AI_MODEL', 'gemini-2.0-flash');

?>
