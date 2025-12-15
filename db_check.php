<?php
/**
 * Simple Database Check - No Login Required
 * 
 * Shows all users and their profile status
 * 
 * Upload to: /public_html/aiq3/api/db_check.php
 * Visit: https://aiquiz.vibeai.cv/aiq3/api/db_check.php
 * DELETE after checking!
 */

require_once 'config.php';
require_once 'db.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .good { color: green; font-weight: bold; }
        .bad { color: red; font-weight: bold; }
        .warning { background: #fff3cd; padding: 15px; border-left: 4px solid orange; margin: 20px 0; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid green; margin: 20px 0; }
        .danger { background: #f8d7da; padding: 15px; border-left: 4px solid red; margin: 20px 0; }
        code { background: #f5f5f5; padding: 10px; display: block; margin: 10px 0; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🔍 AIQ3 Database Check</h1>

<?php
try {
    // Check all users and their profiles
    echo "<h2>Users and Profile Status</h2>";
    echo "<table>";
    echo "<tr><th>Email</th><th>User ID</th><th>Has Profile?</th><th>Profile Role</th></tr>";
    
    $stmt = $pdo->query("
        SELECT 
            u.id as user_id,
            u.email,
            p.id as profile_id,
            p.role,
            CASE WHEN p.id IS NULL THEN 'NO' ELSE 'YES' END as has_profile
        FROM users u
        LEFT JOIN profiles p ON u.id = p.id
        ORDER BY u.created_at DESC
    ");
    
    $users = $stmt->fetchAll();
    $missing_profiles = [];
    
    foreach ($users as $row) {
        $status_class = $row['has_profile'] === 'YES' ? 'good' : 'bad';
        $status_text = $row['has_profile'] === 'YES' ? '✅ YES' : '❌ NO';
        
        echo "<tr>";
        echo "<td><strong>{$row['email']}</strong></td>";
        echo "<td style='font-size: 10px;'>{$row['user_id']}</td>";
        echo "<td class='$status_class'>$status_text</td>";
        echo "<td>" . ($row['role'] ?? 'N/A') . "</td>";
        echo "</tr>";
        
        if ($row['has_profile'] === 'NO') {
            $missing_profiles[] = $row;
        }
    }
    echo "</table>";
    
    // Show summary
    $total = count($users);
    $missing_count = count($missing_profiles);
    
    echo "<h2>Summary</h2>";
    echo "<p>Total users: <strong>$total</strong></p>";
    echo "<p>Users with profiles: <strong>" . ($total - $missing_count) . "</strong></p>";
    echo "<p>Users WITHOUT profiles: <strong class='bad'>$missing_count</strong></p>";
    
    // Show fix instructions if needed
    if ($missing_count > 0) {
        echo "<div class='danger'>";
        echo "<h3>⚠️ PROBLEM FOUND!</h3>";
        echo "<p><strong>$missing_count user(s) are missing profiles.</strong></p>";
        echo "<p>This is why question import fails!</p>";
        echo "</div>";
        
        echo "<h3>🔧 How to Fix:</h3>";
        echo "<p>Run this SQL in <strong>phpMyAdmin</strong> for each missing profile:</p>";
        
        foreach ($missing_profiles as $user) {
            echo "<div class='warning'>";
            echo "<p><strong>For user: {$user['email']}</strong></p>";
            echo "<code>";
            echo "INSERT INTO profiles (id, email, name, role, created_at, updated_at)<br>";
            echo "VALUES ('{$user['user_id']}', '{$user['email']}', 'Admin User', 'admin', NOW(), NOW());";
            echo "</code>";
            echo "</div>";
        }
        
        echo "<h4>Steps:</h4>";
        echo "<ol>";
        echo "<li>Login to cPanel</li>";
        echo "<li>Open phpMyAdmin</li>";
        echo "<li>Select database: <strong>rcdzrtua_aiquiz</strong></li>";
        echo "<li>Click 'SQL' tab</li>";
        echo "<li>Copy-paste the SQL above</li>";
        echo "<li>Click 'Go'</li>";
        echo "<li>Try importing questions again</li>";
        echo "</ol>";
    } else {
        echo "<div class='success'>";
        echo "<h3>✅ ALL GOOD!</h3>";
        echo "<p>All users have profiles. Import should work!</p>";
        echo "<p><strong>If import still fails:</strong></p>";
        echo "<ul>";
        echo "<li>Make sure you're logged in as one of the users above</li>";
        echo "<li>Logout and login again</li>";
        echo "<li>Clear browser cache</li>";
        echo "<li>Check JSON format is correct (question_text, question_type)</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    // Check question types
    echo "<h2>Question Types in Database</h2>";
    $stmt = $pdo->query("
        SELECT question_type, COUNT(*) as count 
        FROM questions 
        GROUP BY question_type
        ORDER BY count DESC
    ");
    
    $types = $stmt->fetchAll();
    
    if (count($types) > 0) {
        echo "<table>";
        echo "<tr><th>Question Type</th><th>Count</th></tr>";
        foreach ($types as $type) {
            echo "<tr>";
            echo "<td>{$type['question_type']}</td>";
            echo "<td><strong>{$type['count']}</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No questions in database yet.</p>";
    }
    
} catch (PDOException $e) {
    echo "<div class='danger'>";
    echo "<h3>❌ Database Connection Error</h3>";
    echo "<p>{$e->getMessage()}</p>";
    echo "<p>Check your config.php file has correct database credentials.</p>";
    echo "</div>";
}
?>

    <div class="warning">
        <h3>🚨 SECURITY WARNING</h3>
        <p><strong>Delete this file immediately after checking:</strong></p>
        <code>rm /public_html/aiq3/api/db_check.php</code>
        <p>Or delete via cPanel File Manager</p>
    </div>

</body>
</html>
