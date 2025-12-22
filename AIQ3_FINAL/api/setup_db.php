<?php
require_once 'config.php';

echo "Setting up database: " . DB_NAME . "\n";

try {
    // Connect to MySQL server (without selecting DB first)
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
    echo "Database created or already exists.\n";

    // Select the database
    $pdo->exec("USE `" . DB_NAME . "`");

    // Read schema file
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        die("Error: schema.sql not found.\n");
    }

    $sql = file_get_contents($schemaFile);

    // Execute SQL commands
    // Note: Splitting by semicolon is a naive way to execute multiple statements, 
    // but works for simple schemas. For complex ones with triggers/procedures, 
    // we might need a more robust parser.
    // However, PDO can execute multiple statements if configured, but it's safer to split.
    // Since our schema is clean, we can try executing it in chunks or just one go if the driver supports it.
    
    // Let's try executing the whole block. MySQL PDO usually supports multiple statements if emulated prepares are on or specific flags set.
    // But let's be safe and split by command.
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                echo "Error executing statement: \n" . substr($statement, 0, 100) . "...\n";
                echo "Message: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "Schema imported successfully.\n";
    
    // Create a default admin user if not exists
    // ID: uuid v4
    $adminId = '00000000-0000-0000-0000-000000000000'; // Fixed ID for simplicity or generate one
    // Let's generate a proper UUID
    function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['vibeaicasv@gmail.com']);
    $user = $stmt->fetch();

    if (!$user) {
        $adminId = gen_uuid();
        $password = password_hash('password123', PASSWORD_DEFAULT);
        
        $pdo->prepare("INSERT INTO users (id, email, password_hash) VALUES (?, ?, ?)")
            ->execute([$adminId, 'vibeaicasv@gmail.com', $password]);
            
        $pdo->prepare("INSERT INTO profiles (id, email, name, role, category) VALUES (?, ?, ?, ?, ?)")
            ->execute([$adminId, 'vibeaicasv@gmail.com', 'Admin User', 'admin', 'professional']);
            
        echo "Default admin user created (vibeaicasv@gmail.com / password123)\n";
    } else {
        echo "Admin user already exists.\n";
    }

} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
