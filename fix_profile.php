<?php
/**
 * Fix Missing Profile - Run this ONCE on production
 * 
 * This script creates the admin profile that's missing from the database.
 * The import is failing because created_by references a profile that doesn't exist.
 * 
 * Upload this file to: /public_html/aiq3/api/
 * Run once via browser: https://aiquiz.vibeai.cv/aiq3/api/fix_profile.php
 * Then delete this file!
 */

require_once 'config.php';
require_once 'db.php';

try {
    // Get the admin user's ID from users table
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = 'vibeaicasv@gmail.com' LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch();
    
    if (!$user) {
        die("ERROR: Admin user not found. Please create user first.");
    }
    
    $userId = $user['id'];
    $email = $user['email'];
    
    echo "Found user: $email (ID: $userId)<br>";
    
    // Check if profile already exists
    $stmt = $pdo->prepare("SELECT id FROM profiles WHERE id = ?");
    $stmt->execute([$userId]);
    $existingProfile = $stmt->fetch();
    
    if ($existingProfile) {
        echo "✅ Profile already exists! No action needed.<br>";
        echo "<br><strong>You can now import questions successfully.</strong><br>";
        echo "<br><em>Please delete this file (fix_profile.php) for security.</em>";
        exit;
    }
    
    // Create the missing profile
    echo "Creating profile...<br>";
    
    $stmt = $pdo->prepare("
        INSERT INTO profiles (id, email, name, role, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([
        $userId,
        $email,
        'Admin User',
        'admin'
    ]);
    
    echo "✅ <strong>Profile created successfully!</strong><br><br>";
    echo "Details:<br>";
    echo "- Email: $email<br>";
    echo "- Role: admin<br>";
    echo "- ID: $userId<br><br>";
    
    echo "<strong>✅ You can now import questions successfully!</strong><br><br>";
    echo "<strong>⚠️ IMPORTANT: Delete this file immediately for security!</strong><br>";
    echo "<code>Delete: /public_html/aiq3/api/fix_profile.php</code>";
    
} catch (PDOException $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<br>If you see 'Duplicate entry' error, the profile already exists.";
}
?>
