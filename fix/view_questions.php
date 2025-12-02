<?php
/**
 * Raw Question Viewer
 * Displays all questions from the database with pagination
 */

header('Content-Type: text/html; charset=utf-8');

// Try to load configuration
$configPath = dirname(__DIR__) . '/api/config.php';
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    die("Error: ../api/config.php not found.");
}

// Database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Pagination settings
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total count
$stmt = $pdo->query("SELECT COUNT(*) FROM questions");
$total_questions = $stmt->fetchColumn();
$total_pages = ceil($total_questions / $limit);

// Fetch questions
$stmt = $pdo->prepare("SELECT * FROM questions ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$questions = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Raw Question Viewer - Page <?php echo $page; ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; margin: 20px; background: #f3f4f6; color: #1f2937; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { color: #111827; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .badge-easy { background: #d1fae5; color: #065f46; }
        .badge-medium { background: #fef3c7; color: #92400e; }
        .badge-hard { background: #fee2e2; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th { background: #f9fafb; font-weight: 600; color: #374151; position: sticky; top: 0; }
        tr:hover { background: #f9fafb; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; gap: 5px; }
        .pagination a, .pagination span { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 4px; text-decoration: none; color: #374151; background: white; }
        .pagination a:hover { background: #f3f4f6; }
        .pagination .active { background: #2563eb; color: white; border-color: #2563eb; }
        .pagination .disabled { color: #9ca3af; pointer-events: none; background: #f3f4f6; }
        .meta { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .id-col { width: 50px; color: #6b7280; }
        .type-col { width: 100px; }
        .cat-col { width: 120px; }
        .diff-col { width: 80px; }
        .actions { margin-bottom: 15px; }
        .btn { display: inline-block; padding: 8px 16px; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
<div class="container">
    <h1>
        <span>Question Database Viewer</span>
        <span style="font-size: 16px; font-weight: normal; color: #6b7280;">Total: <?php echo number_format($total_questions); ?> questions</span>
    </h1>

    <div class="actions">
        <a href="../client/" class="btn">← Back to App</a>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th class="id-col">ID</th>
                    <th>Question Text</th>
                    <th>Correct Answer</th>
                    <th class="type-col">Type</th>
                    <th class="cat-col">Category</th>
                    <th class="diff-col">Difficulty</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($questions) > 0): ?>
                    <?php foreach ($questions as $q): ?>
                        <tr>
                            <td class="id-col">#<?php echo $q['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars(mb_strimwidth($q['question_text'], 0, 100, "...")); ?></strong>
                                <?php if (!empty($q['image_url'])): ?>
                                    <div class="meta">📷 Has Image</div>
                                <?php endif; ?>
                                <div class="meta">
                                    Options: <?php 
                                        $opts = json_decode($q['options'], true);
                                        echo $opts ? htmlspecialchars(implode(', ', array_slice($opts, 0, 4))) . (count($opts) > 4 ? '...' : '') : 'Invalid JSON';
                                    ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($q['correct_answer']); ?></td>
                            <td class="type-col"><?php echo htmlspecialchars(str_replace('_', ' ', $q['question_type'])); ?></td>
                            <td class="cat-col"><?php echo htmlspecialchars($q['category']); ?></td>
                            <td class="diff-col">
                                <span class="badge badge-<?php echo $q['difficulty']; ?>">
                                    <?php echo htmlspecialchars($q['difficulty']); ?>
                                </span>
                            </td>
                            <td style="white-space: nowrap; font-size: 12px;">
                                <?php echo $q['created_at']; ?>
                                <?php if ($q['ai_generated']): ?>
                                    <div style="color: #7c3aed; font-weight: bold;">✨ AI</div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">No questions found in database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1">« First</a>
            <a href="?page=<?php echo $page - 1; ?>">‹ Prev</a>
        <?php else: ?>
            <span class="disabled">« First</span>
            <span class="disabled">‹ Prev</span>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end = min($total_pages, $page + 2);
        
        if ($start > 1) echo '<span class="disabled">...</span>';
        
        for ($i = $start; $i <= $end; $i++):
        ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($end < $total_pages) echo '<span class="disabled">...</span>'; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next ›</a>
            <a href="?page=<?php echo $total_pages; ?>">Last »</a>
        <?php else: ?>
            <span class="disabled">Next ›</span>
            <span class="disabled">Last »</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
