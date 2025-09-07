<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ✅ Check if logged-in user is superadmin
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
        echo "⛔ Unauthorized access!";
        exit;
    }

    // ✅ Collect input data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $college_code = trim($_POST['college_code']);

    // ✅ Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, college_code) 
                            VALUES (?, ?, ?, ?, 'admin', ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $college_code);

    if ($stmt->execute()) {
        header("Location: assign_admin.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
