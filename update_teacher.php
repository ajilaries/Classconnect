<?php
session_start();
include "config.php";

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('⛔ Access Denied! Only admins can update teachers.'); window.location.href='corner_admin.php';</script>";
    exit();
}

// Check if POST data exists
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $subject = trim($_POST['subject']);
    $department = trim($_POST['department']);
    $email = trim($_POST['email']);

    // Basic validation (optional)
    if ($name === '' || $subject === '' || $department === '' || $email === '') {
        echo "<script>alert('⚠️ All fields are required.'); window.history.back();</script>";
        exit();
    }

    // Update query
    $stmt = $conn->prepare("UPDATE teachers SET name=?, subject=?, department=?, email=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $subject, $department, $email, $id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Teacher updated successfully!'); window.location.href='corner_admin.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to update teacher. Try again later.'); window.location.href='corner_admin.php';</script>";
    }
} else {
    echo "<script>alert('Invalid Request'); window.location.href='corner_admin.php';</script>";
}
?>
