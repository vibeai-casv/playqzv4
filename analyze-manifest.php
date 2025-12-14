<?php
// Check manifest for media_id issues
$zipFile = __DIR__ . '/qbank/question_bundle_2025-12-14.zip';
$zip = new ZipArchive();
$zip->open($zipFile);

$manifestJson = $zip->getFromName('manifest.json');
$manifest = json_decode($manifestJson, true);

echo "Analyzing manifest for potential issues...\n\n";

// Check questions
echo "Questions Analysis:\n";
echo str_repeat("-", 60) . "\n";

$questionsWithMedia = 0;
$questionsWithoutMedia = 0;
$mediaIds = [];

foreach ($manifest['questions'] as $i => $q) {
    if (!empty($q['media_id']) && $q['media_id'] !== 'null' && $q['media_id'] !== null) {
        $questionsWithMedia++;
        $mediaIds[] = $q['media_id'];
    } else {
        $questionsWithoutMedia++;
        if ($i < 3) { // Show first 3 questions without media
            echo "Question #{$i}: No media_id\n";
            echo "  ID: " . $q['id'] . "\n";
            echo "  Type: " . $q['question_type'] . "\n";
            echo "  Image URL: " . ($q['image_url'] ?? 'none') . "\n\n";
        }
    }
}

echo "\nSummary:\n";
echo "  Questions with media_id: $questionsWithMedia\n";
echo "  Questions without media_id: $questionsWithoutMedia\n";
echo "  Total questions: " . count($manifest['questions']) . "\n\n";

// Check media metadata
echo "Media Metadata Analysis:\n";
echo str_repeat("-", 60) . "\n";
echo "  Total media entries: " . count($manifest['media_metadata']) . "\n";

if (!empty($manifest['media_metadata'])) {
    echo "\nFirst media entry:\n";
    $firstMedia = $manifest['media_metadata'][0];
    echo "  ID: " . $firstMedia['id'] . "\n";
    echo "  Filename: " . $firstMedia['filename'] . "\n";
    echo "  URL: " . $firstMedia['url'] . "\n";
}

// Check for orphaned media (media not referenced by questions)
$referencedMediaIds = array_unique($mediaIds);
$allMediaIds = array_column($manifest['media_metadata'], 'id');
$orphanedMedia = array_diff($allMediaIds, $referencedMediaIds);

echo "\nMedia Usage:\n";
echo "  Referenced by questions: " . count($referencedMediaIds) . "\n";
echo "  Total in metadata: " . count($allMediaIds) . "\n";
echo "  Orphaned (not used): " . count($orphanedMedia) . "\n";

if (count($orphanedMedia) > 0) {
    echo "\n⚠️  WARNING: Some media files are not linked to any question!\n";
}

$zip->close();

// Check for potential issues
echo "\n" . str_repeat("=", 60) . "\n";
echo "POTENTIAL ISSUES:\n";
echo str_repeat("=", 60) . "\n";

if ($questionsWithoutMedia > 0 && $manifest['questions'][0]['question_type'] === 'image_identify_person') {
    echo "❌ Issue: Questions of type 'image_identify_person' should have media_id\n";
    echo "   Found: $questionsWithoutMedia questions without media_id\n";
    echo "   This will cause import failures!\n\n";
    echo "   Solution: Questions need to link to media via media_id field\n";
}

if (count($orphanedMedia) > 0) {
    echo "⚠️  Warning: " . count($orphanedMedia) . " media files are not linked to questions\n";
    echo "   They will be imported but not used\n\n";
}

echo "\nDone.\n";
?>
