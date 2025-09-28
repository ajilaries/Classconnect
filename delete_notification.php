<?php
session_start();
include "config.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "⛔ Access Denied!";
    exit;
}
if(!isset($_GET['id'])){
    echo "notification id is missing ";
    exit;

}
$id=intval($_GET['id']);
$get = mysqli_query($conn, "SELECT file_path FROM notifications WHERE id = $id");
$data = mysqli_fetch_assoc($get);

if ($data && !empty($data['file_path'])) {
    $file = "uploads/" . $data['file_path'];
    if (file_exists($file)) {
        unlink($file); // Delete the file
    }
}
mysqli_query($conn, "DELETE FROM notifications WHERE id = $id");

header("Location: notification.php");
exit;
?>