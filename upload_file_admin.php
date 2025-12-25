<?php
session_start();
include "config.php";

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "⛔ Access denied.";
    exit;
}

// Get subjects for dropdown
$subjects = mysqli_query($conn, "SELECT * FROM subjects");

// Get uploaded files
$files = mysqli_query($conn, "SELECT f.*, s.name AS subject_name FROM files f JOIN subjects s ON f.subject_id = s.id ORDER BY f.upload_time DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>📤 Admin File Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        #uploadForm {
            display: none;
            margin-top: 20px;
            margin-bottom: 40px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background: #f9f9f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        button {
            padding: 8px 16px;
            background: #2d89ef;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #1e5fb4;
        }
    </style>
</head>
<body>

    <h2>👨‍🏫 Admin Panel - File Upload</h2>

    <!-- Upload Toggle Button -->
   onclick="document.getElementById('uploadForm').style.display = (uploadForm.style.display === 'none') ? 'block' : 'none';"

        📁 Upload New File
    </button>

    <!-- Upload Form -->
    <div id="uploadForm">
        <h3>Upload File</h3>
        <form action="upload_file_admin_backend.php" method="POST" enctype="multipart/form-data">
            <label>Title:</label>
            <input type="text" name="title" required><br><br>

            <label>Subject:</label>
            <select name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php while($row = mysqli_fetch_assoc($subjects)): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select><br><br>

            <label>File Type:</label>
            <select name="file_type" required>
                <option value="material">Material</option>
                <option value="assignment">Assignment</option>
                <option value="other">Other</option>
            </select><br><br>

            <label>Select File:</label>
            <input type="file" name="file" required><br><br>

            <button type="submit" name="upload">Upload</button>
        </form>
    </div>

    <!-- Uploaded Files Table -->
    <h3>📄 Uploaded Files</h3>
    <?php if (mysqli_num_rows($files) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>📄 Title</th>
                <th>📚 Subject</th>
                <th>📁 Type</th>
                <th>⏰ Uploaded At</th>
                <th>🔗 File</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($file = mysqli_fetch_assoc($files)): ?>
            <tr>
                <td><?= htmlspecialchars($file['title']) ?></td>
                <td><?= htmlspecialchars($file['subject_name']) ?></td>
                <td><?= ucfirst($file['file_type']) ?></td>
                <td><?= $file['upload_time'] ?></td>
                <td><a href="uploads/<?= urlencode($file['file_name']) ?>" target="_blank">Download</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>😶 No files uploaded yet.</p>
    <?php endif; ?>

</body>
</html>
