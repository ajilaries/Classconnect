<?php
session_start();
include "config.php";

// Only students can vote
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
  echo "🚫 Access denied!";
  exit();
}

$user_id = $_SESSION['admission_no'];
$poll_id = intval($_POST['poll_id']);
$option_ids = $_POST['option_ids'] ?? [];

if (empty($option_ids)) {
  echo "⚠️ Please select at least one option.";
  exit();
}

// Check if already voted
$check = $conn->prepare("SELECT * FROM poll_votes WHERE poll_id = ? AND user_id = ?");
$check->bind_param("is", $poll_id, $user_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
  echo "⚠️ You’ve already voted!";
  exit();
}

// Insert vote(s)
$stmt = $conn->prepare("INSERT INTO poll_votes (poll_id, option_id, user_id, voted_at) VALUES (?, ?, ?, NOW())");

foreach ($option_ids as $oid) {
  $oid = intval($oid);
  $stmt->bind_param("iis", $poll_id, $oid, $user_id);
  $stmt->execute();
}

header("Location: poll_results.php?poll_id=$poll_id&voted=1");
exit();
?>
