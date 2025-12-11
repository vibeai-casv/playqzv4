<?php
require_once 'api/config.php';
require_once 'api/db.php';

global $pdo;

if (!isset($pdo)) {
    if (!defined('DB_HOST')) {
        die("DB constants not defined. verify config.php path.\n");
    }
    
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage() . "\n");
    }
}

function gen_uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

try {
    // Find an admin user for created_by
    $stmt = $pdo->query("SELECT id FROM profiles WHERE role='admin' LIMIT 1");
    $admin = $stmt->fetch();
    if (!$admin) {
        $stmt = $pdo->query("SELECT id FROM profiles LIMIT 1");
        $admin = $stmt->fetch();
    }
    $created_by = $admin ? $admin['id'] : gen_uuid(); // Fallback if no users, but likely will fail FK

    // Check count
    $stmt = $pdo->query("SELECT count(*) as count FROM questions WHERE question_type = 'image_identify_person'");
    $count = $stmt->fetch()['count'];
    echo "Current personality questions: $count\n";
    
    if ($count < 5) {
        echo "Importing questions from qbank/ai_personalities.json...\n";
        $json = file_get_contents('qbank/ai_personalities.json');
        if (!$json) die("Could not read qbank/ai_personalities.json\n");
        $questions = json_decode($json, true);
        
        if (!$questions) {
            die("Failed to decode JSON\n");
        }
        
        $inserted = 0;
        $stmt = $pdo->prepare("
            INSERT INTO questions (
                id, question_text, question_type, difficulty, category, subcategory, 
                image_url, options, correct_answer, explanation, points, created_by
            ) VALUES (
                ?, ?, 'image_identify_person', ?, ?, ?, 
                ?, ?, ?, ?, ?, ?
            )
        ");
        
        foreach ($questions as $q) {
            // Check existence
            $check = $pdo->prepare("SELECT id FROM questions WHERE question_text = ?");
            $check->execute([$q['question_text']]);
            if ($check->fetch()) {
                continue;
            }
            
            $options_json = json_encode($q['options']);
            $uuid = gen_uuid();
            
            $stmt->execute([
                $uuid,
                $q['question_text'],
                $q['difficulty'],
                $q['category'],
                $q['subcategory'],
                $q['image_url'] ?? null,
                $options_json,
                $q['correct_answer'],
                $q['explanation'],
                10,
                $created_by
            ]);
            $inserted++;
            if ($inserted >= 5 && ($count + $inserted) >= 5) { 
                // imported enough
            }
        }
        echo "Inserted $inserted questions.\n";
    } else {
        echo "Sufficient questions exist.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
