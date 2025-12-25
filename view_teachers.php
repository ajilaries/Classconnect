<?php
session_start();
include "config.php";

// ✅ Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "⛔ Unauthorized!";
    exit;
}

$college_id = $_SESSION['college_id']; // from login session

$stmt = $conn->prepare("SELECT id, first_name, last_name, email FROM users WHERE college_id = ? AND role = 'admin'");
$stmt->bind_param("i", $college_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Teachers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background-color: #f8f9fa;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 6px 12px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>

<h2>👨‍🏫 Teachers List</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['id']); ?></td>
        <td><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>

            <a class="btn" href="delete_teacher.php?id=<?= $row['teacher_id']; ?>" 
               onclick="return confirm('Are you sure you want to delete this teacher?');">
               ❌ Delete
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
