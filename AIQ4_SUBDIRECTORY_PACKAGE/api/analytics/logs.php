<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

// Check if admin
if ($session['role'] !== 'admin') {
    jsonResponse(['error' => 'Forbidden'], 403);
}

$type = $_GET['type'] ?? null;
$search = $_GET['search'] ?? null;
$startDate = $_GET['startDate'] ?? null;
$endDate = $_GET['endDate'] ?? null;
$limit = $_GET['limit'] ?? 20;
$offset = $_GET['offset'] ?? 0;

$sql = "SELECT al.*, p.name as user_name, p.email as user_email 
        FROM user_activity_logs al 
        LEFT JOIN profiles p ON al.user_id = p.id 
        WHERE 1=1";
$params = [];

if ($type) {
    $sql .= " AND al.activity_type = ?";
    $params[] = $type;
}

if ($search) {
    $sql .= " AND (al.description LIKE ? OR al.metadata LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($startDate) {
    // Handle ISO date string from frontend
    $start = date('Y-m-d H:i:s', strtotime($startDate));
    $sql .= " AND al.created_at >= ?";
    $params[] = $start;
}

if ($endDate) {
    // Handle ISO date string from frontend and set to end of day
    $end = date('Y-m-d 23:59:59', strtotime($endDate));
    $sql .= " AND al.created_at <= ?";
    $params[] = $end;
}

$sql .= " ORDER BY al.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    // Parse JSON
    foreach ($logs as &$log) {
        $log['metadata'] = json_decode($log['metadata']);
        $log['profiles'] = [
            'name' => $log['user_name'],
            'email' => $log['user_email']
        ];
        unset($log['user_name']);
        unset($log['user_email']);
    }

    // Count total
    $sqlCount = "SELECT COUNT(*) FROM user_activity_logs al WHERE 1=1";
    $paramsCount = [];
    
    if ($type) {
        $sqlCount .= " AND al.activity_type = ?";
        $paramsCount[] = $type;
    }
    if ($search) {
        $sqlCount .= " AND (al.description LIKE ? OR al.metadata LIKE ?)";
        $paramsCount[] = "%$search%";
        $paramsCount[] = "%$search%";
    }
    if ($startDate) {
        $start = date('Y-m-d H:i:s', strtotime($startDate));
        $sqlCount .= " AND al.created_at >= ?";
        $paramsCount[] = $start;
    }
    if ($endDate) {
        $end = date('Y-m-d 23:59:59', strtotime($endDate));
        $sqlCount .= " AND al.created_at <= ?";
        $paramsCount[] = $end;
    }

    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($paramsCount);
    $total = $stmtCount->fetchColumn();

    jsonResponse(['logs' => $logs, 'total' => $total]);

} catch (PDOException $e) {
    jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
