<?php
include "config.php";
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['college_id'], $_SESSION['batch_id'])) {
    die("⛔ Please login to view timetables.");
}

$user_id    = $_SESSION['user_id'];
$role       = $_SESSION['role'];
$college_id = $_SESSION['college_id'];
$batch_id   = $_SESSION['batch_id'];

// ✅ Prepare SQL with batch filtering
$sql = "
  SELECT t.*, CONCAT(u.first_name, ' ', u.last_name) AS teacher_name
  FROM timetable t
  LEFT JOIN users u ON t.uploaded_by = u.id
  WHERE t.batch_id = ?
  ORDER BY t.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Uploaded Timetables</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f2f6fc;
    padding: 20px;
    margin: 0;
}
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}
.top-buttons {
    position: fixed;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
    z-index: 999;
}
.back-button,
.home-button {
    background-color: #276cdb;
    color: #fff;
    padding: 10px 15px;
    font-size: 0.9em;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.back-button:hover,
.home-button:hover {
    background-color: #1b4d99;
}
.timetable-list {
    max-width: 700px;
    margin: 80px auto 20px;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
.timetable-item {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s ease;
    border-radius: 8px;
    margin-bottom: 10px;
}
.timetable-item:hover {
    background-color: #f0f4ff;
}
.timetable-name {
    font-weight: bold;
    color: #276cdb;
    font-size: 1.1em;
    margin-bottom: 5px;
}
.batch-label {
    font-size: 0.85em;
    color: #555;
    margin-left: 5px;
}
.meta {
    font-size: 0.85em;
    color: #555;
    margin-bottom: 8px;
}
.timetable-link {
    color: #007bff;
    text-decoration: none;
    font-size: 0.9em;
    margin-right: 10px;
}
.timetable-link:hover {
    text-decoration: underline;
}
.delete-form {
    display: inline;
}
.delete-button {
    background-color: transparent;
    border: none;
    color: #dc3545;
    cursor: pointer;
    font-size: 0.85em;
    transition: color 0.2s ease;
}
.delete-button:hover {
    color: #a71d2a;
    text-decoration: underline;
}
@media screen and (max-width: 600px) {
    .timetable-list { padding: 15px; }
    .timetable-name { font-size: 1em; }
    .meta, .timetable-link, .delete-button { font-size: 0.8em; }
    .top-buttons { flex-direction: column; gap: 8px; right: 10px; }
    .back-button, .home-button { padding: 8px 12px; font-size: 0.8em; }
}
</style>
</head>
<body>
<div class="top-buttons">
    <button onclick="history.back()" class="back-button">🔙 Back</button>
    <a href="teacherdash.html" class="home-button">🏠 Home</a>
</div>

<div class="timetable-list">
<h2>📅 Uploaded Timetables</h2>

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='timetable-item'>";
        echo "<div class='timetable-name'>🗂️ " . htmlspecialchars($row['filename']) . 
             ($row['batch_id'] ? " <span class='batch-label'>(Batch " . $row['batch_id'] . ")</span>" : "") . 
             "</div>";
        echo "<div class='meta'>👤 Uploaded by: " . htmlspecialchars($row['teacher_name'] ?? 'Unknown') . "</div>";
        echo "<a class='timetable-link' href='" . htmlspecialchars($row['file_path']) . "' target='_blank'>📎 View File</a>";

        if (in_array($role, ['admin', 'teacher'])) {
            echo "<form class='delete-form' method='POST' action='timetable_delete.php' onsubmit='return confirm(\"Are you sure you want to delete this file?\")'>";
            echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
            echo "<input type='hidden' name='file_path' value='" . htmlspecialchars($row['file_path']) . "'>";
            echo "<button type='submit' class='delete-button'>🗑️ Delete</button>";
            echo "</form>";
        }

        echo "</div>";
    }
} else {
    echo "<p>No timetables uploaded yet for your batch.</p>";
}
?>

</div>
</body>
</html>
