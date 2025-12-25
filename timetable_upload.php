<?php
session_start();
include "config.php";

// ✅ Only allow teachers to upload
if (!isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['batch_id'], $_SESSION['college_id'])) {
    die("⛔ Login session missing. Please login again.");
}

if ($_SESSION['role'] !== 'teacher') {
    die("⛔ Unauthorized! Only teachers can upload timetables.");
}

$uploaded_by  = $_SESSION['user_id'];
$teacher_name = $_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '');
$college_id   = $_SESSION['college_id'];
$batch_id     = $_SESSION['batch_id']; // batch assigned at login

if (isset($_POST['upload'])) {

    // ✅ Sanitize filename input
    $filename = trim($_POST['filename']);
    if (empty($filename)) {
        die("⛔ Please provide a filename.");
    }

    // ✅ Check uploaded file
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        die("❌ File upload error: " . ($_FILES['file']['error'] ?? 'No file selected.'));
    }

    $fileTmp      = $_FILES['file']['tmp_name'];
    $originalName = $_FILES['file']['name'];
    $extension    = pathinfo($originalName, PATHINFO_EXTENSION);

    $uploadDir = "uploads/timetables/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $newFileName = uniqid('tt_', true) . '.' . $extension;
    $filePath    = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmp, $filePath)) {
        // ✅ Insert into database with teacher info + batch
        $stmt = $conn->prepare("
            INSERT INTO timetable (filename, file_path, uploaded_by, uploaded_by_name, college_id, batch_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssii", $filename, $filePath, $uploaded_by, $teacher_name, $college_id, $batch_id);

        if ($stmt->execute()) {
            header("Location: timetable_uploaded.php?success=1");
            exit;
        } else {
            die("❌ Database Error: " . $stmt->error);
        }
    } else {
        die("❌ File move failed. Check folder permissions.");
    }
}
?>
