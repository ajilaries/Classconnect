<?php
session_start();
include "config.php";

// ✅ Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("⛔ Access Denied! Only admins can view this page.");
}

// Fetch all teachers
$teachers = [];
$teacherQuery = "SELECT id, first_name, last_name, email FROM users WHERE role='teacher' ORDER BY first_name ASC";
$teacherResult = $conn->query($teacherQuery);
if ($teacherResult->num_rows > 0) {
    while($row = $teacherResult->fetch_assoc()) {
        $teachers[] = $row;
    }
}

// Handle POST request to send message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $target_teacher = intval($_POST['teacher_id'] ?? 0);

    if (empty($message) || !$target_teacher) {
        echo "<script>alert('⚠️ Please enter a message and select a teacher!');</script>";
    } else {
        $filename = null;

        // Optional file upload
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

        // Insert notification
        $stmt = $conn->prepare("
            INSERT INTO notifications (message, file_path, created_at, target_role, target_user_id)
            VALUES (?, ?, NOW(), 'teacher', ?)
        ");
        $stmt->bind_param("ssi", $message, $filename, $target_teacher);

        if ($stmt->execute()) {
            echo "<script>alert('✅ Message sent to teacher successfully!'); window.location.href='admin_requests.php';</script>";
            exit;
        } else {
            die("❌ Failed to save notification! Error: " . $stmt->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📤 Send Notification to Teacher</title>
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 30px; }
form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.1); max-width: 500px; margin: auto; }
select, textarea, input[type="file"] { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
button { padding: 10px 20px; background: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #218838; }
</style>
</head>
<body>

<h2>📤 Send Notification to a Teacher</h2>
<form method="post" enctype="multipart/form-data">
    <label>Select Teacher:</label>
    <select name="teacher_id" required>
        <option value="">-- Choose a Teacher --</option>
        <?php foreach($teachers as $t): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Message:</label>
    <textarea name="message" rows="5" placeholder="Enter your message..." required></textarea>

    <label>Attach File (optional):</label>
    <input type="file" name="file">

    <button type="submit">Send Notification</button>
</form>

</body>
</html>
