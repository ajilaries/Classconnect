<?php
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Invalid request method.";
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Not logged in.";
    exit();
}

$post_id = intval($_POST['id']);
$message = trim($_POST['message']);
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Check if post exists
$post_query = mysqli_query($conn, "SELECT * FROM classfeed WHERE id = $post_id");
$post = mysqli_fetch_assoc($post_query);

if (!$post) {
    http_response_code(404);
    echo "Post not found.";
    exit();
}

// Only teacher or the post creator can edit
if ($role !== 'teacher' && $post['user_id'] != $user_id) {
    http_response_code(403);
    echo "You don’t have permission to edit this post.";
    exit();
}

// Handle file upload if any
$updated_file = $post['file_path']; // default to existing
if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $targetDir = "uploads/";
    $newFileName = time() . "_" . basename($_FILES['file']['name']);
    $targetFile = $targetDir . $newFileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        // delete old file if exists
        if (!empty($post['file_path']) && file_exists("uploads/" . $post['file_path'])) {
            unlink("uploads/" . $post['file_path']);
        }
        $updated_file = $newFileName;
    }
}

// Update the post
$stmt = $conn->prepare("UPDATE classfeed SET message = ?, file_path = ? WHERE id = ?");
$stmt->bind_param("ssi", $message, $updated_file, $post_id);
if ($stmt->execute()) {
    echo "✅ Post updated successfully.";
} else {
    http_response_code(500);
    echo "Failed to update post.";
}
?>
