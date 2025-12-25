<?php
session_start();
include "config.php";

// Only allow DELETE requests
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method.']);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Not authorized.']);
    exit();
}

// Parse the raw query string
parse_str($_SERVER['QUERY_STRING'], $params);
if (!isset($params['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing post ID.']);
    exit();
}

$post_id = intval($params['id']);
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// First get the post to check ownership
$stmt = $conn->prepare("SELECT * FROM classfeed WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    http_response_code(404);
    echo json_encode(['error' => 'Post not found.']);
    exit();
}

// Only teacher or the original poster can delete
if ($role !== 'teacher' && $post['user_id'] != $user_id) {
    http_response_code(403);
    echo json_encode(['error' => 'You don’t have permission to delete this post.']);
    exit();
}

// Delete attached file if exists
if (!empty($post['file_path']) && file_exists("uploads/" . $post['file_path'])) {
    unlink("uploads/" . $post['file_path']);
}

// Delete the post
$stmt = $conn->prepare("DELETE FROM classfeed WHERE id = ?");
$stmt->bind_param("i", $post_id);
$deletedPost = $stmt->execute();
$stmt->close();

// ✅ Delete corresponding notifications
$stmt = $conn->prepare("DELETE FROM notifications WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

if ($deletedPost) {
    echo json_encode(['success' => true, 'message' => 'Post deleted successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete post.']);
}
?>
