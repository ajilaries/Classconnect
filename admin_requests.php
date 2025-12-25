<?php
session_start();
include "config.php";

// ✅ Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("⛔ Access Denied! Only admins can view this page.");
}

$admin_id = $_SESSION['user_id'];

// --- Handle sending notifications to all teachers ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_all_teachers'])) {
    $message = trim($_POST['message_all_teachers'] ?? '');
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO notifications (message, target_role, created_at, creator_id) VALUES (?, 'teacher', NOW(), ?)");
        $stmt->bind_param("si", $message, $admin_id);
        $stmt->execute();
        $stmt->close();
        $success_teacher_all = "✅ Notification sent to all teachers.";
    }
}

// --- Handle sending notifications to a specific teacher ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_specific_teacher'])) {
    $message = trim($_POST['message_teacher'] ?? '');
    $teacher_id = intval($_POST['teacher_id'] ?? 0);
    if (!empty($message) && $teacher_id > 0) {
        $stmt = $conn->prepare("INSERT INTO notifications (message, target_role, target_user_id, created_at, creator_id) VALUES (?, 'teacher', ?, NOW(), ?)");
        $stmt->bind_param("sii", $message, $teacher_id, $admin_id);
        $stmt->execute();
        $stmt->close();
        $success_specific_teacher = "✅ Notification sent to selected teacher.";
    }
}

// --- Handle sending notifications to students ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_students'])) {
    $message = trim($_POST['message_students'] ?? '');
    $department_id = intval($_POST['department_id'] ?? 0);
    $batch_id = intval($_POST['batch_id'] ?? 0);

    if (!empty($message)) {
        // Target students in the selected department/batch or all if batch_id = 0
        $query = "INSERT INTO notifications (message, target_role, target_user_id, college_id, batch_id, created_at, creator_id)
                  SELECT ?, 'student', id, college_id, ?, NOW(), ? 
                  FROM users 
                  WHERE role='student' AND college_id=?";
        
        if($department_id > 0){
            $query .= " AND department_id = $department_id";
        }
        if($batch_id > 0){
            $query .= " AND batch_id = $batch_id";
        }

        $stmt = $conn->prepare($query);
        $stmt->bind_param("siii", $message, $batch_id, $admin_id, $_SESSION['college_id']);
        $stmt->execute();
        $stmt->close();
        $success_students = "✅ Notification sent to selected students.";
    }
}

// --- Fetch teacher requests/messages ---
$teacher_requests = $conn->query("
    SELECT n.id, n.message, n.file_path, n.created_at, u.first_name, u.last_name
    FROM notifications n
    JOIN users u ON n.creator_id = u.id
    WHERE n.target_role = 'admin'
    ORDER BY n.created_at DESC
");

// --- Fetch all teachers for dropdown ---
$teachers_result = $conn->query("SELECT id, first_name, last_name FROM users WHERE role='teacher' ORDER BY first_name ASC");

// --- Fetch all departments for dropdown ---
$departments_result = $conn->query("SELECT id, department_name FROM departments ORDER BY department_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📥 Admin Requests & Notifications</title>
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 30px; }
h2 { margin-bottom: 20px; text-align: center; }
button.menu-btn { padding: 12px 20px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; background: #007bff; color: #fff; font-size: 14px; }
button.menu-btn:hover { background: #0056b3; }
.section { display: none; background: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
textarea, select { width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }
.success { color: green; font-weight: bold; margin-bottom: 10px; }
.notification { background: #f9f9f9; padding: 10px; margin-bottom: 10px; border-left: 5px solid #007bff; border-radius: 5px; }
.file-link { color: #28a745; font-weight: bold; }
.time { font-size: 12px; color: #666; }
</style>
<script>
function showSection(id){
    document.querySelectorAll('.section').forEach(sec => sec.style.display='none');
    document.getElementById(id).style.display = 'block';
}

// AJAX to fetch batches for selected department
function fetchBatches(deptId){
    let batchSelect = document.getElementById('batch_id');
    batchSelect.innerHTML = '<option value="0">All Batches</option>'; // default
    if(deptId > 0){
        fetch('get_batches.php?department_id=' + deptId)
            .then(res => res.json())
            .then(data => {
                data.forEach(b => {
                    let option = document.createElement('option');
                    option.value = b.id;
                    option.text = b.batch_name;
                    batchSelect.add(option);
                });
            });
    }
}
</script>
</head>
<body>

<h2>📥 Admin Requests & Notifications</h2>

<!-- Buttons to show sections -->
<div style="text-align:center;">
    <button class="menu-btn" onclick="showSection('view_teacher_requests')">👨‍🏫 View Teacher Requests</button>
    <button class="menu-btn" onclick="showSection('notify_teachers')">✉️ Notify Teachers</button>
    <button class="menu-btn" onclick="showSection('notify_students')">🎓 Notify Students</button>
</div>

<!-- Section 1: Teacher Requests -->
<div id="view_teacher_requests" class="section">
    <h3>📋 Messages sent by Teachers</h3>
    <?php if($teacher_requests->num_rows > 0): ?>
        <?php while($row = $teacher_requests->fetch_assoc()): ?>
            <div class="notification">
                <p><strong>👨‍🏫 <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></strong></p>
                <p><?= htmlspecialchars($row['message']) ?></p>
                <p class="time">🕒 <?= htmlspecialchars($row['created_at']) ?></p>
                <?php if(!empty($row['file_path'])): ?>
                    <p><a href="<?= $row['file_path'] ?>" target="_blank" class="file-link">📎 Attached File</a></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No teacher messages found.</p>
    <?php endif; ?>
</div>

<!-- Section 2: Notify Teachers -->
<div id="notify_teachers" class="section">
    <h3>✉️ Send Notification to Teachers</h3>
    <!-- All teachers -->
    <form method="post">
        <?php if(!empty($success_teacher_all)) echo "<p class='success'>$success_teacher_all</p>"; ?>
        <textarea name="message_all_teachers" placeholder="Message to all teachers..." required></textarea>
        <button type="submit" name="send_all_teachers">Send to All Teachers</button>
    </form>
    <hr>
    <!-- Specific teacher -->
    <form method="post">
        <?php if(!empty($success_specific_teacher)) echo "<p class='success'>$success_specific_teacher</p>"; ?>
        <select name="teacher_id" required>
            <option value="">Select Teacher</option>
            <?php while($t = $teachers_result->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['first_name'].' '.$t['last_name']) ?></option>
            <?php endwhile; ?>
        </select>
        <textarea name="message_teacher" placeholder="Message to selected teacher..." required></textarea>
        <button type="submit" name="send_specific_teacher">Send to Teacher</button>
    </form>
</div>

<!-- Section 3: Notify Students -->
<div id="notify_students" class="section">
    <h3>🎓 Send Notification to Students</h3>
    <?php if(!empty($success_students)) echo "<p class='success'>$success_students</p>"; ?>
    <form method="post">
        <select name="department_id" onchange="fetchBatches(this.value)">
            <option value="0">All Departments</option>
            <?php while($d = $departments_result->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['department_name']) ?></option>
            <?php endwhile; ?>
        </select>
        <select name="batch_id" id="batch_id">
            <option value="0">All Batches</option>
        </select>
        <textarea name="message_students" placeholder="Message to students..." required></textarea>
        <button type="submit" name="send_students">Send to Students</button>
    </form>
</div>

</body>
</html>
