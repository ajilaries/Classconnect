<?php
session_start();
include "config.php";

// Security: only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("⛔ Unauthorized");
}

$college_id = intval($_SESSION['college_id']);

// Get department_id
if (!isset($_GET['department_id'])) {
    die("⚠️ Department not specified.");
}
$department_id = intval($_GET['department_id']);

// Fetch department name
$dept_stmt = $conn->prepare("SELECT department_name FROM departments WHERE id = ? AND college_id = ?");
$dept_stmt->bind_param("ii", $department_id, $college_id);
$dept_stmt->execute();
$dept_res = $dept_stmt->get_result();
if ($dept_res->num_rows === 0) {
    die("⚠️ Department not found.");
}
$department = $dept_res->fetch_assoc();
$dept_stmt->close();

// Handle Batch Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batch_name'])) {
    $batch_name = trim($_POST['batch_name']);
    if (!empty($batch_name)) {
        $class_code = strtoupper(substr(md5(uniqid()), 0, 6));
        $stmt = $conn->prepare("INSERT INTO batches (department_id, batch_name, class_code) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $department_id, $batch_name, $class_code);
        $stmt->execute();
        $stmt->close();
        header("Location: batches.php?department_id=$department_id");
        exit;
    }
}

// Handle Batch Deletion
if (isset($_GET['delete'])) {
    $batch_id = intval($_GET['delete']);

    // Optional: confirm batch belongs to this department
    $check = $conn->prepare("SELECT id FROM batches WHERE id = ? AND department_id = ?");
    $check->bind_param("ii", $batch_id, $department_id);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) {
        // Delete all related data first if necessary
        // Example: delete teacher allocations
        $conn->query("DELETE FROM teacher_allocations WHERE batch_id = $batch_id");
        // Example: delete students
        $conn->query("DELETE FROM users WHERE batch_id = $batch_id AND role = 'student'");
        // Example: delete question papers
        $conn->query("DELETE FROM question_papers WHERE batch_id = $batch_id");
        // Finally delete batch
        $del = $conn->prepare("DELETE FROM batches WHERE id = ? AND department_id = ?");
        $del->bind_param("ii", $batch_id, $department_id);
        $del->execute();
        $del->close();
        header("Location: batches.php?department_id=$department_id");
        exit;
    }
    $check->close();
}

// Fetch batches for this department
$batches_stmt = $conn->prepare("SELECT * FROM batches WHERE department_id = ?");
$batches_stmt->bind_param("i", $department_id);
$batches_stmt->execute();
$batches_res = $batches_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Batches - <?php echo htmlspecialchars($department['department_name']); ?></title>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f7f9fc;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 700px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
h1 {
    color: #276cdb;
}
form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
input[type="text"] {
    flex: 1;
    padding: 10px;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
}
button {
    background: #276cdb;
    color: #fff;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    cursor: pointer;
}
button:hover {
    background: #1d4ed8;
}
ul {
    list-style: none;
    padding: 0;
}
ul li {
    background: #f9fafb;
    margin-bottom: 10px;
    padding: 12px 15px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 4px solid #276cdb;
    transition: all 0.2s ease;
}
ul li:hover {
    background: #eef3ff;
}
ul li a.delete-btn {
    color: #ef4444;
    text-decoration: none;
    font-weight: bold;
}
ul li a.delete-btn:hover {
    color: #b91c1c;
}
a.back {
    display: inline-block;
    margin-top: 20px;
    color: #276cdb;
    text-decoration: none;
}
a.back:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">
    <h1>Batches for <?php echo htmlspecialchars($department['department_name']); ?></h1>

    <form method="POST">
        <input type="text" name="batch_name" placeholder="New Batch" required>
        <button type="submit">➕ Create Batch</button>
    </form>

    <h2>Existing Batches</h2>
    <ul>
    <?php while ($batch = $batches_res->fetch_assoc()) { ?>
        <li>
            <span><?= htmlspecialchars($batch['batch_name']); ?> - Class Code: <?= htmlspecialchars($batch['class_code']); ?></span>
            <a href="batches.php?department_id=<?= $department_id ?>&delete=<?= $batch['id']; ?>" class="delete-btn" 
               onclick="return confirm('⚠️ Are you sure? All students, teacher allocations, and question papers will be deleted!');">🗑️ Delete</a>
        </li>
    <?php } ?>
    </ul>

    <a href="departments.php" class="back">⬅ Back to Departments</a>
</div>

</body>
</html>
