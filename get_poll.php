<?php
session_start();
include "config.php";

// Fetch active poll
$sql = "SELECT * FROM polls WHERE is_active = 1 LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    $poll = [
        "id" => $row["id"],
        "question" => $row["question"],
        "options" => json_decode($row["options"], true) // stored as JSON
    ];
    echo json_encode($poll);
} else {
    echo json_encode(["error" => "No active poll found"]);
}
?>
