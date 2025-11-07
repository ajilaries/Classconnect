<?php
session_start();
include "config.php";

// Ensure the user is logged in as a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Step 1: Get all subjects assigned to this teacher
$sql_alloc = "SELECT subject FROM teacher_allocations WHERE teacher_id = ?";
$stmt_alloc = $conn->prepare($sql_alloc);
$stmt_alloc->bind_param("i", $teacher_id);
$stmt_alloc->execute();
$result_alloc = $stmt_alloc->get_result();

$subjects = [];
while ($row = $result_alloc->fetch_assoc()) {
    $subjects[] = $row['subject'];
}
$stmt_alloc->close();

// Step 2: Fetch feedback for each subject
$feedbacks = [];
foreach ($subjects as $subject) {
    $sql_fb = "SELECT feedback_text, category, rating FROM feedback WHERE subject = ? ORDER BY id DESC";
    $stmt_fb = $conn->prepare($sql_fb);
    $stmt_fb->bind_param("s", $subject);
    $stmt_fb->execute();
    $result_fb = $stmt_fb->get_result();

    while ($row = $result_fb->fetch_assoc()) {
        $row['subject'] = $subject; // attach subject for display
        $feedbacks[] = $row;
    }
    $stmt_fb->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Feedback</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        h2 { margin-bottom: 20px; }
        .feedback-card {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .feedback-card p { margin: 5px 0; }
        .feedback-card strong { color: #333; }
    </style>
</head>
<body>
    <h2>📝 Feedback for Your Subjects</h2>

    <?php if (count($feedbacks) > 0): ?>
        <?php foreach ($feedbacks as $fb): ?>
            <div class="feedback-card">
                <p><strong>Subject:</strong> <?= htmlspecialchars($fb['subject']) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($fb['category']) ?></p>
                <p><strong>Rating:</strong> <?= $fb['rating'] ?> ⭐</p>
                <p><strong>Feedback:</strong> <?= htmlspecialchars($fb['feedback_text']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No feedback has been submitted for your subjects yet.</p>
    <?php endif; ?>
</body>
</html>
