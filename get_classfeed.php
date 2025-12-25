<?php
session_start();
include "config.php";

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];
$posts   = [];

if ($role === 'teacher') {
    // ✅ Teacher sees only posts they uploaded for the current batch
    $batch_id = $_SESSION['batch_id'] ?? 0;

    if ($batch_id > 0) {
        $stmt = $conn->prepare("
            SELECT cf.id, cf.user_id, cf.post_type, cf.message, cf.file_path, cf.subject,
                   u.first_name, u.last_name, u.role, cf.created_at
            FROM classfeed cf
            JOIN users u ON cf.user_id = u.id
            WHERE cf.user_id = ? AND cf.batch_id = ?
            ORDER BY cf.created_at DESC
        ");
        $stmt->bind_param("ii", $user_id, $batch_id);
        $stmt->execute();
        $posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

} elseif ($role === 'student') {
    // ✅ Student sees posts for their batch, optionally filtered by subject
    $batch_id = $_SESSION['batch_id'] ?? 0;
    $subject  = isset($_GET['subject']) ? trim($_GET['subject']) : '';

    if ($batch_id > 0) {
        if ($subject !== '') {
            $stmt = $conn->prepare("
                SELECT cf.id, cf.user_id, cf.post_type, cf.message, cf.file_path, cf.subject,
                       u.first_name, u.last_name, u.role, cf.created_at
                FROM classfeed cf
                JOIN users u ON cf.user_id = u.id
                WHERE u.role = 'teacher' 
                  AND cf.batch_id = ? 
                  AND cf.subject = ?
                ORDER BY cf.created_at DESC
            ");
            $stmt->bind_param("is", $batch_id, $subject);
        } else {
            $stmt = $conn->prepare("
                SELECT cf.id, cf.user_id, cf.post_type, cf.message, cf.file_path, cf.subject,
                       u.first_name, u.last_name, u.role, cf.created_at
                FROM classfeed cf
                JOIN users u ON cf.user_id = u.id
                WHERE u.role = 'teacher' 
                  AND cf.batch_id = ?
                ORDER BY cf.created_at DESC
            ");
            $stmt->bind_param("i", $batch_id);
        }

        $stmt->execute();
        $posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

echo json_encode($posts);
?>
