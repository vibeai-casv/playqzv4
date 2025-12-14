<?php
// ZIP File Inspector
$zipFile = __DIR__ . '/qbank/question_bundle_2025-12-14.zip';

if (!file_exists($zipFile)) {
    die("ZIP file not found: $zipFile\n");
}

echo "Inspecting: $zipFile\n";
echo "File size: " . filesize($zipFile) . " bytes\n\n";

$zip = new ZipArchive();
if ($zip->open($zipFile) !== TRUE) {
    die("Failed to open ZIP file\n");
}

echo "Files in ZIP (" . $zip->numFiles . " total):\n";
echo str_repeat("-", 60) . "\n";

for ($i = 0; $i < $zip->numFiles; $i++) {
    $stat = $zip->statIndex($i);
    $isDir = substr($stat['name'], -1) === '/';
    $type = $isDir ? '[DIR]' : '[FILE]';
    $size = $isDir ? '-' : number_format($stat['size']) . ' bytes';
    echo sprintf("%-8s %-40s %s\n", $type, $stat['name'], $size);
}

echo "\n" . str_repeat("-", 60) . "\n";
echo "Checking manifest.json...\n";

$manifestJson = $zip->getFromName('manifest.json');
if ($manifestJson) {
    echo "✓ manifest.json found\n";
    $manifest = json_decode($manifestJson, true);
    if ($manifest) {
        echo "✓ manifest.json is valid JSON\n";
        echo "\nManifest contents:\n";
        echo "  - Questions: " . count($manifest['questions'] ?? []) . "\n";
        echo "  - Media metadata: " . count($manifest['media_metadata'] ?? []) . "\n";
        
        if (!empty($manifest['questions'])) {
            echo "\nFirst question sample:\n";
            $firstQ = $manifest['questions'][0];
            echo "  ID: " . $firstQ['id'] . "\n";
            echo "  Type: " . $firstQ['question_type'] . "\n";
            echo "  Text: " . substr($firstQ['question_text'], 0, 50) . "...\n";
            echo "  Media ID: " . ($firstQ['media_id'] ?? 'none') . "\n";
        }
    } else {
        echo "✗ manifest.json is NOT valid JSON\n";
    }
} else {
    echo "✗ manifest.json NOT found in ZIP\n";
}

$zip->close();
echo "\nInspection complete.\n";
?>
