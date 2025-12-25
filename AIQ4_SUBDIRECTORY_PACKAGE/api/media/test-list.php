<?php
// Simple test for media list endpoint
// Upload to: /var/www/aiquiz.vibeai.cv/aiq3/api/media/test-list.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$response = [
    'test' => 'Media List Test',
    'timestamp' => date('Y-m-d H:i:s'),
    'steps' => []
];

// Step 1: Try to load config
try {
    require_once '../config.php';
    $response['steps']['config'] = 'OK';
} catch (Exception $e) {
    $response['steps']['config'] = 'ERROR: ' . $e->getMessage();
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// Step 2: Try to load db
try {
    require_once '../db.php';
    $response['steps']['database'] = 'OK';
} catch (Exception $e) {
    $response['steps']['database'] = 'ERROR: ' . $e->getMessage();
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// Step 3: Try to load utils (for authentication)
try {
    require_once '../utils.php';
    $response['steps']['utils'] = 'OK';
} catch (Exception $e) {
    $response['steps']['utils'] = 'ERROR: ' . $e->getMessage();
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// Step 4: Check authentication header
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
$response['steps']['auth_header'] = $authHeader ? 'Present' : 'Missing';
$response['auth_header_value'] = $authHeader ? substr($authHeader, 0, 20) . '...' : 'none';

// Step 5: Try to authenticate manually (without exiting)
$authenticated = false;
$authUserId = null;

if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
    
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, u.email, p.role, p.id as profile_id 
            FROM sessions s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN profiles p ON u.id = p.id
            WHERE s.token = ? AND s.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($session) {
            $authenticated = true;
            $authUserId = $session['user_id'];
            $response['steps']['authentication'] = 'OK - User: ' . $session['email'] . ' (Role: ' . $session['role'] . ')';
            $response['session_info'] = [
                'user_id' => $session['user_id'],
                'email' => $session['email'],
                'role' => $session['role'],
                'expires_at' => $session['expires_at']
            ];
        } else {
            $response['steps']['authentication'] = 'FAILED - Token not found or expired in database';
        }
    } catch (Exception $e) {
        $response['steps']['authentication'] = 'ERROR: ' . $e->getMessage();
    }
} else {
    $response['steps']['authentication'] = 'FAILED - No Bearer token in request';
}

$response['authenticated'] = $authenticated;

// Step 6: Try to query media (without auth requirement)
try {
    $sql = "SELECT * FROM media_library WHERE is_active = 1 ORDER BY created_at DESC LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response['steps']['query'] = 'OK - Found ' . count($media) . ' items';
    $response['sample_media'] = array_slice($media, 0, 3); // First 3 items as sample
} catch (Exception $e) {
    $response['steps']['query'] = 'ERROR: ' . $e->getMessage();
}

// Step 7: What would the actual API return?
try {
    if ($response['authenticated']) {
        $type = $_GET['type'] ?? null;
        $limit = $_GET['limit'] ?? 50;
        $offset = $_GET['offset'] ?? 0;
        
        $sql = "SELECT * FROM media_library WHERE is_active = 1";
        $params = [];
        
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $sqlCount = "SELECT COUNT(*) FROM media_library WHERE is_active = 1";
        if ($type) {
            $sqlCount .= " AND type = ?";
        }
        $stmtCount = $pdo->prepare($sqlCount);
        if ($type) {
            $stmtCount->execute([$type]);
        } else {
            $stmtCount->execute();
        }
        $total = $stmtCount->fetchColumn();
        
        $response['steps']['api_simulation'] = 'OK';
        $response['api_result'] = [
            'media' => $media,
            'total' => $total
        ];
    } else {
        $response['steps']['api_simulation'] = 'SKIPPED - Not authenticated';
    }
} catch (Exception $e) {
    $response['steps']['api_simulation'] = 'ERROR: ' . $e->getMessage();
}

$response['summary'] = $response['authenticated'] ? 
    'Authentication OK - Media API should work' : 
    'Authentication FAILED - This is likely your issue!';

echo json_encode($response, JSON_PRETTY_PRINT);
?>
