<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);

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
$media = $stmt->fetchAll();

// Count total
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

jsonResponse(['media' => $media, 'total' => $total]);
?>
