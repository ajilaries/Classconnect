<?php
session_start();
include "config.php";

// Only teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => '⛔ Unauthorized Access']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $file_type_id   = intval($_POST['file_type_id'] ?? 0);
    $file_number_id = intval($_POST['file_type_number_id'] ?? 0);
    $batch_id       = intval($_POST['batch_id'] ?? 0);
    $deadline       = $_POST['deadline'] ?? '';

    if (!$file_type_id || !$file_number_id || !$batch_id || !$deadline) {
        echo json_encode(['success' => false, 'message' => '⚠️ All fields are required.']);
        exit;
    }

    // Validate datetime
    $deadline_ts = strtotime($deadline);
    if (!$deadline_ts) {
        echo json_encode(['success' => false, 'message' => '⚠️ Invalid datetime format']);
        exit;
    }
    $deadline_mysql = date("Y-m-d H:i:s", $deadline_ts);

    // Check if deadline exists
    $check = $conn->prepare(
        "SELECT id FROM deadlines WHERE category_id=? AND type_number_id=? AND batch_id=?"
    );
    $check->bind_param("iii", $file_type_id, $file_number_id, $batch_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // Update
        $update = $conn->prepare(
            "UPDATE deadlines SET deadline=? WHERE category_id=? AND type_number_id=? AND batch_id=?"
        );
        $update->bind_param("siii", $deadline_mysql, $file_type_id, $file_number_id, $batch_id);
        if ($update->execute()) {
            echo json_encode(['success' => true, 'message' => '✅ Deadline updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Error updating deadline: '.$conn->error]);
        }
        $update->close();
    } else {
        // Insert
        $insert = $conn->prepare(
            "INSERT INTO deadlines (category_id, type_number_id, batch_id, deadline) VALUES (?, ?, ?, ?)"
        );
        $insert->bind_param("iiis", $file_type_id, $file_number_id, $batch_id, $deadline_mysql);
        if ($insert->execute()) {
            echo json_encode(['success' => true, 'message' => '✅ Deadline set successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Error setting deadline: '.$conn->error]);
        }
        $insert->close();
    }
    $check->close();
} else {
    echo json_encode(['success' => false, 'message' => '⚠️ Invalid request.']);
}
?>
