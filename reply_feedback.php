<?php
include "config.php";

$id = $_POST['id'];
$reply = $_POST['reply'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE feedback SET admin_reply = ?, status = ? WHERE id = ?");
$stmt->bind_param("ssi", $reply, $status, $id);

if ($stmt->execute()) {
    echo "✅ Feedback updated.";
} else {
    echo "❌ Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
