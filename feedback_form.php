<?php
session_start();
include "config.php";

// Only logged-in students
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$batch_id = $_SESSION['batch_id'];

// Fetch teachers for this batch
$sql = "
    SELECT DISTINCT u.id, CONCAT(u.first_name, ' ', u.last_name) AS teacher_name
    FROM teacher_allocations a
    JOIN users u ON u.id = a.teacher_id
    WHERE a.batch_id = ? AND u.role = 'teacher'
    ORDER BY u.first_name ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $batch_id);
$stmt->execute();
$result = $stmt->get_result();

$teachers = [];
while ($row = $result->fetch_assoc()) {
    $teachers[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Feedback</title>
  <link rel="stylesheet" href="feedback.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <form action="feedback.php" method="POST">
    <h2>📝 Submit Feedback</h2>

    <!-- Teacher Dropdown -->
    <label for="teacher">Select Teacher:</label>
    <select name="teacher_id" id="teacher" required>
      <option value="">-- Select Teacher --</option>
      <?php foreach ($teachers as $t): ?>
        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['teacher_name']) ?></option>
      <?php endforeach; ?>
    </select>

    <!-- Subject (hidden, auto-filled) -->
    <input type="hidden" name="subject" id="subject_input">

    <!-- Feedback Text -->
    <label for="feedback_text">Feedback:</label>
    <textarea name="feedback_text" id="feedback_text" placeholder="Write your feedback..." required></textarea>

    <!-- Category -->
    <label for="category">Category:</label>
    <select name="category" id="category" required>
      <option value="">-- Select --</option>
      <option value="teaching">Teaching Method</option>
      <option value="knowledge">Subject Knowledge</option>
      <option value="interaction">Student Interaction</option>
      <option value="other">Other</option>
    </select>

    <!-- Rating Stars -->
    <label>Rate Teacher:</label>
    <div class="rating">
      <input type="radio" id="star5" name="rating" value="5" required>
      <label for="star5">★</label>
      <input type="radio" id="star4" name="rating" value="4">
      <label for="star4">★</label>
      <input type="radio" id="star3" name="rating" value="3">
      <label for="star3">★</label>
      <input type="radio" id="star2" name="rating" value="2">
      <label for="star2">★</label>
      <input type="radio" id="star1" name="rating" value="1">
      <label for="star1">★</label>
    </div>

    <!-- Anonymous -->
    <label for="anonymous">
      <input type="checkbox" id="anonymous" name="anonymous" value="1"> Submit Anonymously
    </label>

    <!-- Submit -->
    <button type="submit">Submit Feedback</button>
  </form>

  <script>
    // Auto-fill subject when teacher is selected
    $("#teacher").change(function() {
      var teacherId = $(this).val();
      var batchId = <?= $batch_id ?>;

      if (teacherId) {
        $.ajax({
          url: "get_subjects.php",
          type: "POST",
          data: { teacher_id: teacherId, batch_id: batchId },
          success: function(data) {
            $("#subject_input").val(data.trim());
          }
        });
      } else {
        $("#subject_input").val('');
      }
    });
  </script>
</body>
</html>
