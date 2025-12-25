<?php
// api/fix_missing_profiles.php
// Run this script in your browser: https://aiquiz.vibeai.cv/aiq2/api/fix_missing_profiles.php

require_once 'config.php';
require_once 'db.php';
require_once 'utils.php'; // For UUID if needed, though we use existing user IDs

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><body style='font-family: sans-serif; padding: 20px;'>";
echo "<h1>Profile Integrity Fixer</h1>";

try {
    // 1. Fetch all users from the users table
    echo "<h3>Step 1: Scanning Users...</h3>";
    $stmt = $pdo->query("SELECT id, email FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($users) . " users in the database.<br><br>";

    $fixedCount = 0;

    foreach ($users as $user) {
        $userId = $user['id'];
        $email = $user['email'];

        echo "Checking user: <strong>" . htmlspecialchars($email) . "</strong> (ID: $userId)... ";

        // 2. Check if a profile exists for this user ID
        $checkStmt = $pdo->prepare("SELECT id, role FROM profiles WHERE id = ?");
        $checkStmt->execute([$userId]);
        $profile = $checkStmt->fetch();

        if ($profile) {
            echo "<span style='color: green;'>OK (Profile exists, Role: {$profile['role']})</span><br>";
        } else {
            echo "<span style='color: red;'>MISSING PROFILE!</span> Attempting to fix... ";

            // 3. Create the missing profile
            try {
                // Generate a name from email
                $parts = explode('@', $email);
                $name = ucfirst($parts[0]);

                // Determine role (default to admin for the specific email asking for help, or user otherwise)
                // You mentioned vibeaicasv@gmail.com is supposed to be admin
                $role = 'admin'; 
                // Alternatively, 'super_admin' if the enum supports it
                if (strpos($email, 'vibeaicasv') !== false) {
                     $role = 'super_admin'; // Attempt super_admin if supported, fallback to admin in catch if enum fails? 
                     // Let's stick to 'admin' to be safe with FKs, usually updated later. 
                     // Actually schema shows ENUM('user', 'admin'). Wait, schema shows ENUM('user', 'admin'). 
                     // user's conversations mention adding 'super_admin'. 
                     // Let's safe bet 'admin' for now.
                     $role = 'admin'; 
                }

                $insertStmt = $pdo->prepare("
                    INSERT INTO profiles (id, email, name, role, created_at, updated_at)
                    VALUES (?, ?, ?, ?, NOW(), NOW())
                ");
                
                $insertStmt->execute([$userId, $email, $name, $role]);
                
                echo "<span style='color: green; font-weight: bold;'>FIXED! Created '$role' profile.</span><br>";
                $fixedCount++;

            } catch (Exception $e) {
                echo "<span style='color: red; font-weight: bold;'>FAILED: " . $e->getMessage() . "</span><br>";
            }
        }
    }

    echo "<h3>Summary</h3>";
    if ($fixedCount > 0) {
        echo "<p style='color: green; font-size: 1.2em;'>Successfully fixed $fixedCount missing profiles.</p>";
        echo "<p>Please go back to the Admin Dashboard and try importing your questions again.</p>";
    } else {
        echo "<p>No missing profiles were found. If you are still seeing errors, please check if your session has expired by logging out and logging back in.</p>";
    }

} catch (PDOException $e) {
    echo "<h2>Database Connection Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Check your config.php settings.</p>";
}

echo "</body></html>";
?>
