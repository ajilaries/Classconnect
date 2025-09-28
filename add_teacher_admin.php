<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('⛔ Access Denied'); window.location.href='corner_admin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Teacher</title>
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background-color: #f5f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .form-container {
      background-color: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      width: 90%;
      max-width: 450px;
    }

    .form-container h2 {
      margin-bottom: 20px;
      text-align: center;
      color: #333;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    input[type="submit"] {
      background-color: #276cdb;
      color: white;
      cursor: pointer;
      border: none;
    }

    input[type="submit"]:hover {
      background-color: #1a4fab;
    }

    .back-link {
      text-align: center;
      margin-top: 15px;
      display: block;
      color: #276cdb;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>➕ Add New Teacher</h2>
    <form method="post" action="add_teacher.php">
      <input type="text" name="name" placeholder="Name" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <input type="text" name="department" placeholder="Department" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="submit" value="Add Teacher">
    </form>

    <a href="corner_admin.php" class="back-link">← Back to Teacher's Corner</a>
  </div>

</body>
</html>
