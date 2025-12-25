<?php
session_start();
include "config.php";

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "❌ Unauthorized access.";
    exit;
}

// Check if form was submitted
if (isset($_POST['upload']) && isset($_FILES['file'])) {
    $title = trim($_POST['title']);
    $file_type = $_POST['file_type'];
    $subject_id = intval($_POST['subject_id']);
    $user_id = $_SESSION['user_id'];

    $file = $_FILES['file'];
    $fileName = basename($file['name']);
    $uploadDir = "uploads/";

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $targetFilePath = $uploadDir . time() . "_" . $fileName;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO files (title, file_path, file_type, subject_id, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $title, $targetFilePath, $file_type, $subject_id, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('✅ File uploaded successfully!'); window.location.href='upload_file_admin.php';</script>";
        } else {
            echo "❌ Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ Failed to upload file.";
    }
} else {
    echo "⚠️ Form not submitted properly.";
}
?>
u