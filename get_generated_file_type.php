<?php
include "config.php";
session_start();

$baseType = $_GET['base_type'] ?? '';
$batch_id = $_SESSION['batch_id'] ?? 0;

if(!$baseType || !$batch_id){
    die('<option value="">Select</option>');
}

// Count existing files of this base type for the batch
$stmt = $conn->prepare("
    SELECT number FROM file_type_numbers
    JOIN file_types ON file_type_numbers.file_type_id = file_types.id
    WHERE type_name=? AND batch_id=?
    ORDER BY number ASC
");
$stmt->bind_param("si", $baseType, $batch_id);
$stmt->execute();
$res = $stmt->get_result();

$numbers = [];
while($row = $res->fetch_assoc()){
    $numbers[] = intval($row['number']);
}

// Generate next 5 numbers (you can change this)
$maxNum = $numbers ? max($numbers) : 0;
$options = '';
for($i = 1; $i <= $maxNum+1; $i++){
    if(!in_array($i, $numbers)){
        $options .= '<option value="'.$i.'">'.htmlspecialchars($baseType.' '.$i).'</option>';
    }
}

echo $options;
?>
