<?php
include "config.php";
session_start();

$batch_id = $_SESSION['batch_id'] ?? null;
$teacher_id = $_POST['teacher_id'] ?? null;

if ($batch_id && $teacher_id) {
    $sql = "SELECT s.id, s.subject_name
            FROM subjects s
            INNER JOIN teacher_allocation ta ON s.id = ta.subject_id
            WHERE ta.batch_id = ? AND ta.teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $batch_id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">-- Select Subject --</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="'.$row['id'].'">'.htmlspecialchars($row['subject_name']).'</option>';
    }
    $stmt->close();
}
?>
