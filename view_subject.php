<?php
include "config.php";

if (!isset($_GET['subject_id'])) {
    echo "⚠️ No subject selected.";
    exit;
}

$subject_id = intval($_GET['subject_id']);
$subject_query = mysqli_query($conn, "SELECT name FROM subjects WHERE id=$subject_id");
$subject = mysqli_fetch_assoc($subject_query);

if (!$subject) {
    echo "❌ Subject not found.";
    exit;
}

$papers = mysqli_query($conn, "SELECT * FROM question_papers WHERE subject_id=$subject_id");
?>

<!DOCTYPE html>
<html>
<head>
  <title><?= htmlspecialchars($subject['name']) ?> - Question Papers</title>
  <link rel="stylesheet" href="view_subject.css">
</head>
<body>

  <div class="wrapper">
    <h2 class="subject-title">📄 Question Papers for <span><?= htmlspecialchars($subject['name']) ?></span></h2>

    <ul class="paper-list">
      <?php while($row = mysqli_fetch_assoc($papers)) { ?>
        <li>
          <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="paper-link">
            <?= htmlspecialchars($row['title']) ?>
          </a>
        </li>
      <?php } ?>
    </ul>

    <a href="upload_form.php?subject_id=<?= $subject_id ?>" class="upload-btn">➕ Upload New Question Paper</a>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')  ?>
<a href="delete_subject.php?subject_id=<?= $subject_id ?>" class="upload-btn delete-subject-btn" onclick="return confirm('Are you sure you want to delete this subject? This will also delete all its question papers.')">🗑️ Delete Subject</a>
  </div>

</body>
</html>
