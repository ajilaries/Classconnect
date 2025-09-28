<?php
session_start();
include "config.php"; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied!");
}

if (isset($_GET['poll_id'])) {
    $poll_id = intval($_GET['poll_id']);

    $stmt = $conn->prepare("UPDATE polls SET status = 'ended' WHERE id = ?");
    $stmt->bind_param("i", $poll_id);

    if ($stmt->execute()) {
        echo "<script>alert('Poll ended successfully!'); window.location.href='admin_poll.html';</script>";
    } else {
        echo "Failed to end poll: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
