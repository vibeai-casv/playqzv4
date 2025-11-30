<?php
// Production Database Configuration
// IMPORTANT: Update these values with your actual hosting credentials

define('DB_HOST', 'localhost'); // Usually 'localhost' for shared hosting
define('DB_NAME', 'aiqz'); // Your database name from hosting
define('DB_USER', 'aiqz'); // Your database user from hosting
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
?>
