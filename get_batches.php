<?php
include "config.php";

if(!isset($_GET['department_id'])) {
    echo json_encode([]);
    exit;
}

$department_id = intval($_GET['department_id']);
$stmt = $conn->prepare("SELECT id, batch_name FROM batches WHERE department_id = ? ORDER BY batch_name ASC");
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();

$batches = [];
while($row = $result->fetch_assoc()) {
    $batches[] = $row;
}

echo json_encode($batches);
?>
