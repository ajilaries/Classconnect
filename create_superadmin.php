<?php
include "config.php";

$email = "godofclassconnect@college.com";
$password = "GODOFCC@60"; // choose a secure password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$first_name = "Admin";
$last_name  = "God";
$role = "super_admin";

// Insert into users table
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $first_name, $last_name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "✅ Super Admin account created successfully!";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>
