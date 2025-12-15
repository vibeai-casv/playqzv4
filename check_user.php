<?php
/**
 * Check Current User Session
 * 
 * This shows which user you're currently logged in as
 * and whether they have a profile.
 * 
 * Upload to: /public_html/aiq3/api/check_user.php
 * Visit: https://aiquiz.vibeai.cv/aiq3/api/check_user.php
 * DELETE after checking!
 */

require_once 'config.php';
require_once 'db.php';
require_once 'utils.php';

cors();

try {
    // Get current session
    $session = authenticate($pdo);
    
    echo "<h2>Current Login Status</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td><strong>Logged in as Email</strong></td><td><strong style='color: blue;'>{$session['email']}</strong></td></tr>";
    echo "<tr><td>User ID</td><td>{$session['user_id']}</td></tr>";
    echo "<tr><td>Role</td><td>{$session['role']}</td></tr>";
    echo "<tr><td>Profile ID</td><td>" . ($session['profile_id'] ?? 'NULL') . "</td></tr>";
    echo "</table>";
    
    // Check if profile exists
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE id = ?");
    $stmt->execute([$session['user_id']]);
    $profile = $stmt->fetch();
    
    echo "<h3>Profile Check:</h3>";
    if ($profile) {
        echo "<p style='color: green; font-weight: bold;'>✅ Profile EXISTS for this user</p>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>Profile ID</td><td>{$profile['id']}</td></tr>";
        echo "<tr><td>Email</td><td>{$profile['email']}</td></tr>";
        echo "<tr><td>Name</td><td>{$profile['name']}</td></tr>";
        echo "<tr><td>Role</td><td>{$profile['role']}</td></tr>";
        echo "</table>";
        echo "<br><p style='background: #d4edda; padding: 15px; border-left: 4px solid green;'>";
        echo "<strong>✅ GOOD NEWS!</strong> Your profile exists. Import should work!<br><br>";
        echo "If import still fails, try:<br>";
        echo "1. Logout and login again<br>";
        echo "2. Clear browser cache<br>";
        echo "3. Check the import JSON format is correct";
        echo "</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Profile MISSING for this user</p>";
        echo "<p style='background: #f8d7da; padding: 15px; border-left: 4px solid red;'>";
        echo "<strong>This is why import fails!</strong><br><br>";
        echo "To fix, run this SQL in phpMyAdmin:<br><br>";
        echo "<code style='background: #f5f5f5; padding: 10px; display: block;'>";
        echo "INSERT INTO profiles (id, email, name, role, created_at, updated_at)<br>";
        echo "VALUES ('{$session['user_id']}', '{$session['email']}', 'Admin User', 'admin', NOW(), NOW());";
        echo "</code>";
        echo "</p>";
    }
    
    // Show all users and their profiles
    echo "<h3>All Users with Profile Status:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Email</th><th>Has Profile?</th><th>Role</th></tr>";
    
    $stmt = $pdo->query("
        SELECT u.email, 
               CASE WHEN p.id IS NULL THEN 'NO ❌' ELSE 'YES ✓' END as has_profile,
               COALESCE(p.role, 'N/A') as role
        FROM users u
        LEFT JOIN profiles p ON u.id = p.id
        ORDER BY u.created_at DESC
    ");
    
    while ($row = $stmt->fetch()) {
        $color = $row['has_profile'] === 'YES ✓' ? 'green' : 'red';
        echo "<tr>";
        echo "<td>{$row['email']}</td>";
        echo "<td style='color: $color; font-weight: bold;'>{$row['has_profile']}</td>";
        echo "<td>{$row['role']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><hr><br>";
    echo "<p style='background: #fff3cd; padding: 15px; border-left: 4px solid orange;'>";
    echo "<strong>⚠️ SECURITY WARNING:</strong><br>";
    echo "Delete this file immediately: /public_html/aiq3/api/check_user.php";
    echo "</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>You may not be logged in. Please login first at: <a href='https://aiquiz.vibeai.cv/aiq3/'>https://aiquiz.vibeai.cv/aiq3/</a></p>";
}
?>
