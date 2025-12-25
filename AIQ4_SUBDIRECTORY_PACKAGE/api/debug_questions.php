<?php
require_once 'config.php';
require_once 'db.php';
require_once 'utils.php';

cors();

try {
    $stmt = $pdo->query("
        SELECT 
            question_type, 
            is_active, 
            COUNT(*) as count 
        FROM questions 
        GROUP BY question_type, is_active
        ORDER BY question_type, is_active
    ");
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h1>Question Status Diagnostic</h1>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #eee;'><th>Type</th><th>Status</th><th>Count</th></tr>";
    
    foreach ($stats as $row) {
        $status = $row['is_active'] ? '<span style="color: green; font-weight: bold;">Active</span>' : '<span style="color: orange;">Draft/Inactive</span>';
        echo "<tr>";
        echo "<td>" . $row['question_type'] . "</td>";
        echo "<td>" . $status . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Legend:</h3>";
    echo "<ul>";
    echo "<li><b>Active:</b> Question can be used in Quizzes.</li>";
    echo "<li><b>Draft/Inactive:</b> Question is hidden from Quizzes.</li>";
    echo "</ul>";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
