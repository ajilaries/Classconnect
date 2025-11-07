<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check super admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
        echo "⛔ Unauthorized access!";
        exit;
    }

    // Collect data
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $college_id = intval($_POST['college_id']); // ✅ Use college_id now

    // Validate college exists
    $stmt = $conn->prepare("SELECT college_id FROM colleges WHERE college_id = ?");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("❌ Invalid college selected!");
    }

    // Insert admin
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, college_id) 
                            VALUES (?, ?, ?, ?, 'admin', ?)");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $password, $college_id);

    if ($stmt->execute()) {
        echo "✅ Admin created successfully!";
        // Optionally redirect:
        // header("Location: assign_admin.php?success=1");
        // exit;
    } else {
        echo "❌ Failed to create admin: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
