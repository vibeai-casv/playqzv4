<?php
/**
 * Simple Directory Scanner
 * Shows what files actually exist on the server
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Directory Scanner</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1 { color: #4ec9b0; }
        h2 { color: #569cd6; margin-top: 30px; }
        .file { color: #ce9178; padding: 5px; }
        .dir { color: #4ec9b0; padding: 5px; font-weight: bold; }
        .info { background: #264f78; padding: 10px; margin: 10px 0; border-left: 4px solid #569cd6; }
        .error { background: #5a1d1d; padding: 10px; margin: 10px 0; border-left: 4px solid #f48771; }
        .success { background: #1e3a1e; padding: 10px; margin: 10px 0; border-left: 4px solid #4ec9b0; }
        pre { background: #2d2d2d; padding: 15px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>📁 Directory Scanner</h1>
    
    <?php
    $currentDir = __DIR__;
    $parentDir = dirname($currentDir);
    
    echo "<div class='info'>";
    echo "<strong>Current script location:</strong><br>";
    echo "Script: <code>$currentDir</code><br>";
    echo "Parent: <code>$parentDir</code>";
    echo "</div>";
    
    // Function to scan directory
    function listDirectory($path, $indent = 0) {
        if (!is_dir($path)) {
            echo "<div class='error'>Not a directory: $path</div>";
            return;
        }
        
        $items = @scandir($path);
        if ($items === false) {
            echo "<div class='error'>Cannot read directory: $path</div>";
            return;
        }
        
        $files = [];
        $dirs = [];
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $fullPath = $path . '/' . $item;
            
            if (is_dir($fullPath)) {
                $dirs[] = $item;
            } else {
                $size = filesize($fullPath);
                $sizeKB = round($size / 1024, 2);
                $files[] = ['name' => $item, 'size' => $sizeKB];
            }
        }
        
        // Show directories first
        foreach ($dirs as $dir) {
            $spaces = str_repeat('&nbsp;&nbsp;', $indent);
            echo "<div class='dir'>{$spaces}📁 $dir/</div>";
            
            // Recursively list subdirectories (limit depth to 3)
            if ($indent < 3) {
                listDirectory($path . '/' . $dir, $indent + 1);
            }
        }
        
        // Then show files
        foreach ($files as $file) {
            $spaces = str_repeat('&nbsp;&nbsp;', $indent);
            echo "<div class='file'>{$spaces}📄 {$file['name']} ({$file['size']} KB)</div>";
        }
    }
    
    // Scan parent directory
    echo "<h2>Contents of: $parentDir</h2>";
    echo "<pre>";
    listDirectory($parentDir);
    echo "</pre>";
    
    // Check specifically for /api/ directory
    $apiDir = $parentDir . '/api';
    echo "<h2>Checking for /api/ directory</h2>";
    
    if (is_dir($apiDir)) {
        echo "<div class='success'>✓ /api/ directory EXISTS at: <code>$apiDir</code></div>";
        
        echo "<h3>Contents of /api/:</h3>";
        echo "<pre>";
        listDirectory($apiDir);
        echo "</pre>";
        
        // Check for specific important files
        $importantFiles = [
            'config.php',
            'db.php',
            'index.php',
            'auth/login.php',
            'auth/signup.php'
        ];
        
        echo "<h3>Important Files Check:</h3>";
        foreach ($importantFiles as $file) {
            $fullPath = $apiDir . '/' . $file;
            if (file_exists($fullPath)) {
                $size = round(filesize($fullPath) / 1024, 2);
                echo "<div class='success'>✓ $file ($size KB)</div>";
            } else {
                echo "<div class='error'>✗ $file (MISSING)</div>";
            }
        }
        
    } else {
        echo "<div class='error'>✗ /api/ directory NOT FOUND at: <code>$apiDir</code></div>";
        echo "<div class='info'>You need to create /api/ directory and upload your API files there!</div>";
    }
    
    // Show what directories DO exist
    echo "<h2>Directories in parent folder:</h2>";
    $items = @scandir($parentDir);
    if ($items !== false) {
        echo "<pre>";
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $fullPath = $parentDir . '/' . $item;
            if (is_dir($fullPath)) {
                echo "📁 $item/\n";
            }
        }
        echo "</pre>";
    }
    
    ?>
    
    <div class="info" style="margin-top: 30px;">
        <strong>What to do next:</strong><br>
        1. Check if /api/ directory exists above<br>
        2. If it exists, check what files are inside<br>
        3. If it doesn't exist, create it and upload your API files<br>
        4. Upload path should be: <code><?php echo $parentDir; ?>/api/</code>
    </div>
</div>
</body>
</html>
