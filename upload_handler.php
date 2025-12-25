<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $subject_id = intval($_POST['subject_id']);

    $file = $_FILES['file'];
    $targetDir = "uploads/";
    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;

    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'png'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            $stmt = $conn->prepare("INSERT INTO question_papers (title, subject_id, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $title, $subject_id, $targetFilePath);

            if ($stmt->execute()) {
                echo "File uploaded successfully.";
                header("location=view_subject.php");
            } else {
                echo "Database error: " . $conn->error;
            }
        } else {
            echo "Error moving the uploaded file.";
        }
    } else {
        echo "Invalid file type.";
    }
}
?>
