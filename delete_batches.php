<?php
session_start();
include "config.php";

// ✅ Security: Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

if (!isset($_GET['batch_id'], $_GET['department_id'])) {
    die("⚠️ Missing required parameters.");
}

$batch_id = intval($_GET['batch_id']);
$department_id = intval($_GET['department_id']);

// ✅ Wrap everything in a transaction for safety
$conn->begin_transaction();

try {
    // 1️⃣ Delete users in this batch (students + teachers)
    $conn->query("DELETE FROM users WHERE batch_id = $batch_id");

    // 2️⃣ Delete classfeed posts
    $conn->query("DELETE FROM classfeed WHERE batch_id = $batch_id");

    // 3️⃣ Delete question papers
    $conn->query("DELETE FROM question_papers WHERE batch_id = $batch_id");

    // 4️⃣ Delete polls
    $conn->query("DELETE FROM polls WHERE batch_id = $batch_id");

    // 5️⃣ Delete feedback
    $conn->query("DELETE FROM feedback WHERE batch_id = $batch_id");

    // 6️⃣ Delete notifications
    $conn->query("DELETE FROM notifications WHERE batch_id = $batch_id");

    // 7️⃣ Finally delete the batch
    $conn->query("DELETE FROM batches WHERE id = $batch_id");

    $conn->commit();

    header("Location: batches.php?department_id=$department_id");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("❌ Failed to delete batch: " . $e->getMessage());
}
