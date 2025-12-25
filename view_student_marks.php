<?php
session_start();
include "config.php";

// Only students
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
    exit();
}

$student_id = $_SESSION['user_id'];
$batch_id   = $_SESSION['batch_id'];

// Get selected subject filter
$subject = isset($_GET['subject']) ? $_GET['subject'] : "";

// Fetch subjects for this student's batch
$subjects = $conn->query("
    SELECT DISTINCT subject 
    FROM teacher_allocations 
    WHERE batch_id='$batch_id'
");

// Fetch marks for this student (filtered by subject if selected)
$marks = [];
$sql = "SELECT m.subject, m.exam_type, m.marks_obtained, m.max_marks, CONCAT(t.first_name,' ',t.last_name) AS teacher_name
        FROM marks m
        JOIN users t ON t.id = m.teacher_id
        WHERE m.student_id=?";
$params = [$student_id];
$types = "i";

if(!empty($subject)){
    $sql .= " AND m.subject=?";
    $params[] = $subject;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$marks = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Marks</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<h2>📚 My Marks</h2>

<!-- Subject filter -->
<form method="GET">
    <label>Subject:</label>
    <select name="subject">
        <option value="">--All Subjects--</option>
        <?php while($row = $subjects->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['subject']) ?>" <?= ($row['subject'] == $subject)?'selected':'' ?>>
                <?= htmlspecialchars($row['subject']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button type="submit">View</button>
</form>

<br>

<!-- Marks Table -->
<?php if(!empty($marks)): ?>
<table border="1" cellpadding="8">
    <tr>
        <th>Subject</th>
        <th>Exam Type</th>
        <th>Marks Obtained</th>
        <th>Max Marks</th>
        <th>Teacher</th>
    </tr>
    <?php foreach($marks as $m): ?>
    <tr>
        <td><?= htmlspecialchars($m['subject']) ?></td>
        <td><?= htmlspecialchars($m['exam_type']) ?></td>
        <td><?= $m['marks_obtained'] ?></td>
        <td><?= $m['max_marks'] ?></td>
        <td><?= htmlspecialchars($m['teacher_name']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>No marks found for the selected subject.</p>
<?php endif; ?>

</body>
</html>
