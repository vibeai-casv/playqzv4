<?php
/**
 * Test Login Functionality Directly
 */

echo "=== Testing Login API ===\n\n";

// Test with correct credentials
$testCredentials = [
    ['email' => 'admin@example.com', 'password' => 'admin123'],
    ['email' => 'testadmin@local.test', 'password' => 'testadmin123'],
];

foreach ($testCredentials as $index => $creds) {
    echo "Test " . ($index + 1) . ": {$creds['email']}\n";
    echo "Password: {$creds['password']}\n";
    
    // Prepare POST data
    $postData = json_encode($creds);
    
    // Make request to login.php
    $ch = curl_init('http://projects/playqzv4/api/auth/login.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    
    if ($error) {
        echo "CURL Error: $error\n";
    }
    
    if ($response) {
        $decoded = json_decode($response, true);
        if (isset($decoded['token'])) {
            echo "✅ SUCCESS! Token received\n";
            echo "Token: " . substr($decoded['token'], 0, 20) . "...\n";
            echo "User: {$decoded['user']['email']}\n";
            echo "Role: {$decoded['user']['role']}\n";
        } else if (isset($decoded['error'])) {
            echo "❌ FAILED: {$decoded['error']}\n";
        } else {
            echo "Response: $response\n";
        }
    } else {
        echo "❌ No response received\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

echo "=== Testing Complete ===\n";
?>
