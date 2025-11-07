<?php
session_start();
include "config.php";

// Only teachers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
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
<style>
:root {
    --theme-color: #276cdb;
    --theme-hover: #276cdb;
    --bg-color: white;
    --text-color: black;
    --table-header-bg: #276cdb;
    --table-header-text: white;
    --table-row-hover: white;
}

body {
    font-family: Arial, sans-serif;
    background-color: var(--bg-color);
    color: var(--text-color);
    padding: 40px;
}

.poll-container {
    max-width: 1000px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

h2 {
    color: var(--theme-color);
    text-align: center;
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: var(--table-header-bg);
    color: var(--table-header-text);
    font-size: 16px;
}

table tr:hover {
    background-color: var(--table-row-hover);
}

a {
    text-decoration: none;
    color: var(--theme-color);
    font-weight: bold;
    margin: 0 5px;
    transition: color 0.2s;
}

a:hover {
    color: var(--theme-hover);
}

p {
    text-align: center;
    font-size: 18px;
    margin-top: 20px;
}
</style>
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
            <td><?= (strtotime($row['expires_at']) > time()) ? '🟢 Active' : '🔴 Expired' ?></td>
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
