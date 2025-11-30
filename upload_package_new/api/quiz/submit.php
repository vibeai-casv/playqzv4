<?php
require_once '../config.php';
require_once '../db.php';
require_once '../utils.php';

cors();

$session = authenticate($pdo);
$input = getJsonInput();

$attemptId = $input['attemptId'] ?? null;
$responses = $input['responses'] ?? []; // Array of response objects

if (!$attemptId || empty($responses)) {
    jsonResponse(['error' => 'Invalid input'], 400);
}

try {
    $pdo->beginTransaction();

    // Verify attempt belongs to user
    $stmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE id = ? AND user_id = ?");
    $stmt->execute([$attemptId, $session['user_id']]);
    $attempt = $stmt->fetch();

    if (!$attempt) {
        throw new Exception("Attempt not found or unauthorized");
    }

    $correctAnswers = 0;
    $totalTimeSpent = 0;

    $stmtInsert = $pdo->prepare("INSERT INTO quiz_responses (
        id, attempt_id, question_id, user_id, user_answer, is_correct, 
        time_spent_seconds, points_awarded, max_points, question_position, skipped
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($responses as $r) {
        $isCorrect = $r['is_correct'] ? 1 : 0;
        if ($isCorrect) $correctAnswers++;
        $totalTimeSpent += ($r['time_spent_seconds'] ?? 0);

        $stmtInsert->execute([
            generateUuid(),
            $attemptId,
            $r['question_id'],
            $session['user_id'],
            $r['user_answer'],
            $isCorrect,
            $r['time_spent_seconds'] ?? 0,
            $r['points_awarded'] ?? 0,
            $r['max_points'] ?? 0,
            $r['question_position'] ?? 0,
            $r['skipped'] ? 1 : 0
        ]);
    }

    // Update Attempt
    $totalQuestions = $attempt['total_questions'];
    $score = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;
    
    $stmtUpdate = $pdo->prepare("UPDATE quiz_attempts SET 
        status = 'completed',
        score = ?,
        correct_answers = ?,
        time_spent_seconds = ?,
        completed_at = NOW()
        WHERE id = ?");
    
    $stmtUpdate->execute([$score, $correctAnswers, $totalTimeSpent, $attemptId]);

    // Update User Stats (Simple version)
    // Fetch current stats
    $stmtProfile = $pdo->prepare("SELECT stats FROM profiles WHERE id = ?");
    $stmtProfile->execute([$session['user_id']]);
    $profile = $stmtProfile->fetch();
    
    $stats = json_decode($profile['stats'] ?? '{}', true);
    $stats['totalAttempts'] = ($stats['totalAttempts'] ?? 0) + 1;
    $stats['totalTimeSpent'] = ($stats['totalTimeSpent'] ?? 0) + $totalTimeSpent;
    $stats['bestScore'] = max($stats['bestScore'] ?? 0, $score);
    // Recalculate average (simplified)
    // Ideally we query all attempts, but for now let's just approximate or query
    
    // Let's query actual average
    $stmtAvg = $pdo->prepare("SELECT AVG(score) FROM quiz_attempts WHERE user_id = ? AND status = 'completed'");
    $stmtAvg->execute([$session['user_id']]);
    $avgScore = $stmtAvg->fetchColumn();
    $stats['averageScore'] = (float)$avgScore;

    $stmtUpdateProfile = $pdo->prepare("UPDATE profiles SET stats = ? WHERE id = ?");
    $stmtUpdateProfile->execute([json_encode($stats), $session['user_id']]);

    $pdo->commit();
    jsonResponse(['message' => 'Quiz submitted successfully', 'score' => $score]);

} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(['error' => 'Submission failed: ' . $e->getMessage()], 500);
}
?>
