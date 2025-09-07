<?php
session_start();

// ✅ Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: login.html");
    exit;
}

// ✅ Prevent cached version from being shown after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="superstyles.css">
</head>
<body>
    <header class="header">
        <h1>Super Admin Dashboard</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <main class="dashboard">
        <div class="card" onclick="window.location.href='manage_colleges.php'">
            <h2>Manage Colleges</h2>
            <p>Add, edit, and delete colleges with unique codes.</p>
        </div>

        <div class="card" onclick="window.location.href='assign_admin.php'">
            <h2>Assign Admins</h2>
            <p>Create admins and link them to colleges.</p>
        </div>

        <div class="card" onclick="window.location.href='system_settings.php'">
            <h2>System Settings</h2>
            <p>Update platform settings and configurations.</p>
        </div>
    </main>
</body>
</html>
