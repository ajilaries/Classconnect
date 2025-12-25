<?php
session_start();
include "config.php"; 

// ⚡ Set PHP timezone to local
date_default_timezone_set('Asia/Kolkata');

// Only teachers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') die("Access Denied!");

// Ensure batch and college info exists
$teacher_batch_id = $_SESSION['batch_id'] ?? null;
$college_id = $_SESSION['college_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$teacher_batch_id || !$college_id || !$user_id) die("Batch, college, or user info missing!");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');
    $options = array_filter($_POST['options'] ?? []); // remove empty options
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $is_multiple_choice = isset($_POST['is_multiple_choice']) ? 1 : 0;
    $expires_in = isset($_POST['expires_in']) ? (int)$_POST['expires_in'] : 0;

    if (empty($question) || count($options) < 2) {
        die("Poll must have a question and at least 2 options!");
    }

    // Calculate expiration datetime
    $expires_at = $expires_in > 0 ? date("Y-m-d H:i:s", time() + $expires_in * 60) : null;

    // Convert options array to JSON for storing in polls table
    $options_json = json_encode($options);

    // Insert poll into database
    $stmt = $conn->prepare("
        INSERT INTO polls 
        (question, options, is_anonymous, is_multiple_choice, expires_at, batch_id, created_by, college_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        "ssiisiii", 
        $question, 
        $options_json, 
        $is_anonymous, 
        $is_multiple_choice, 
        $expires_at, 
        $teacher_batch_id, 
        $user_id, 
        $college_id
    );

    if ($stmt->execute()) {
        $poll_id = $stmt->insert_id;

        // Insert each option into separate poll_options table
        $opt_stmt = $conn->prepare("INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)");
        if (!$opt_stmt) die("Prepare failed: " . $conn->error);

        foreach ($options as $opt) {
            $opt_stmt->bind_param("is", $poll_id, $opt);
            $opt_stmt->execute();
        }
        $opt_stmt->close();

        echo "<script>
            alert('✅ Poll Created Successfully!');
            window.location.href='poll_list.php';
        </script>";
        exit();
    } else {
        echo "❌ Error inserting poll: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Poll - ClassConnect</title>
<link rel="stylesheet" href="admin_poll.css">
<style>
#options-container input { display:block; margin-bottom:5px; width: 100%; padding: 8px; }
.submit-btn, #add-option-btn { padding: 10px 20px; margin-top: 10px; border:none; border-radius:6px; background-color:#4CAF50; color:white; cursor:pointer; }
.submit-btn:hover, #add-option-btn:hover { background-color:#45a049; }
</style>
</head>
<body>

<div class="poll-container">
<h2>📋 Create New Poll</h2>

<form id="create-poll-form" method="POST" action="">
  <label for="question">Poll Question:</label>
  <input type="text" id="question" name="question" placeholder="Type your Question?" required>

  <label>Options:</label>
  <div id="options-container">
    <input type="text" name="options[]" placeholder="Option 1" required>
    <input type="text" name="options[]" placeholder="Option 2" required>
  </div>
  <button type="button" id="add-option-btn">➕ Add Option</button>

  <label for="expires_in">Expires In (minutes):</label>
  <input type="number" name="expires_in" id="expires_in" placeholder="e.g. 10">

  <label>
    <input type="checkbox" name="is_anonymous"> Anonymous Voting
  </label>

  <label>
    <input type="checkbox" name="is_multiple_choice"> Allow Multiple Choice
  </label>

  <button type="submit" class="submit-btn">✅ Create Poll</button>
</form>
</div>

<script>
// Add new option dynamically
document.getElementById("add-option-btn").addEventListener("click", () => {
    const container = document.getElementById("options-container");
    const input = document.createElement("input");
    input.type = "text";
    input.name = "options[]";
    input.placeholder = "New Option";
    input.required = true;
    container.appendChild(input);
});

// Validation
document.getElementById("create-poll-form").addEventListener("submit", function (e) {
    const questionInput = document.getElementById("question");
    const options = document.querySelectorAll('input[name="options[]"]');
    const question = questionInput.value.trim();

    if (!/^[A-Z]/.test(question)) {
      alert("Poll question must start with an uppercase letter.");
      questionInput.focus();
      e.preventDefault();
      return;
    }
    if (!/\?$/.test(question)) {
      alert("Poll question must end with a question mark (?).");
      questionInput.focus();
      e.preventDefault();
      return;
    }
    let validOptions = 0;
    for (let opt of options) {
      if (opt.value.trim().length >= 3) validOptions++;
      else { alert("Each option must be at least 3 characters long."); opt.focus(); e.preventDefault(); return; }
    }
    if (validOptions < 2) { alert("Provide at least 2 valid options."); e.preventDefault(); return; }
});
</script>

</body>
</html>
