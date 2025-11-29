<?php
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

if ($session['role'] !== 'admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

try {
    // Get total count
    $countStmt = $pdo->query("SELECT COUNT(*) FROM profiles");
    $total = $countStmt->fetchColumn();

    // Get users
    $stmt = $pdo->prepare("
        SELECT id, email, name, role, created_at, updated_at, 
               institution, category, bio, avatar, disabled
        FROM profiles 
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
