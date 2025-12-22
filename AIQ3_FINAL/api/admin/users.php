<?php
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

// Allow both admin and super_admin
if (!in_array($session['role'], ['admin', 'super_admin'])) {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

try {
    // Base query conditions
    $whereClause = "";
    $params = [];

    // If not super admin, hide the super admin account
    if ($session['role'] !== 'super_admin') {
        $whereClause = "WHERE role != 'super_admin' AND email != 'vibeaicasv@gmail.com'";
    } else {
        $whereClause = "WHERE 1=1"; // Show everyone to super admin
    }

    // Get total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM profiles $whereClause");
    $countStmt->execute();
    $total = $countStmt->fetchColumn();

    // Get users
    $stmt = $pdo->prepare("
        SELECT id, email, name, role, created_at, updated_at, 
               institution, category, bio, avatar, disabled, district, phone
        FROM profiles 
        $whereClause
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset
    ");
    
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $users = $stmt->fetchAll();
    
    // Format users to match frontend User interface
    $formattedUsers = array_map(function($user) {
        return [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role'],
            'institution' => $user['institution'],
            'category' => $user['category'],
            'bio' => $user['bio'],
            'avatar' => $user['avatar'],
            'disabled' => (bool)$user['disabled'],
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at']
        ];
    }, $users);

    jsonResponse([
        'users' => $formattedUsers,
        'total' => $total,
        'page' => $page,
        'limit' => $limit
    ]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
