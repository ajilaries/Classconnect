<?php
include "config.php";

$teacher_id = intval($_POST['teacher_id']);
$batch_id   = intval($_POST['batch_id']);

$stmt = $conn->prepare("SELECT subject FROM teacher_allocations WHERE teacher_id = ? AND batch_id = ?");
$stmt->bind_param("ii", $teacher_id, $batch_id);
$stmt->execute();
$result = $stmt->get_result();

$subject = '';
if ($row = $result->fetch_assoc()) {
    $subject = $row['subject'];
}

echo $subject; // returns the subject string
?>
