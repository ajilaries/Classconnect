<?php
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $id = intval($_POST['id']);
    $message = trim($_POST['message']);

    if (empty($message)) {
        echo " Message cannot be empty!";
        exit;
    }

    // Get current notification data
    $result = mysqli_query($conn, "SELECT * FROM notifications WHERE id = $id");
    $existing = mysqli_fetch_assoc($result);

    if (!$existing) {
        echo " Notification not found!";
        exit;
    }

    $filename = $existing['file_path'];

    // If a new file is uploaded, replace the old one
    if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
        $uploadDir = "uploads/";
        $originalName = basename($_FILES['file']['name']);
        $newFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $originalName);
        $targetPath = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            // Delete old file if it exists
            if (!empty($filename) && file_exists($uploadDir . $filename)) {
                unlink($uploadDir . $filename);
            }
            $filename = $newFileName;
        } else {
            echo " Failed to upload new file!";
            exit;
        }
    }

    // Update query
    $stmt = $conn->prepare("UPDATE notifications SET message = ?, file_path = ? WHERE id = ?");
    $stmt->bind_param("ssi", $message, $filename, $id);

    if ($stmt->execute()) {
        header("Location: notification.php");
        exit;
    } else {
        echo " Update failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    echo " Access Denied!";
}
?>
