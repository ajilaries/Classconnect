<?php
session_start();
include "config.php";

// Make sure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  echo "🚫 Access denied!";
  exit();
}

// Fetch all polls
$sql = "SELECT * FROM polls ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Poll List - Admin</title>
  <link rel="stylesheet" href="poll.css">
</head>
<body>
  <div class="poll-container">
    <h2>🗳️ All Created Polls</h2>

    <?php if ($result->num_rows > 0): ?>
      <table>
        <tr>
          <th>ID</th>
          <th>Question</th>
          <th>Created</th>
          <th>Expires</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['question']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td><?= $row['expires_at'] ?></td>
            <td>
              <?= (strtotime($row['expires_at']) > time()) ? '🟢 Active' : '🔴 Expired' ?>
            </td>
            <td>
              <a href="poll_results.php?poll_id=<?= $row['id'] ?>">📊 View</a> |
              <a href="end_poll.php?poll_id=<?= $row['id'] ?>" onclick="return confirm('End this poll now?')">🛑 End Now</a> |
              <a href="delete_poll.php?poll_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this poll?')">❌ Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No polls found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
