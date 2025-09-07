<?php
session_start();
include "config.php";

// Check if user is super admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    echo "⛔ Unauthorized access!";
    exit;
}

// Fetch all colleges
$colleges = $conn->query("SELECT college_code, college_name FROM colleges");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <h1>Assign Admin</h1>
        <a href="super_admin_dashboard.php" class="logout-btn">⬅ Back</a>
    </header>

    <main class="form-container">
        <form action="process_assign_admin.php" method="POST" class="card">
            <h2>Create New Admin</h2>

            <label for="name">Full Name</label>
            <input type="text" name="name" required>

            <label for="email">Email</label>
            <input type="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" required>

            <label for="college_code">Assign to College</label>
            <select name="college_code" required>
                <option value="">-- Select College --</option>
                <?php while($row = $colleges->fetch_assoc()) { ?>
                    <option value="<?= $row['college_code'] ?>">
                        <?= $row['college_name'] ?> (<?= $row['college_code'] ?>)
                    </option>
                <?php } ?>
            </select>

            <button type="submit" class="btn">Create Admin</button>
        </form>
    </main>
</body>
</html>
