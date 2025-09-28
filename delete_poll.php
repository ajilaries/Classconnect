<?php
session_start();
include "config.php";

// Only admins allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  echo "🚫 Access denied!";
  exit();
}

if (isset($_GET['poll_id'])) {
  $poll_id = intval($_GET['poll_id']);

  $stmt = $conn->prepare("DELETE FROM polls WHERE id = ?");
  $stmt->bind_param("i", $poll_id);

  if ($stmt->execute()) {
    header("Location: poll_list.php?msg=Poll deleted successfully");
    exit();
  } else {
    echo "❌ Failed to delete poll.";
  }
} else {
  echo "⚠️ Poll ID not provided.";
}
?>
