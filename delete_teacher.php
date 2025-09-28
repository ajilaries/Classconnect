<?php
session_start();
include "config.php";

// ✅ Ensure user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('⛔ Access denied! Only admins can delete teachers.'); window.location.href='questionpapers.php';</script>";
    exit();
}

// ✅ Check if teacher ID is passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('⚠️ Invalid teacher ID.'); window.location.href='questionpapers.php';</script>";
    exit();
}

$teacher_id = intval($_GET['id']);

// ✅ Delete query
$stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
$stmt->bind_param("i", $teacher_id);

if ($stmt->execute()) {
    echo "<script>alert('✅ Teacher deleted successfully.'); window.location.href='questionpapers.php';</script>";
} else {
    echo "<script>alert('❌ Failed to delete teacher. Try again later.'); window.location.href='questionpapers.php';</script>";
}
?>
