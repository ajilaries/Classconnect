<?php
session_start();
include "config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "⛔ Unauthorized!";
    exit;
}

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'classes.php';
header("Location: " . $redirect);
exit;
