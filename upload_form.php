<?php
include "config.php";
session_start();

// Role check: Only admin and teacher can access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'Student'])) {
    echo "<script>alert('🚫 Access Denied: Only Admins  can upload question papers.'); window.location.href='questionpapers.php';</script>";
    exit();
}

// Fetch subject list
$subjects = mysqli_query($conn, "SELECT * FROM subjects");
?>



<!DOCTYPE html>
<html>
<head>
  <title>Upload Paper - <?= htmlspecialchars($subject) ?></title>
  <style>
    .form-container {
      width: 90%;
      max-width: 500px;
      margin: auto;
      margin-top: 60px;
      padding: 25px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    input, label {
      display: block;
      width: 100%;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>Upload Question Paper</h2>
    <form action="upload_handler.php" method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Enter file title" required><br><br>

    <select name="subject_id" required>
        <option value="">-- Select Subject --</option>
        <?php while($row = mysqli_fetch_assoc($subjects)) { ?>
            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
        <?php } ?>
    </select><br><br>

    <input type="file" name="file" required><br><br>
    <input type="submit" value="Upload">
</form>
  </div>

</body>
</html>
