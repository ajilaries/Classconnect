<?php
session_start();
include "config.php";

// ✅ Only allow teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    die("⛔ Access Denied! Only teachers can send notifications.");
}

$user_id     = $_SESSION['user_id'];
$first_name  = $_SESSION['first_name'] ?? '';
$last_name   = $_SESSION['last_name'] ?? '';
$teacher_name = trim("$first_name $last_name");

// ✅ Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');

    if (empty($message)) {
        echo "<script>alert('⚠️ Message cannot be empty!'); history.back();</script>";
        exit;
    }

    // Optional file upload
    $filename = null;
    if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $originalName = basename($_FILES['file']['name']);
        $filename     = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $originalName);
        $targetPath   = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            die("❌ File upload failed!");
        }
    }

    // ✅ Prepare notification
    $full_message = "{$teacher_name}: {$message}";
    $target_role  = 'admin'; // only admins can see
    $creator_id   = $user_id;

    $stmt = $conn->prepare("
        INSERT INTO notifications (message, file_path, created_at, target_role, creator_id)
        VALUES (?, ?, NOW(), ?, ?)
    ");
    $stmt->bind_param("sssi", $full_message, $filename, $target_role, $creator_id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Notification sent to admin successfully!'); window.location.href='notification.php';</script>";
        exit;
    } else {
        die("❌ Failed to save notification! Error: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📨 Send Notification to Admin</title>
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 30px; }
form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1); max-width: 500px; margin: auto; }
textarea { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
input[type="file"] { margin-bottom: 15px; }
button { padding: 10px 20px; background: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #218838; }
h2 { text-align: center; margin-bottom: 20px; }
</style>
</head>
<body>

<h2>📨 Send Notification to Admin</h2>
<form method="post" enctype="multipart/form-data">
    <label>Message:</label>
    <textarea name="message" rows="5" placeholder="Enter your message..." required></textarea>

    <label>Attach File (optional):</label>
    <input type="file" name="file">

    <button type="submit">Send Notification</button>
</form>

</body>
</html>
