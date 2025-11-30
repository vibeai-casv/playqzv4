<?php
/**
 * List Questions Diagnostic Tool
 * Lists the latest 100 questions and summary statistics.
 */

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Helper to find file
function findFile($filename) {
    $paths = [
        dirname(__FILE__) . '/../' . $filename, // Parent directory (standard)
        dirname(__FILE__) . '/' . $filename,    // Current directory (if moved)
        $_SERVER['DOCUMENT_ROOT'] . '/' . $filename, // Web root
        $_SERVER['DOCUMENT_ROOT'] . '/api/' . $filename // API folder?
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    return false;
}

$configPath = findFile('config.php');
$dbPath = findFile('db.php');

if (!$configPath || !$dbPath) {
    echo "<h1>Error: Critical Files Not Found</h1>";
    echo "<p>Could not locate <code>config.php</code> or <code>db.php</code>.</p>";
    echo "<p>Searched in:</p><ul>";
    echo "<li>" . dirname(__FILE__) . '/../' . "</li>";
    echo "<li>" . dirname(__FILE__) . '/' . "</li>";
    echo "<li>" . $_SERVER['DOCUMENT_ROOT'] . '/' . "</li>";
    echo "</ul>";
    echo "<p>Current Script Path: " . __FILE__ . "</p>";
    exit;
}

require_once $configPath;
require_once $dbPath;

echo "<!DOCTYPE html><html><head><title>Questions List</title>";
echo "<style>
    body { font-family: sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8em; color: white; }
    .easy { background-color: #28a745; }
    .medium { background-color: #ffc107; color: black; }
    .hard { background-color: #dc3545; }
    .summary { display: flex; gap: 20px; margin-bottom: 20px; }
    .card { border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #f9f9f9; }
</style></head><body>";

echo "<h1>Questions Diagnostic</h1>";
echo "<p style='color: gray; font-size: 0.8em;'>Loaded config from: $configPath</p>";

try {
    // 1. Get Summary Stats
    $statsQuery = "SELECT category, COUNT(*) as count FROM questions GROUP BY category";
    $statsStmt = $pdo->query($statsQuery);
    $stats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

    $totalQuery = "SELECT COUNT(*) FROM questions";
    $total = $pdo->query($totalQuery)->fetchColumn();

    echo "<div class='summary'>";
    echo "<div class='card'><h3>Total Questions</h3><p style='font-size: 2em; margin: 0;'>$total</p></div>";
    
    echo "<div class='card'><h3>By Category</h3><ul>";
    foreach ($stats as $stat) {
        echo "<li><strong>" . htmlspecialchars($stat['category']) . ":</strong> " . $stat['count'] . "</li>";
    }
    echo "</ul></div>";
    echo "</div>";

    // 2. Get Latest Questions
    $sql = "SELECT id, question_text, category, difficulty, question_type, is_active, created_at FROM questions ORDER BY created_at DESC LIMIT 100";
    $stmt = $pdo->query($sql);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Latest 100 Questions</h2>";
    echo "<table>";
    echo "<thead><tr><th>ID</th><th>Question</th><th>Category</th><th>Type</th><th>Difficulty</th><th>Status</th><th>Created</th></tr></thead>";
    echo "<tbody>";

    if (count($questions) > 0) {
        foreach ($questions as $q) {
            $diffClass = strtolower($q['difficulty']);
            $status = $q['is_active'] ? 'Active' : 'Inactive';
            echo "<tr>";
            echo "<td>" . substr($q['id'], 0, 8) . "...</td>";
            echo "<td>" . htmlspecialchars(substr($q['question_text'], 0, 100)) . (strlen($q['question_text']) > 100 ? '...' : '') . "</td>";
            echo "<td>" . htmlspecialchars($q['category']) . "</td>";
            echo "<td>" . htmlspecialchars($q['question_type']) . "</td>";
            echo "<td><span class='badge $diffClass'>" . htmlspecialchars($q['difficulty']) . "</span></td>";
            echo "<td>" . $status . "</td>";
            echo "<td>" . $q['created_at'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No questions found in the database.</td></tr>";
    }

    echo "</tbody></table>";

} catch (PDOException $e) {
    echo "<div style='color: red; padding: 20px; border: 1px solid red;'>";
    echo "<h3>Database Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</body></html>";
?>
