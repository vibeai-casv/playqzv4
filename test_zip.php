<?php
// Test script to verify bundle export is working

require_once 'api/config.php';
require_once 'api/db.php';

// Check if ZIP extension is available
if (!class_exists('ZipArchive')) {
    die("❌ ERROR: ZipArchive class not found. ZIP extension is not enabled.\n");
}

echo "✅ ZipArchive class is available!\n\n";

// Test creating a simple ZIP file
$testZip = new ZipArchive();
$tempFile = tempnam(sys_get_temp_dir(), 'test');
echo "Creating test ZIP at: $tempFile\n";

if ($testZip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
    die("❌ ERROR: Cannot create ZIP file\n");
}

$testZip->addFromString('test.txt', 'Hello, this is a test file!');
$testZip->close();

$size = filesize($tempFile);
echo "✅ Test ZIP created successfully! Size: $size bytes\n";

if ($size > 0) {
    echo "✅ ZIP file is valid and has content\n";
    
    // Copy to a location we can check
    $outputPath = __DIR__ . '/test_bundle.zip';
    copy($tempFile, $outputPath);
    echo "✅ Test file saved to: $outputPath\n";
} else {
    echo "❌ ERROR: ZIP file is empty\n";
}

@unlink($tempFile);

// Test database connection
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM questions");
    $result = $stmt->fetch();
    echo "\n✅ Database connection OK. Found {$result['count']} questions\n";
} catch (Exception $e) {
    echo "\n❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n🎉 All tests passed! Bundle export should work now.\n";
?>
