<?php
session_start();
include "config.php";
$user_id = $_SESSION['user_id'];

$memories = mysqli_query($conn, "SELECT m.*, u.name FROM memories m JOIN users u ON m.user_id = u.id ORDER BY m.upload_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📸 Yearly Memories</title>
  <link rel="stylesheet" href="memorires.css">
</head>
<body>

<h2 class="mem-title">🎞️ Yearbook Vibes</h2>
<div class="mem-grid">
  <?php while ($row = mysqli_fetch_assoc($memories)) { ?>
    <div class="mem-card" onclick="openPreview('<?= $row['file_path'] ?>', '<?= $row['description'] ?>')">
      <img src="../uploads/<?= $row['file_path'] ?>" alt="memory">
      <p><?= htmlspecialchars($row['title']) ?></p>
    </div>
  <?php } ?>
</div>

<!-- Preview Popup -->
<div id="previewBox" class="preview-box" onclick="this.style.display='none'">
  <img id="previewImg" src="">
  <p id="previewDesc"></p>
</div>
<a href="upload_memories.php" class="upload-btn">➕ Upload</a>

<script>
  function openPreview(src, desc) {
    document.getElementById('previewImg').src = '../uploads/' + src;
    document.getElementById('previewDesc').innerText = desc;
    document.getElementById('previewBox').style.display = 'block';
  }
</script>

</body>
</html>
