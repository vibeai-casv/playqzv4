<?php
// Production Database Configuration
// IMPORTANT: Update these values with your actual hosting credentials

define('DB_HOST', 'localhost'); // Usually 'localhost' for shared hosting
define('DB_NAME', 'aiqz_production'); // Your database name from hosting
define('DB_USER', 'aiqz_user'); // Your database user from hosting
define('DB_PASS', 'YOUR_PASSWORD_HERE'); // CHANGE THIS!
define('DB_CHARSET', 'utf8mb4');

// CORS configuration
define('ALLOWED_ORIGIN', 'https://aiquiz.vibeai.cv');

// Production settings
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);
define('LOG_ERRORS', true);
?>
