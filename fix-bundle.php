<?php
/**
 * Fix Bundle - Link Questions to Media
 * 
 * This script repairs the bundle by linking questions to their media via media_id
 */

$sourceZip = __DIR__ . '/qbank/question_bundle_2025-12-14.zip';
$fixedZip = __DIR__ . '/qbank/question_bundle_FIXED.zip';

echo "Fixing bundle...\n\n";

// Open source ZIP
$zip = new ZipArchive();
if ($zip->open($sourceZip) !== TRUE) {
    die("Failed to open source ZIP\n");
}

// Read manifest
$manifestJson = $zip->getFromName('manifest.json');
$manifest = json_decode($manifestJson, true);

echo "Original manifest:\n";
echo "  Questions: " . count($manifest['questions']) . "\n";
echo "  Media: " . count($manifest['media_metadata']) . "\n\n";

// Create lookup for media by filename/URL
$mediaByUrl = [];
foreach ($manifest['media_metadata'] as $media) {
    // Index by URL
    $mediaByUrl[$media['url']] = $media['id'];
    // Also index by filename
    $filename = basename($media['url']);
    $mediaByUrl[$filename] = $media['id'];
}

echo "Linking questions to media...\n";
$linked = 0;
$notLinked = 0;

foreach ($manifest['questions'] as &$question) {
    if (empty($question['media_id']) && !empty($question['image_url'])) {
        // Try to find matching media
        $imageUrl = $question['image_url'];
        
        // Try exact URL match
        if (isset($mediaByUrl[$imageUrl])) {
            $question['media_id'] = $mediaByUrl[$imageUrl];
            $linked++;
            echo "  ✓ Linked question " . substr($question['id'], 0, 8) . "... to media\n";
            continue;
        }
        
        // Try filename match
        $filename = basename($imageUrl);
        if (isset($mediaByUrl[$filename])) {
            $question['media_id'] = $mediaByUrl[$filename];
            $linked++;
           echo "  ✓ Linked question " . substr($question['id'], 0, 8) . "... to media (by filename)\n";
            continue;
        }
        
        // Try URL without leading slash
        $urlNoSlash = ltrim($imageUrl, '/');
        if (isset($mediaByUrl[$urlNoSlash])) {
            $question['media_id'] = $mediaByUrl[$urlNoSlash];
            $linked++;
            echo "  ✓ Linked question " . substr($question['id'], 0, 8) . "... to media\n";
            continue;
        }
        
        $notLinked++;
        echo "  ✗ Could not link: " . $imageUrl . "\n";
    }
}
unset($question);

echo "\nResults:\n";
echo "  Questions linked: $linked\n";
echo "  Questions not linked: $notLinked\n\n";

// Create fixed ZIP
echo "Creating fixed bundle...\n";

$fixedZipObj = new ZipArchive();
if ($fixedZipObj->open($fixedZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Failed to create fixed ZIP\n");
}

// Add fixed manifest
$fixedManifestJson = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$fixedZipObj->addFromString('manifest.json', $fixedManifestJson);

// Copy all other files from source ZIP
for ($i = 0; $i < $zip->numFiles; $i++) {
    $filename = $zip->getNameIndex($i);
    if ($filename !== 'manifest.json') {
        $content = $zip->getFromIndex($i);
        $fixedZipObj->addFromString($filename, $content);
    }
}

$fixedZipObj->close();
$zip->close();

echo "✓ Fixed bundle created: qbank/question_bundle_FIXED.zip\n";
echo "\nYou can now import this fixed bundle.\n";
?>
