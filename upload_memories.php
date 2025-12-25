<?php
session_start();
include "config.php";

// Make sure only logged-in students can upload
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
  echo "Unauthorized access. Only students can upload memories.";
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user_id = $_SESSION['user_id'];
  $title = trim($_POST['title']);
  $description = trim($_POST['description']);
  $file_name = $_FILES['file']['name'];
  $tmp_name = $_FILES['file']['tmp_name'];

  $upload_dir = "../uploads/";
  $target_file = $upload_dir . basename($file_name);

  // Move file to uploads folder
  if (move_uploaded_file($tmp_name, $target_file)) {
    $query = "INSERT INTO memories (user_id, title, description, file_path, upload_time) 
              VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $title, $description, $file_name);
    $stmt->execute();

    header("Location: memories.php");
    exit;
  } else {
    echo "Upload failed 😓. Try again!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📸 Upload Memory</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f5f5;
      padding: 30px;
    }
    .upload-box {
      max-width: 500px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button {
      background-color: #4CAF50;
      color: white;
      padding: 12px 20px;
      margin-top: 15px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background-color: #388E3C;
    }
  </style>
</head>
<body>

<div class="upload-box">
  <h2>📤 Share Your Memory</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Title</label>
    <input type="text" name="title" required placeholder="e.g., Onam Celebration 2025 🎉">
    
    <label>Description</label>
    <textarea name="description" rows="4" placeholder="Write something about the moment..."></textarea>
    
    <label>Choose an image</label>
    <input type="file" name="file" accept="image/*" required>

    <button type="submit">Upload</button>
  </form>
</div>

</body>
</html>
