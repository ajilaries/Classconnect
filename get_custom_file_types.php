<?php
session_start();
include "config.php";

if (!isset($_GET['base_type_id'], $_GET['batch_id'])) exit;

$base_type_id = intval($_GET['base_type_id']);
$batch_id = intval($_GET['batch_id']);

$stmt = $conn->prepare("SELECT id, type_label FROM file_type_numbers WHERE file_type_id=? AND batch_id=? ORDER BY type_label ASC");
$stmt->bind_param("ii", $base_type_id, $batch_id);
$stmt->execute();
$res = $stmt->get_result();

$options = "";
while($row = $res->fetch_assoc()){
    $label = htmlspecialchars($row['type_label']);
    $options .= "<option value='{$label}'>{$label}</option>";
}
echo $options;
$stmt->close();
