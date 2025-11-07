<?php
session_start();
include "config.php";

// ✅ Only teachers allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit;
}

$teacher_id = $_SESSION['user_id'];

// ✅ Fetch allocations
$query = "SELECT ta.id AS allocation_id, ta.subject, ta.batch_id, b.batch_name AS batch_name
          FROM teacher_allocations ta
          INNER JOIN batches b ON b.id = ta.batch_id
          WHERE ta.teacher_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$allocations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ✅ Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $title = trim($_POST['title']);

    if (count($allocations) === 1) {
        $allocation_id = $allocations[0]['allocation_id'];
        $batch_id = $allocations[0]['batch_id'];
    } else {
        $allocation_id = intval($_POST['allocation_id'] ?? 0);
        $batchQuery = $conn->prepare("SELECT batch_id FROM teacher_allocations WHERE id = ?");
        $batchQuery->bind_param("i", $allocation_id);
        $batchQuery->execute();
        $batchResult = $batchQuery->get_result()->fetch_assoc();
        $batch_id = $batchResult['batch_id'] ?? 0;
        $batchQuery->close();
    }

    if (!empty($_FILES['file']['name'])) {
        $uploadDir = "uploads/question_papers/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['file']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $sql = "INSERT INTO question_papers 
                    (title, file_path, allocation_id, batch_id, college_id, uploaded_by, uploaded_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("ssiiii", $title, $targetPath, $allocation_id, $batch_id, $_SESSION['college_id'], $teacher_id);
            $stmt2->execute();
            $stmt2->close();
            $success = "✅ Question paper uploaded successfully!";
        } else {
            $error = "❌ File upload failed.";
        }
    } else {
        $error = "⚠️ Please select a file to upload.";
    }
}

// ✅ Fetch teacher’s uploaded papers
$papers = [];
$fetchPapers = $conn->prepare("SELECT qp.id, qp.title, qp.file_path, qp.uploaded_at, 
                                      ta.subject, b.batch_name
                               FROM question_papers qp
                               INNER JOIN teacher_allocations ta ON ta.id = qp.allocation_id
                               INNER JOIN batches b ON b.id = qp.batch_id
                               WHERE qp.uploaded_by = ?
                               ORDER BY qp.uploaded_at DESC");
$fetchPapers->bind_param("i", $teacher_id);
$fetchPapers->execute();
$res = $fetchPapers->get_result();
$papers = $res->fetch_all(MYSQLI_ASSOC);
$fetchPapers->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher - Question Papers</title>
<style>
body { font-family: "Segoe UI", sans-serif; background: #f7f9fc; padding: 30px; }
.container { max-width: 700px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align:center; }
h2 { color: #276cdb; }
button.main-btn { margin: 10px; padding: 15px 25px; font-size: 18px; border:none; border-radius:8px; cursor:pointer; background:#276cdb; color:white; }
button.main-btn:hover { background:#174a9c; }
.hidden { display: none; margin-top:20px; text-align:left; }
input, select { width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:6px; }
form button { width:100%; padding:12px; background:#276cdb; color:white; border:none; border-radius:6px; font-size:16px; cursor:pointer; }
form button:hover { background:#174a9c; }
table { width:100%; border-collapse: collapse; margin-top:15px; }
th, td { padding:10px; border:1px solid #ccc; text-align:center; }
th { background:#276cdb; color:white; }
.message { font-weight:bold; margin:10px 0; }
.success { color:green; }
.error { color:red; }
</style>
<script>
function toggleSection(id) {
    document.getElementById('uploadSection').style.display = 'none';
    document.getElementById('viewSection').style.display = 'none';
    document.getElementById(id).style.display = 'block';
}
</script>
</head>
<body>
<div class="container">
    <h2>📚 Question Paper Management</h2>
    <button class="main-btn" onclick="toggleSection('uploadSection')">📤 Upload Question Paper</button>
    <button class="main-btn" onclick="toggleSection('viewSection')">📂 View Question Papers</button>

    <!-- Upload Section -->
    <div id="uploadSection" class="hidden">
        <h3>📤 Upload Question Paper</h3>
        <?php if (!empty($success)): ?><p class="message success"><?= $success ?></p><?php endif; ?>
        <?php if (!empty($error)): ?><p class="message error"><?= $error ?></p><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="upload" value="1">
            <label>Title</label>
            <input type="text" name="title" required>

            <?php if (count($allocations) > 1): ?>
                <label>Subject (Batch)</label>
                <select name="allocation_id" required>
                    <option value="">-- Select Subject --</option>
                    <?php foreach ($allocations as $a): ?>
                        <option value="<?= $a['allocation_id'] ?>">
                            <?= htmlspecialchars($a['subject']) ?> (<?= htmlspecialchars($a['batch_name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="allocation_id" value="<?= $allocations[0]['allocation_id'] ?>">
                <p><strong>Subject:</strong> <?= htmlspecialchars($allocations[0]['subject']) ?> (<?= htmlspecialchars($allocations[0]['batch_name']) ?>)</p>
            <?php endif; ?>

            <label>Upload File</label>
            <input type="file" name="file" required>

            <button type="submit">Upload</button>
        </form>
    </div>

    <!-- View Section -->
    <div id="viewSection" class="hidden">
        <h3>📂 My Uploaded Question Papers</h3>
        <?php if (!empty($papers)): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Batch</th>
                    <th>Uploaded At</th>
                    <th>File</th>
                </tr>
                <?php foreach ($papers as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td><?= htmlspecialchars($p['subject']) ?></td>
                    <td><?= htmlspecialchars($p['batch_name']) ?></td>
                    <td><?= htmlspecialchars($p['uploaded_at']) ?></td>
                    <td><a href="<?= $p['file_path'] ?>" target="_blank">📥 Download</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No question papers uploaded yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
