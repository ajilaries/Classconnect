<?php
session_start();
include "config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') die("🚫 Access Denied!");

$user_id  = $_SESSION['user_id']; // ✅ use user_id
$poll_id  = intval($_POST['poll_id'] ?? 0);
$option_ids = $_POST['option_ids'] ?? [];

if (empty($option_ids)) die("⚠️ Select at least one option.");

// Fetch poll info
$stmt = $conn->prepare("SELECT * FROM polls WHERE id=?");
$stmt->bind_param("i", $poll_id);
$stmt->execute();
$poll = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$poll) die("⚠️ Poll not found.");

// Determine vote key (anonymous vs normal)
$vote_key = $poll['is_anonymous'] ? hash('sha256', $user_id . $poll_id) : $user_id;

// Check if already voted
if ($poll['is_anonymous']) {
    $check = $conn->prepare("SELECT * FROM poll_votes WHERE poll_id=? AND user_id=?");
    $check->bind_param("is", $poll_id, $vote_key);
} else {
    $check = $conn->prepare("SELECT * FROM poll_votes WHERE poll_id=? AND user_id=?");
    $check->bind_param("ii", $poll_id, $vote_key);
}
$check->execute();
if ($check->get_result()->num_rows > 0) die("⚠️ Already voted.");
$check->close();

// Insert votes
if ($poll['is_anonymous']) {
    $stmt = $conn->prepare("INSERT INTO poll_votes (poll_id, option_id, user_id) VALUES (?, ?, ?)");
    foreach ($option_ids as $oid) {
        $oid = intval($oid);
        $stmt->bind_param("iis", $poll_id, $oid, $vote_key);
        $stmt->execute();
    }
} else {
    $stmt = $conn->prepare("INSERT INTO poll_votes (poll_id, option_id, user_id) VALUES (?, ?, ?)");
    foreach ($option_ids as $oid) {
        $oid = intval($oid);
        $stmt->bind_param("iii", $poll_id, $oid, $vote_key);
        $stmt->execute();
    }
}

$stmt->close();

header("Location: poll_results.php?poll_id=$poll_id&voted=1");
exit();
?>
