<?php
session_start();
include "config.php";

// Only teachers
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$batch_id   = $_SESSION['batch_id'];

// Fetch teacher's subjects for this batch
$subjects = $conn->query("SELECT DISTINCT subject FROM teacher_allocations WHERE teacher_id='$teacher_id' AND batch_id='$batch_id'");

// Fetch students
$students_result = $conn->query("SELECT id, CONCAT(first_name,' ',last_name) AS full_name FROM users WHERE role='student' AND batch_id='$batch_id' ORDER BY first_name");

// Store students in array
$students = [];
while($row = $students_result->fetch_assoc()){
    $students[$row['id']] = $row['full_name'];
}

// Handle POST submission
$success = '';
$selected_subject = $_POST['subject'] ?? '';
$selected_exam_type = $_POST['exam_type'] ?? '';
$existing_marks = [];

if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($selected_subject) && !empty($selected_exam_type)){
    // Save/update marks
    $student_ids = $_POST['student_id'];
    $marks_obtained = $_POST['marks_obtained'];
    $max_marks = $_POST['max_marks'];

    foreach($student_ids as $i => $student_id){
        $marks = floatval($marks_obtained[$i]);
        $max = floatval($max_marks[$i]);

        $stmt = $conn->prepare("SELECT id FROM marks WHERE student_id=? AND subject=? AND exam_type=? AND batch_id=?");
        $stmt->bind_param("issi", $student_id, $selected_subject, $selected_exam_type, $batch_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows > 0){
            $row = $res->fetch_assoc();
            $stmt2 = $conn->prepare("UPDATE marks SET marks_obtained=?, max_marks=?, teacher_id=? WHERE id=?");
            $stmt2->bind_param("ddii", $marks, $max, $teacher_id, $row['id']);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $stmt2 = $conn->prepare("INSERT INTO marks (student_id, teacher_id, batch_id, subject, exam_type, marks_obtained, max_marks) VALUES (?,?,?,?,?,?,?)");
            $stmt2->bind_param("iiissdd", $student_id, $teacher_id, $batch_id, $selected_subject, $selected_exam_type, $marks, $max);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
    }
    $success = "Marks saved successfully!";
}

// Prefill existing marks for selected subject & exam
if(!empty($selected_subject) && !empty($selected_exam_type)){
    $stmt = $conn->prepare("SELECT student_id, marks_obtained, max_marks FROM marks WHERE batch_id=? AND subject=? AND exam_type=?");
    $stmt->bind_param("iss", $batch_id, $selected_subject, $selected_exam_type);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()){
        $existing_marks[$row['student_id']] = [
            'marks_obtained' => $row['marks_obtained'],
            'max_marks' => $row['max_marks']
        ];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Marks</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<h2>Upload Marks</h2>

<!-- View Marks Button -->
<a href="view_marks.php" class="button">View Marks</a>

<?php if($success) echo "<p style='color:green;'>$success</p>"; ?>

<form method="POST">
    <label>Subject:</label>
    <select name="subject" required>
        <option value="">--Select Subject--</option>
        <?php 
        $subjects->data_seek(0);
        while($row = $subjects->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['subject']) ?>" <?= ($row['subject'] == $selected_subject)?'selected':'' ?>>
                <?= htmlspecialchars($row['subject']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Exam Type:</label>
    <input type="text" name="exam_type" value="<?= htmlspecialchars($selected_exam_type) ?>" required>

    <label>Search Student:</label>
    <input type="text" id="studentSearch" placeholder="Type student name...">

    <table border="1" cellpadding="5">
        <tr>
            <th>Student</th><th>Marks Obtained</th><th>Max Marks</th>
        </tr>
        <?php foreach($students as $id => $name): ?>
        <tr>
            <td><?= htmlspecialchars($name) ?></td>
            <td>
                <input type="hidden" name="student_id[]" value="<?= $id ?>">
                <input type="number" step="0.01" name="marks_obtained[]" required
                       value="<?= $existing_marks[$id]['marks_obtained'] ?? '' ?>">
            </td>
            <td>
                <input type="number" step="0.01" name="max_marks[]" required
                       value="<?= $existing_marks[$id]['max_marks'] ?? '' ?>">
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <button type="submit">Save Marks</button>
</form>

<script>
// Student search filter
document.getElementById('studentSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('table tr');
    rows.forEach((row, i) => {
        if(i === 0) return; // header
        row.style.display = row.cells[0].textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>
