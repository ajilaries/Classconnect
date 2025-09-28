<?php
include "config.php";
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('⛔ Access Denied! Only admins can edit teachers.'); window.location.href='corner_admin.php';</script>";
    exit();
}
if (!isset($_GET['id'])) {
    echo "<script>alert('⚠️ No teacher selected.'); window.location.href='corner_admin.php';</script>";
    exit();
}

$teacher_id = intval($_GET['id']);

// Fetch teacher details
$stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    echo "<script>alert('❌ Teacher not found.'); window.location.href='corner_admin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Teacher</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
    }
    .form-container {
      background: white;
      max-width: 500px;
      margin: 80px auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
    }
    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    input[type="submit"] {
      background-color: #4CAF50;
      border: none;
      color: white;
      padding: 12px 20px;
      text-align: center;
      cursor: pointer;
      border-radius: 5px;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>✏️ Edit Teacher</h2>
  <form method="post" action="update_teacher.php">
    <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
    <input type="text" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" required>
    <input type="text" name="subject" value="<?php echo htmlspecialchars($teacher['subject']); ?>" required>
    <input type="text" name="department" value="<?php echo htmlspecialchars($teacher['department']); ?>" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
    <input type="submit" value="Update">
  </form>
</div>

</body>
</html>