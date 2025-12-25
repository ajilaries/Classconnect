<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Session values
$role          = $_SESSION['role'] ?? null;
$department_id = $_SESSION['department_id'] ?? null;
$batch_id      = $_SESSION['batch_id'] ?? null;
$college_id    = $_SESSION['college_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date  = $_POST['event_date'] ?: null;
    $event_time  = $_POST['event_time'] ?: null;
    $category    = $_POST['category'] ?? 'General';
    $created_by  = $_SESSION['user_id'];

    // Determine visibility
    $visibility = $_POST['visibility'] ?? (($role === 'admin') ? 'all' : 'batch');

    // Set department_id and batch_id
    if ($role === 'admin') {
        if ($visibility === 'department') {
            $department_id = !empty($_POST['department_id']) ? intval($_POST['department_id']) : null;
            if (!$department_id) die("❌ Please select a department for department-specific events.");
        } else {
            $department_id = null;
        }
        $batch_id = !empty($_POST['batch_id']) ? intval($_POST['batch_id']) : null;
    } else {
        // teacher/student
        if (!$department_id) die("❌ Your department is not set. Contact admin.");
        if (!$batch_id) die("❌ Your batch is not set. Contact admin.");
    }

    // Handle file upload
    $attachment_path = null;
    if (!empty($_FILES['attachment']['name'])) {
        $allowed_ext = ['jpg','jpeg','png','pdf','doc','docx','ppt','pptx','xls','xlsx'];
        $upload_dir  = "uploads/events/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_ext)) die("❌ Invalid file type.");

        $file_name   = time() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($_FILES['attachment']['name']));
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $attachment_path = $target_file;
        }
    }

    // Insert into events table
    $sql = "INSERT INTO events (title, description, event_date, event_time, category, visibility, department_id, college_id, batch_id, created_by, attachment, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssiiiss",
        $title,
        $description,
        $event_date,
        $event_time,
        $category,
        $visibility,
        $department_id,
        $college_id,
        $batch_id,
        $created_by,
        $attachment_path
    );

    if ($stmt->execute()) {
        header("Location: events.php?success=1");
        exit;
    } else {
        echo "❌ Error creating event: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event / Notice</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f8fc; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; padding: 20px; }
        .form-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h2 { text-align: center; margin-bottom: 15px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; border-radius: 6px; border: 1px solid #ccc; }
        button { margin-top: 15px; width: 100%; background: #28a745; color: white; border: none; padding: 10px; font-size: 16px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>➕ Create Event / Notice</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Title</label>
        <input type="text" name="title" required>

        <label>Description</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Date</label>
        <input type="date" name="event_date">

        <label>Time</label>
        <input type="time" name="event_time">

        <?php if ($role === 'admin'): ?>
            <label>Visibility</label>
            <select name="visibility" required>
                <option value="all">🌍 Global (All Students & Teachers)</option>
                <option value="department">🏫 Department Specific</option>
            </select>

            <label>Department</label>
            <select name="department_id">
                <option value="">-- Select Department --</option>
                <?php
                $deptResult = $conn->query("SELECT id, name FROM departments WHERE college_id = $college_id");
                while ($dept = $deptResult->fetch_assoc()) {
                    echo "<option value='{$dept['id']}'>{$dept['name']}</option>";
                }
                ?>
            </select>

            <label>Batch (optional)</label>
            <select name="batch_id">
                <option value="">-- Select Batch --</option>
                <?php
                $batchResult = $conn->query("SELECT id, name FROM batches WHERE college_id = $college_id");
                while ($batch = $batchResult->fetch_assoc()) {
                    echo "<option value='{$batch['id']}'>{$batch['name']}</option>";
                }
                ?>
            </select>
        <?php else: ?>
            <input type="hidden" name="visibility" value="batch">
            <input type="hidden" name="department_id" value="<?= htmlspecialchars($department_id); ?>">
            <input type="hidden" name="batch_id" value="<?= htmlspecialchars($batch_id); ?>">
        <?php endif; ?>

        <label>Attachment (Optional)</label>
        <input type="file" name="attachment">

        <button type="submit">Create Event</button>
    </form>
</div>
</body>
</html>
