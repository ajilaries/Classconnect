<?php
session_start();
include "config.php";

// ✅ Security: only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("⛔ Unauthorized");
}

// ✅ Get college_id
if (!isset($_SESSION['college_id'])) {
    die("⚠️ College ID missing. Log in again.");
}
$college_id = intval($_SESSION['college_id']);

// ✅ Handle Department Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['department_name'])) {
    $department_name = trim($_POST['department_name']);
    if (!empty($department_name)) {
        $stmt = $conn->prepare("INSERT INTO departments (department_name, college_id) VALUES (?, ?)");
        $stmt->bind_param("si", $department_name, $college_id);
        $stmt->execute();
        $stmt->close();
        header("Location: departments.php");
        exit;
    }
}

// ✅ Handle Deletion
if (isset($_GET['delete'])) {
    $dept_id = intval($_GET['delete']);

    // Optional: check if the department belongs to this college
    $check = $conn->prepare("SELECT id FROM departments WHERE id = ? AND college_id = ?");
    $check->bind_param("ii", $dept_id, $college_id);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
        // Delete department (cascades to batches, allocations, question papers if DB constraints set)
        $del = $conn->prepare("DELETE FROM departments WHERE id = ? AND college_id = ?");
        $del->bind_param("ii", $dept_id, $college_id);
        $del->execute();
        $del->close();
        header("Location: departments.php");
        exit;
    }
    $check->close();
}

// ✅ Fetch departments for this college
$departments = $conn->query("SELECT * FROM departments WHERE college_id = $college_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Departments</title>
<style>
/* ---------- Container ---------- */
.container {
    max-width: 700px;
    margin: 40px auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

/* ---------- Header ---------- */
header {
    background: #ffffff;
    border-bottom: 2px solid #e5e7eb;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

header h1 {
    margin: 0;
    font-size: 1.6rem;
    font-weight: bold;
    color: #276cdb;
}

header a {
    color: #276cdb;
    font-weight: 600;
    text-decoration: none;
    transition: opacity 0.2s ease;
}

header a:hover {
    opacity: 0.8;
}

/* ---------- Form ---------- */
form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

input[type="text"] {
    flex: 1;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    outline: none;
    transition: 0.2s ease;
}

input[type="text"]:focus {
    border-color: #276cdb;
    box-shadow: 0 0 4px rgba(39, 108, 219, 0.3);
}

button {
    background: #276cdb;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;
}

button:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
}

/* ---------- Departments List ---------- */
ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

ul li {
    background: #f9fafb;
    margin-bottom: 10px;
    padding: 12px 15px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s ease;
    border-left: 4px solid #276cdb;
}

ul li:hover {
    background: #eef3ff;
    transform: translateX(4px);
}

ul li a {
    text-decoration: none;
    color: #276cdb;
    font-weight: 500;
    margin-left: 10px;
}

ul li button.delete-btn {
    background: #ef4444;
    margin-left: 10px;
}

ul li button.delete-btn:hover {
    background: #b91c1c;
}

/* ---------- Section Headers ---------- */
h2 {
    margin-top: 30px;
    font-size: 1.3rem;
    color: #276cdb;
    border-bottom: 2px solid #276cdb;
    padding-bottom: 5px;
}

/* ---------- Footer ---------- */
footer {
    text-align: center;
    padding: 15px;
    font-size: 0.85rem;
    color: #276cdb;
    border-top: 1px solid #e5e7eb;
    margin-top: auto;
}
</style>
</head>
<body>

<header>
    <h1>Departments</h1>
    <a href="admindash.php">⬅ Back to Dashboard</a>
</header>

<div class="container">
    <form method="POST">
        <input type="text" name="department_name" placeholder="Enter new department name" required>
        <button type="submit">➕ Add</button>
    </form>

    <h2>Existing Departments</h2>
    <ul>
    <?php while ($row = $departments->fetch_assoc()) { ?>
        <li>
            <span><?= htmlspecialchars($row['department_name']); ?></span>
            <div>
                <a href="batches.php?department_id=<?= $row['id']; ?>">View Batches ➜</a>
                <a href="departments.php?delete=<?= $row['id']; ?>" 
                   onclick="return confirm('⚠️ Are you sure? All batches and data will be deleted!');" 
                   style="color: red;">🗑️ Delete</a>
            </div>
        </li>
    <?php } ?>
    </ul>
</div>

<footer>
    &copy; 2025 ClassConnect Admin Dashboard
</footer>

</body>
</html>
