<?php
include "config.php";
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    echo "⛔ Unauthorized access";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user role from DB
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Role check: Only allow teachers
if (!$user || $user['role'] !== 'teacher') {
    echo "<script>alert('🚫 You do not have permission to delete files.'); window.location.href='timetable_uploaded.php';</script>";
    exit;
}

// Proceed only if POST request contains valid data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['file_path'])) {
    $id = intval($_POST['id']);
    $file_path = $_POST['file_path'];

    // Delete file from server
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Delete entry from database
    $stmt = $conn->prepare("DELETE FROM timetable WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to timetable view
header("Location: timetable_uploaded.php");
exit;
?>
