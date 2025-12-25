<?php
session_start();
include "config.php";

// ✅ Ensure admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("⛔ Unauthorized!");
}

// 1️⃣ Get selected department and batch
$departmentId = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;
$selectedBatch = isset($_GET['batch_id']) ? intval($_GET['batch_id']) : null;

// 2️⃣ Fetch all departments
$departments = $conn->query("SELECT * FROM departments")->fetch_all(MYSQLI_ASSOC);

// 3️⃣ Fetch batches under selected department
$batches = [];
if ($departmentId) {
    $stmt = $conn->prepare("SELECT * FROM batches WHERE department_id = ?");
    $stmt->bind_param("i", $departmentId);
    $stmt->execute();
    $batches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// 4️⃣ Fetch students and teachers if a batch is selected
$students = [];
$teachers = [];

if ($selectedBatch) {
    // Fetch students in this batch
    $stmt = $conn->prepare("SELECT * FROM users WHERE batch_id = ? AND role = 'student'");
    $stmt->bind_param("i", $selectedBatch);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch teachers allocated to this batch along with subject
    $stmt = $conn->prepare("
        SELECT u.id, u.first_name, u.last_name, u.email, ta.subject
        FROM users u
        JOIN teacher_allocations ta ON u.id = ta.teacher_id
        WHERE u.role = 'teacher' AND ta.batch_id = ?
    ");
    $stmt->bind_param("i", $selectedBatch);
    $stmt->execute();
    $teachers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Classes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { display: flex; flex-direction: column; gap: 20px; }
        .list { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .item { background: #f4f4f4; padding: 10px 15px; border-radius: 5px; text-decoration: none; color: #333; }
        .item:hover { background: #ddd; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #eee; }
        h1, h2 { margin-bottom: 10px; }
        .back-btn { display: inline-block; margin-bottom: 15px; background: #007bff; color: #fff; padding: 8px 12px; border-radius: 4px; text-decoration: none; }
        .back-btn:hover { background: #0056b3; }
        .action-btn { padding: 5px 10px; margin: 0 3px; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .edit-btn { background: #ffc107; color: #000; }
        .delete-btn { background: #dc3545; color: #fff; }
    </style>
    <script>
        function confirmDelete(userId) {
            if (confirm("Are you sure you want to remove this user?")) {
                window.location.href = "remove_user.php?id=" + userId + "&redirect=" + encodeURIComponent(window.location.href);
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Classes</h1>

    <!-- Departments -->
    <?php if (!$departmentId): ?>
        <h2>Select Department</h2>
        <div class="list">
            <?php foreach ($departments as $dept): ?>
                <a class="item" href="?department_id=<?= $dept['id'] ?>"><?= htmlspecialchars($dept['department_name']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Batches -->
    <?php if ($departmentId && !$selectedBatch): ?>
        <a class="back-btn" href="classes.php">⬅ Back to Departments</a>
        <h2>Available Batches</h2>
        <div class="list">
            <?php foreach ($batches as $batch): ?>
                <a class="item" href="?department_id=<?= $departmentId ?>&batch_id=<?= $batch['id'] ?>"><?= htmlspecialchars($batch['batch_name']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Students & Teachers -->
    <?php if ($selectedBatch): ?>
        <a class="back-btn" href="?department_id=<?= $departmentId ?>">⬅ Back to Batches</a>

        <!-- Students -->
        <h2>Students in Batch</h2>
        <?php if ($students): ?>
            <table>
                <tr>
                    <th>Admission No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Register No</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['admission_no']) ?></td>
                        <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><?= htmlspecialchars($s['register_no']) ?></td>
                        <td>
                            <a class="action-btn edit-btn" href="edit_user.php?id=<?= $s['id'] ?>">✏️ Edit</a>
                            <button class="action-btn delete-btn" onclick="confirmDelete(<?= $s['id'] ?>)">🗑️ Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No students found in this batch.</p>
        <?php endif; ?>

        <!-- Teachers -->
        <h2>Teachers in Batch</h2>
        <?php if ($teachers): ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($teachers as $t): ?>
                    <tr>
                        <td><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?></td>
                        <td><?= htmlspecialchars($t['email']) ?></td>
                        <td><?= htmlspecialchars($t['subject'] ?? 'N/A') ?></td>
                        <td>
                            <a class="action-btn edit-btn" href="edit_user.php?id=<?= $t['id'] ?>">✏️ Edit</a>
                            <button class="action-btn delete-btn" onclick="confirmDelete(<?= $t['id'] ?>)">🗑️ Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No teachers found in this batch.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
