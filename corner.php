<?php
session_start();
include "config.php"; // DB connection

// ✅ Only logged-in users can access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Get the current user's batch_id
$batch_id = $_SESSION['batch_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher's Corner</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
    body { background-color: #f0f4f8; padding: 40px; }
    .main-box { margin-top: 100px; }
    h2 { text-align: center; margin-bottom: 40px; font-size: 2rem; color: #333; }
    .cards-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; max-width: 1200px; margin: 0 auto; }
    .teacher-card { background-color: white; padding: 20px; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .teacher-card:hover { transform: translateY(-5px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }
    .teacher-card h3 { font-size: 1.3rem; margin-bottom: 10px; color: #276cdb; }
    .teacher-card p { margin: 6px 0; color: black; }
    .email-btn { display: inline-block; margin-top: 12px; padding: 8px 14px; background-color: #276cdb; color: white; border-radius: 8px; text-decoration: none; font-size: 14px; transition: background-color 0.3s ease; }
    .email-btn:hover { background-color: #1f5ec5; }
  </style>
</head>
<body>
  <main class="main-box">
    <h2>📚 Teacher's Corner</h2>

    <div class="cards-container">
      <?php
      // Fetch all teachers assigned to the current batch
      $query = "
    SELECT CONCAT(u.first_name, ' ', u.last_name) AS name, u.email, a.subject, d.department_name
    FROM teacher_allocations a
    JOIN users u ON u.id = a.teacher_id
    JOIN departments d ON d.id = a.department_id
    WHERE a.batch_id = '$batch_id' AND u.role = 'teacher'
    ORDER BY u.first_name ASC
";

      $result = mysqli_query($conn, $query);

      if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
              echo '<div class="teacher-card">';
              echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
              echo '<p><strong>Subject:</strong> ' . htmlspecialchars($row['subject']) . '</p>';
              echo '<p><strong>Department:</strong> ' . htmlspecialchars($row['department_name']) . '</p>';
              echo '<a class="email-btn" href="mailto:' . htmlspecialchars($row['email']) . '">Email</a>';
              echo '</div>';
          }
      } else {
          echo "<p style='text-align:center;'>No teachers found for your batch.</p>";
      }
      ?>
    </div>
  </main>
</body>
</html>
