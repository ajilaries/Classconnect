<?php
session_start();
include "config.php";

// ✅ Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "⛔ Unauthorized!";
    exit;
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT); // ✅ Secure password
    $college_id = intval($_SESSION['college_id']); // Assuming admin belongs to a college

    // Insert into users
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, college_id) VALUES (?, ?, ?, ?, 'teacher', ?)");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $password, $college_id);

    if ($stmt->execute()) {
        $newTeacherId = $conn->insert_id;
        header("Location: allocate_teacher.php?teacher_id=" . $newTeacherId);
        exit;
    } else {
        $error = "❌ Failed to add teacher: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Teacher</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
        form { background: white; padding: 20px; border-radius: 10px; max-width: 500px; margin: auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
        button { margin-top: 20px; width: 100%; background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; margin-bottom: 10px; text-align: center; }
    </style>
</head>
<body>
    <form method="POST">
        <h1>➕ Add Teacher</h1>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <label>First Name:</label>
        <input type="text" name="first_name" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Add Teacher</button>
    </form>
</body>
</html>
