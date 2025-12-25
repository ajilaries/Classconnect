<?php
include "config.php";
session_start();
if(!isset($_SESSION['role'])||$_SESSION['role'] !=='teacher'){
    echo "access denied";
    exit;

}
if (!isset($_GET['id'])) {
    echo "❌ Notification ID missing!";
    exit;
}
$id=intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM notifications WHERE id=$id");
$notification = mysqli_fetch_assoc($query);

if (!$notification) {
    echo "⚠️ Notification not found!";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Notification</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background: #f2f2f2;
        }
        .edit-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background: #28a745;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .file-preview {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <h2>✏️ Edit Notification</h2>

    <form class="edit-form" action="update_notification.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $notification['id'] ?>">

        <label>Message:</label>
        <textarea name="message" rows="4" required><?= htmlspecialchars($notification['message']) ?></textarea>

        <?php if (!empty($notification['file_path'])): ?>
            <div class="file-preview">
                <p>📎 <strong>Current file:</strong> 
                    <a href="uploads/<?= $notification['file_path'] ?>" target="_blank"><?= $notification['file_path'] ?></a>
                </p>
            </div>
        <?php endif; ?>

        <label>Replace File (optional):</label>
        <input type="file" name="file">

        <button type="submit" name="update">💾 Update Notification</button>
    </form>

</body>
</html>