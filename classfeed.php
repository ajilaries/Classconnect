<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id   = $_SESSION['user_id'];
$role      = $_SESSION['role'];
$subjects  = [];

// ✅ Only for students: fetch available subjects from teacher_allocations
if ($role === 'student') {
    if (!isset($_SESSION['batch_id']) || empty($_SESSION['batch_id'])) {
        die("❌ Batch not found in session. Please check login script.");
    }

    $batch_id = $_SESSION['batch_id'];

    $stmt = $conn->prepare("
    SELECT DISTINCT subject 
    FROM teacher_allocations 
    WHERE batch_id = ?
");

    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['subject'];
    }
    $stmt->close();
}

// ✅ Capture post_id from notification redirect
$highlightPostId = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ClassConnect - ClassFeed</title>
  <link rel="stylesheet" href="classfeed.css">
  <style>
    .highlight {
      border: 2px solid #007bff !important;
      background-color: #e9f5ff !important;
      animation: fadeHighlight 2.2s ease-out forwards;
    }
    @keyframes fadeHighlight {
      0% { background-color: #e9f5ff; }
      100% { background-color: white; }
    }
  </style>
</head>
<body>

<script>
sessionStorage.setItem("user_id", "<?= $user_id ?>");
sessionStorage.setItem("role", "<?= $role ?>");
sessionStorage.setItem("first_name", "<?= htmlspecialchars($_SESSION['first_name'] ?? '') ?>");
sessionStorage.setItem("last_name", "<?= htmlspecialchars($_SESSION['last_name'] ?? '') ?>");
</script>

<div class="feed-container">
  <div class="feed-header">
    <h2>📚 ClassFeed</h2>
    <?php if($role === 'teacher'): ?>
      <button onclick="togglePostBox()">+ New Post</button>
    <?php endif; ?>
  </div>

  <?php if($role === 'student'): ?>
    <!-- Subject Filter for Students -->
    <div class="subject-filter" style="margin-bottom:15px;">
      <label for="subjectFilter">Filter by Subject:</label>
      <select id="subjectFilter" onchange="loadPosts()">
        <option value="">-- Select Subject --</option>
        <?php foreach($subjects as $sub): ?>
          <option value="<?= htmlspecialchars($sub) ?>"><?= htmlspecialchars($sub) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php endif; ?>

  <div id="post-feed"></div>
</div>

<?php if($role === 'teacher'): ?>
<!-- Upload Box for teacher only -->
<div class="post-box-popup" id="postBoxPopup">
  <div class="post-box">
    <span class="close-btn" onclick="togglePostBox()">✖</span>
    <h3>Share something with your class ✨</h3>
    <form action="classfeed_upload.php" method="POST" enctype="multipart/form-data">
      <label for="post_type">Post Type</label>
      <select name="post_type" id="post_type" required>
        <option value="">-- Select Type --</option>
        <option value="notes">NOTES</option>
        <option value="announcement">ANNOUNCEMENT</option>
        <option value="meet_link">MEET LINKS</option>
        <option value="other">OTHERS</option>
      </select>

      <label for="message">Message</label>
      <textarea name="message" id="message" rows="4" placeholder="Write something..." required></textarea>

      <label for="file">Optional File Upload</label>
      <input type="file" name="file" id="file" accept=".pdf,.jpg,.jpeg,.png,.docx,.pptx">

      <button type="submit" name="submit">📤 Post</button>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
function togglePostBox() {
  const box = document.getElementById("postBoxPopup");
  box.style.display = (box.style.display === "flex") ? "none" : "flex";
}

function escapeHTML(str) {
  return str ? str.replace(/[&<>"']/g, tag => ({"&":"&amp;","<":"&lt;","&gt;":"&gt;",'"':"&quot;","'":"&#039;"}[tag])) : '';
}

async function loadPosts() {
  const container = document.getElementById('post-feed');
  const currentUserId = sessionStorage.getItem("user_id");
  const currentUserRole = sessionStorage.getItem("role");
  const filterSubject = document.getElementById("subjectFilter") ? document.getElementById("subjectFilter").value : '';

  container.innerHTML = '';

  const res = await fetch(`get_classfeed.php?subject=${encodeURIComponent(filterSubject)}`);
  const posts = await res.json();

  posts.forEach(post => {
    const box = document.createElement('div');
    box.classList.add('post-item');
    box.setAttribute('id', 'post-' + post.id);
    box.setAttribute('data-id', post.id);

    let fileSection = '';
    if (post.file_path) {
      fileSection = `<div class="file-link">📎 <a href="uploads/${escapeHTML(post.file_path)}" target="_blank">View Attached File</a></div>`;
    }

    box.innerHTML = `
      <div class="post-meta">
        <strong>${escapeHTML(post.first_name)} ${escapeHTML(post.last_name)}</strong>
        <span style="margin-left:10px;font-style:italic;">[${escapeHTML(post.subject)}]</span>
        <span style="float:right;">${new Date(post.created_at).toLocaleString()}</span>
      </div>
      <div class="post-type">📌 ${escapeHTML(post.post_type)}</div>
      <p>${escapeHTML(post.message)}</p>
      ${fileSection}
    `;

if(currentUserRole === 'teacher' && Number(currentUserId) === Number(post.user_id)) {
    // Delete button
    const delBtn = document.createElement('button');
    delBtn.textContent = '🗑️ Delete';
    delBtn.classList.add('delete-btn');
    delBtn.onclick = async () => {
        if(confirm("Delete this post?")) {
            await fetch(`classfeed_delete.php?id=${post.id}`, { method: 'DELETE' });
            loadPosts();
        }
    };
    box.appendChild(delBtn);

    // Edit button
    const editBtn = document.createElement('button');
    editBtn.textContent = '✏️ Edit';
    editBtn.classList.add('edit-btn');
    editBtn.onclick = () => {
        // Here you can open a popup to edit message, post type, or file
        openEditPopup(post);
    };
    box.appendChild(editBtn);
}


    container.appendChild(box);
  });

  // ✅ Highlight after DOM is fully painted
  const highlightId = <?= $highlightPostId ?: 0 ?>;
  if (highlightId) {
    requestAnimationFrame(() => {
      const target = document.getElementById('post-' + highlightId);
      if (target) {
        target.classList.add('highlight');
        target.scrollIntoView({ behavior: "smooth", block: "center" });
        setTimeout(() => target.classList.remove('highlight'), 2300);
      }
    });
  }
}

loadPosts();

</script>

<style>
.post-box-popup { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999; }
.post-box { background:white; padding:20px; border-radius:10px; width:90%; max-width:500px; position:relative; }
.close-btn { position:absolute; right:15px; top:10px; cursor:pointer; font-size:20px; }
.delete-btn { background:#dc3545; color:white; padding:6px 12px; border:none; border-radius:5px; margin-top:10px; cursor:pointer; }
</style>

</body>
</html>
