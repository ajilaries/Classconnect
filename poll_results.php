<?php
session_start();
include "config.php";

$poll_id = isset($_GET['poll_id']) ? intval($_GET['poll_id']) : 0;

// Fetch poll
$stmt = $conn->prepare("SELECT * FROM polls WHERE id = ?");
$stmt->bind_param("i", $poll_id);
$stmt->execute();
$poll = $stmt->get_result()->fetch_assoc();

if (!$poll) {
  echo "⚠️ Poll not found.";
  exit();
}

// Fetch total votes
$total_stmt = $conn->prepare("SELECT COUNT(*) as total_votes FROM poll_votes WHERE poll_id = ?");
$total_stmt->bind_param("i", $poll_id);
$total_stmt->execute();
$total = $total_stmt->get_result()->fetch_assoc()['total_votes'];

// Fetch options with vote count
$option_stmt = $conn->prepare("SELECT po.id, po.option_text, COUNT(pv.id) as vote_count
  FROM poll_options po
  LEFT JOIN poll_votes pv ON po.id = pv.option_id
  WHERE po.poll_id = ?
  GROUP BY po.id
  ORDER BY po.id");
$option_stmt->bind_param("i", $poll_id);
$option_stmt->execute();
$options = $option_stmt->get_result();

$is_active = strtotime($poll['expires_at']) > time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Poll Results</title>
  <link rel="stylesheet" href="poll.css">
  <style>
    .bar {
      height: 20px;
      background-color: #4caf50;
      text-align: right;
      padding-right: 5px;
      color: white;
      border-radius: 5px;
    }
    .bar-wrapper {
      width: 100%;
      background-color: #eee;
      border-radius: 5px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="poll-container">
    <h2>📊 Results: <?= htmlspecialchars($poll['question']) ?></h2>
    <p>Status: <?= $is_active ? '🟢 Active' : '🔴 Expired' ?></p>
    <p>Total Votes: <?= $total ?></p>

    <?php while ($row = $options->fetch_assoc()): 
      $percent = $total > 0 ? round(($row['vote_count'] / $total) * 100) : 0;
    ?>
      <p><?= htmlspecialchars($row['option_text']) ?> - <?= $row['vote_count'] ?> votes (<?= $percent ?>%)</p>
      <div class="bar-wrapper">
        <div class="bar" style="width: <?= $percent ?>%"><?= $percent ?>%</div>
      </div>
    <?php endwhile; ?>

    <br>
    <a href="poll_list.php">⬅️ Back to Polls</a>
  </div>
</body>
</html>
