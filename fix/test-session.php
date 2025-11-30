<?php
/**
 * Session and Authentication Diagnostic
 * Checks if user is logged in and has admin access
 */

header('Content-Type: application/json');

// Start session
session_start();

$result = array(
    'timestamp' => date('Y-m-d H:i:s'),
    'test_name' => 'Session & Auth Diagnostic'
);

// Session info
$result['session'] = array(
    'session_id' => session_id(),
    'session_status' => session_status(),
    'session_started' => session_status() === PHP_SESSION_ACTIVE,
    'session_save_path' => session_save_path(),
    'session_name' => session_name()
);

// Check session data
$result['session_data'] = array(
    'has_user_id' => isset($_SESSION['user_id']),
    'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
    'all_session_keys' => array_keys($_SESSION)
);

// Check cookies
$result['cookies'] = array(
    'has_session_cookie' => isset($_COOKIE[session_name()]),
    'session_cookie_value' => isset($_COOKIE[session_name()]) ? substr($_COOKIE[session_name()], 0, 20) . '...' : null,
    'all_cookies' => array_keys($_COOKIE)
);

// Try to load database and check user
try {
    $apiPath = dirname(dirname(__FILE__)) . '/api';
    
    if (file_exists($apiPath . '/config.php')) {
        require_once $apiPath . '/config.php';
        require_once $apiPath . '/db.php';
        
        $result['db_loaded'] = true;
        
        // If user_id exists in session, get user data
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("
                SELECT u.id, u.email, u.full_name, p.role, p.mobile, p.category
                FROM users u
                LEFT JOIN profiles p ON u.id = p.id
                WHERE u.id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $result['user_found'] = true;
                $result['user_data'] = array(
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'is_admin' => $user['role'] === 'admin'
                );
            } else {
                $result['user_found'] = false;
                $result['error'] = 'User ID in session but not found in database';
            }
        } else {
            $result['user_found'] = false;
            $result['error'] = 'No user_id in session - not logged in';
        }
        
        // Check if there are any admin users
        $stmt = $pdo->query("SELECT COUNT(*) FROM profiles WHERE role = 'admin'");
        $result['admin_count'] = $stmt->fetchColumn();
        
        // Get all users
        $stmt = $pdo->query("SELECT u.email, p.role FROM users u LEFT JOIN profiles p ON u.id = p.id LIMIT 10");
        $result['sample_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } else {
        $result['db_loaded'] = false;
        $result['error'] = 'Config file not found';
    }
    
} catch (Exception $e) {
    $result['error'] = $e->getMessage();
}

// Authentication status
$result['auth_status'] = array(
    'logged_in' => isset($_SESSION['user_id']),
    'is_admin' => isset($user) && $user && $user['role'] === 'admin',
    'can_import' => isset($user) && $user && $user['role'] === 'admin'
);

// Recommendations
$result['recommendations'] = array();

if (!isset($_SESSION['user_id'])) {
    $result['recommendations'][] = 'You are not logged in. Please login at https://aiquiz.vibeai.cv';
}

if (isset($_SESSION['user_id']) && (!isset($user) || !$user)) {
    $result['recommendations'][] = 'Session exists but user not found in database. Try logging out and logging in again.';
}

if (isset($user) && $user && $user['role'] !== 'admin') {
    $result['recommendations'][] = 'You are logged in but not as admin. Current role: ' . $user['role'];
}

if (isset($user) && $user && $user['role'] === 'admin') {
    $result['recommendations'][] = 'You are logged in as admin! Import should work.';
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
