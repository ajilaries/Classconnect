<?php
session_start();

// ✅ Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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
    <title>Admin Dashboard</title>
    <style>
        /* ---------- Base Layout ---------- */
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #dfe6ef);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #276cdb;
            margin: 0;
            letter-spacing: 1px;
        }

        header a {
            color: #e11d48;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            padding: 6px 12px;
            border: 1px solid #e11d48;
            border-radius: 8px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        header a:hover {
            background: #e11d48;
            color: white;
        }

        /* ---------- Dashboard Grid ---------- */
        main {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
        }

        /* ---------- Card Style ---------- */
        .card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }

        .card h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #111827;
            margin: 0 0 12px;
        }

        .card p {
            font-size: 1rem;
            color: #4b5563;
            line-height: 1.5;
            margin: 0;
        }

        /* ---------- Footer ---------- */
        footer {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            color: #276cdb;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <header>
        <h1>ClassConnect Admin</h1>
        <a href="logout.php">Logout</a>
    </header>

    <!-- Dashboard Content -->
    <main>
        <a href="departments.php" class="card">
            <h2>🏫 Departments</h2>
            <p>Create new departments and manage batches for each department.</p>
        </a>

        <a href="classes.php" class="card">
            <h2>📚 Classes</h2>
            <p>View all classes under each department and manage students & teachers.</p>
        </a>

        <a href="allocate_teacher.php" class="card">
            <h2>👩‍🏫 Teachers</h2>
            <p>Add teachers, allocate subjects, and manage batches for them.</p>
        </a>

        <a href="admin_requests.php" class="card">
            <h2>🔔 Notifications</h2>
            <p>Send announcements to teachers and review their requests.</p>
        </a>

        <a href="all_students.php" class="card">
            <h2>👨‍🎓 Students</h2>
            <p>View, edit, or delete student records department-wise and batch-wise.</p>
        </a>
        <a href="manageclass.php" class="card">
            <h2>ClassManagement</h2>
            <p>here the admin can manage class materials and all </p>
        </a>
    </main>

    <!-- Footer -->
    <footer>
        &copy; 2025 ClassConnect Admin Dashboard
    </footer>

</body>
</html>
