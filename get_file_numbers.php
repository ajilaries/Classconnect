<?php
include "config.php";

// Return JSON
header('Content-Type: application/json');

if (!isset($_GET['file_type_id']) || !is_numeric($_GET['file_type_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type ID', 'data' => []]);
    exit;
}

$file_type_id = intval($_GET['file_type_id']);
$batch_id = isset($_GET['batch_id']) ? intval($_GET['batch_id']) : 0;
$now = date('Y-m-d H:i:s');

// Prepare query with optional batch & deadline filtering
$sql = "
    SELECT ftn.id, ftn.number 
    FROM file_type_numbers ftn
    LEFT JOIN deadlines d 
      ON d.category_id = ftn.file_type_id 
      AND d.type_number_id = ftn.id 
      AND d.batch_id = ?
    WHERE ftn.file_type_id = ? 
      AND (d.deadline IS NULL OR d.deadline > ?)
    ORDER BY ftn.number ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $batch_id, $file_type_id, $now);

$response = ['success' => false, 'message' => 'No numbers found', 'data' => []];

if ($stmt->execute()) {
    $res = $stmt->get_result();
    $numbers = [];
    while ($row = $res->fetch_assoc()) $numbers[] = $row;

    if (!empty($numbers)) {
        $response['success'] = true;
        $response['message'] = 'Numbers fetched successfully';
        $response['data'] = $numbers;
    }
} else {
    $response['message'] = 'Database error: ' . $stmt->error;
}

$stmt->close();
echo json_encode($response);
exit;
