<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Validate new password
    if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/", $new_password)) {
        die("Password must be at least 6 characters, include 1 uppercase, 1 lowercase, and 1 number.");
    }

    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password in DB
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Password reset successful! Please log in.'); window.location='login.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
