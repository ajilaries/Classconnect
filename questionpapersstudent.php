<?php
session_start();
include "config.php";

// ✅ Security: Allow only students
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
    exit;
}

$student_batch_id = $_SESSION['batch_id'] ?? 0;

// ✅ 1. Fetch subjects for this student's batch
$subjects = [];
$subjectQuery = "SELECT DISTINCT id AS allocation_id, subject 
                 FROM teacher_allocations 
                 WHERE batch_id = ?";
$stmt = $conn->prepare($subjectQuery);
$stmt->bind_param("i", $student_batch_id);
$stmt->execute();
$result = $stmt->get_result();
$subjects = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ✅ 2. Get selected subject (allocation_id)
$selected_allocation = isset($_GET['allocation_id']) ? intval($_GET['allocation_id']) : 0;
$papers = [];

if ($selected_allocation > 0) {
    // ✅ Fetch question papers for this batch + subject
    $query = "
        SELECT qp.id, qp.title, qp.file_path, qp.uploaded_at,
               ta.subject, CONCAT(u.first_name, ' ', u.last_name) AS teacher_name
        FROM question_papers qp
        INNER JOIN teacher_allocations ta ON ta.id = qp.allocation_id
        LEFT JOIN users u ON u.id = qp.uploaded_by
        WHERE qp.batch_id = ? AND qp.allocation_id = ?
        ORDER BY qp.uploaded_at DESC
    ";
    $stmt2 = $conn->prepare($query);
    $stmt2->bind_param("ii", $student_batch_id, $selected_allocation);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $papers = $result2->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>📄 Question Papers</title>
<style>
body { font-family: "Segoe UI", sans-serif; padding: 20px; background: #f7f9fc; }
h2 { color: #276cdb; }
.subject-list { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
.subject-btn {
    padding: 10px 15px;
    background: #fff;
    border: 1px solid #276cdb;
    color: #276cdb;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.2s ease;
}
.subject-btn:hover, .subject-btn.active { background: #276cdb; color: #fff; }
ul { list-style: none; padding-left: 0; }
li {
    background: white;
    margin: 10px 0;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
a.download { color: #276cdb; font-weight: bold; }
.no-data { color: #666; font-style: italic; margin-top: 10px; }
</style>
</head>
<body>

<h2>📄 Question Papers</h2>

<div class="subject-list">
<?php if (!empty($subjects)): ?>
    <?php foreach ($subjects as $s): ?>
        <a class="subject-btn <?= $selected_allocation === intval($s['allocation_id']) ? 'active' : '' ?>" 
           href="?allocation_id=<?= intval($s['allocation_id']) ?>">
            <?= htmlspecialchars($s['subject']) ?>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <p class="no-data">⚠️ No subjects found for your batch.</p>
<?php endif; ?>
</div>

<?php if ($selected_allocation > 0): ?>
    <?php if (!empty($papers)): ?>
        <ul>
        <?php foreach ($papers as $p): ?>
            <li>
                <strong><?= htmlspecialchars($p['subject']) ?></strong> - 
                <?= htmlspecialchars($p['title']) ?><br>
                👨‍🏫 Uploaded by: <em><?= htmlspecialchars($p['teacher_name'] ?? "Unknown") ?></em><br>
                📅 <?= date("d M Y", strtotime($p['uploaded_at'])) ?><br>
                <a class="download" href="<?= htmlspecialchars($p['file_path']) ?>" target="_blank">📥 Download</a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="no-data">🚫 No question papers uploaded for this subject yet.</p>
    <?php endif; ?>
<?php else: ?>
    <p class="no-data">⬆️ Click a subject above to view its question papers.</p>
<?php endif; ?>

</body>
</html>
