<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Redirect to reset page with email (you can use token later for security)
        header("Location: reset_password.php?email=" . urlencode($email));
        exit();
    } else {
        echo "<script>alert('Email not found'); window.location='forget_password.php';</script>";
    }
}
