<?php
session_start();
include "config.php"; // connect to your DB

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  die("Access Denied!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $options = $_POST['options'];
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $is_multiple_choice = isset($_POST['is_multiple_choice']) ? 1 : 0;
    $expires_in = isset($_POST['expires_in']) ? (int)$_POST['expires_in'] : 0;

    if (empty($question) || count($options) < 2) {
        die("Poll must have a question and at least 2 options!");
    }

    // JSON encode options for storage
    $options_json = json_encode(array_filter($options));

    // Calculate expiration datetime
    $expires_at = $expires_in > 0 ? date("Y-m-d H:i:s", time() + $expires_in * 60) : null;

    $stmt = $conn->prepare("INSERT INTO polls (question, options, is_anonymous, is_multiple_choice, expires_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $question, $options_json, $is_anonymous, $is_multiple_choice, $expires_at);

    if ($stmt->execute()) {
        echo "<script>alert('Poll Created Successfully!'); window.location.href='poll_list.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
                                                                                                    
    $stmt->close();
    $conn->close();
}
?>
