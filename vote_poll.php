<?php
session_start();
include "config.php";

// ✅ Only students can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    die("🚫 Access Denied!");
}

$user_id  = $_SESSION['user_id'];
$batch_id = $_SESSION['batch_id'] ?? 0;

// ✅ Fetch all active polls for this batch
$stmt = $conn->prepare("
    SELECT * 
    FROM polls 
    WHERE batch_id=? AND status=1 AND (expires_at IS NULL OR expires_at > NOW())
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$polls = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Active Polls</title>
<link rel="stylesheet" href="poll.css">
</head>
<body>

<?php
if ($polls->num_rows === 0) {
    echo "<p>No active polls right now.</p>";
} else {
    while ($poll = $polls->fetch_assoc()):

        // Check if the user already voted
        $vote_key = $poll['is_anonymous'] ? hash('sha256', $user_id . $poll['id']) : $user_id;
        $check_stmt = $conn->prepare("SELECT * FROM poll_votes WHERE poll_id=? AND user_id=?");
        $check_stmt->bind_param($poll['is_anonymous'] ? "is" : "ii", $poll['id'], $vote_key);
        $check_stmt->execute();
        $voted = $check_stmt->get_result()->num_rows > 0;
        $check_stmt->close();
?>

<div class="poll-container">
    <h3><?= htmlspecialchars($poll['question']) ?></h3>

    <?php if ($voted): ?>
        <p>✅ You’ve already voted in this poll.</p>
        <a href="poll_results.php?poll_id=<?= $poll['id'] ?>">📊 View Results</a>
    <?php else: ?>
        <form action="submit_vote.php" method="POST">
            <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
            <?php
            // Fetch poll options
            $opt_stmt = $conn->prepare("SELECT * FROM poll_options WHERE poll_id=? ORDER BY id");
            $opt_stmt->bind_param("i", $poll['id']);
            $opt_stmt->execute();
            $options = $opt_stmt->get_result();
            while ($opt = $options->fetch_assoc()):
            ?>
                <label>
                    <input type="<?= $poll['is_multiple_choice'] ? 'checkbox' : 'radio' ?>" 
                           name="option_ids[]" 
                           value="<?= $opt['id'] ?>" required>
                    <?= htmlspecialchars($opt['option_text']) ?>
                </label><br>
            <?php endwhile;
            $opt_stmt->close(); ?>
            <button type="submit">✅ Submit Vote</button>
        </form>
    <?php endif; ?>
</div>

<?php endwhile; } ?>

</body>
</html>
