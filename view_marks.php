<?php
session_start();
include "config.php";

// Only teachers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit();
}

$college_id = $_SESSION['college_id'];
$batch_id   = $_SESSION['batch_id'];

// Get filter values
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$subject    = isset($_GET['subject']) ? $_GET['subject'] : "";
$exam_type  = isset($_GET['exam_type']) ? $_GET['exam_type'] : "";
$pass_marks = isset($_GET['pass_marks']) ? floatval($_GET['pass_marks']) : 0;

// Fetch students in this batch
$students = $conn->query("
    SELECT id, CONCAT(first_name, ' ', last_name) AS full_name 
    FROM users 
    WHERE role = 'student' AND batch_id='$batch_id'
    ORDER BY first_name
");

// Fetch distinct subjects from teacher allocations for this batch
$subjects = $conn->query("
    SELECT DISTINCT subject 
    FROM teacher_allocations 
    WHERE batch_id = '$batch_id'
");

// Fetch distinct exam types from marks table
$exam_types = $conn->query("SELECT DISTINCT exam_type FROM marks ORDER BY exam_type");

// Fetch marks if a student is selected
$marks = [];
if ($student_id > 0) {
    $sql = "SELECT 
                m.id, 
                CONCAT(stu.first_name, ' ', stu.last_name) AS student_name, 
                m.subject AS subject_name, 
                m.exam_type, 
                m.marks_obtained, 
                m.max_marks, 
                CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
            FROM marks m
            JOIN users stu ON stu.id = m.student_id
            JOIN users t ON t.id = m.teacher_id
            WHERE m.student_id = ?";

    $params = [$student_id];
    $types = "i";

    if (!empty($subject)) {
        $sql .= " AND m.subject = ?";
        $params[] = $subject;
        $types .= "s";
    }
    if (!empty($exam_type)) {
        $sql .= " AND m.exam_type = ?";
        $params[] = $exam_type;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $marks = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Marks</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<h2>📊 View Marks</h2>

<!-- Filter Form -->
<form method="GET">
    <label>Student:</label>
    <select name="student_id">
        <option value="">--Select--</option>
        <?php while($row = $students->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $student_id)?'selected':'' ?>>
                <?= htmlspecialchars($row['full_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Subject:</label>
    <select name="subject">
        <option value="">--All Subjects--</option>
        <?php while($row = $subjects->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['subject']) ?>" <?= ($row['subject'] == $subject)?'selected':'' ?>>
                <?= htmlspecialchars($row['subject']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Exam Type:</label>
    <select name="exam_type">
        <option value="">--All Exams--</option>
        <?php while($row = $exam_types->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['exam_type']) ?>" <?= ($row['exam_type'] == $exam_type)?'selected':'' ?>>
                <?= htmlspecialchars($row['exam_type']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Pass Marks:</label>
    <input type="number" name="pass_marks" step="0.01" value="<?= $pass_marks ?>" placeholder="Enter pass marks">

    <button type="submit">Filter</button>
</form>

<br>

<!-- Results -->
<?php if (!empty($marks)): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Student</th>
            <th>Subject</th>
            <th>Exam Type</th>
            <th>Marks</th>
            <th>Max Marks</th>
            <th>Uploaded By</th>
            <th>Status</th>
        </tr>
        <?php foreach($marks as $m): 
            $status = ($pass_marks > 0 && $m['marks_obtained'] < $pass_marks) ? "❌ Failed" : "✅ Passed";
        ?>
            <tr>
                <td><?= htmlspecialchars($m['student_name']) ?></td>
                <td><?= htmlspecialchars($m['subject_name']) ?></td>
                <td><?= htmlspecialchars($m['exam_type']) ?></td>
                <td><?= $m['marks_obtained'] ?></td>
                <td><?= $m['max_marks'] ?></td>
                <td><?= htmlspecialchars($m['teacher_name']) ?></td>
                <td><?= $status ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php elseif ($student_id): ?>
    <p>No marks found for the selected filters.</p>
<?php endif; ?>
</body>
</html>
