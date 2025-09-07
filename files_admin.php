<?php
session_start();
include "config.php";

// ✅ Check if teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit();
}

$college_id = $_SESSION['college_id'];
$batch_id   = $_SESSION['batch_id'];
$teacher_id = $_SESSION['user_id']; // ✅ assuming teacher's user_id is stored here

// ✅ Initialize $file_type to avoid undefined variable warning
$file_type = isset($_GET['file_type']) ? intval($_GET['file_type']) : 0;

// ✅ Fetch teacher's allocated subjects for this batch
$subjectStmt = $conn->prepare("
    SELECT subject 
    FROM teacher_allocations 
    WHERE teacher_id = ? AND batch_id = ?
");
$subjectStmt->bind_param("ii", $teacher_id, $batch_id);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();

$teacherSubjects = [];
while ($row = $subjectResult->fetch_assoc()) {
    $teacherSubjects[] = $row['subject'];
}

// ✅ If teacher has no subjects allocated, block access (or show empty table)
if (empty($teacherSubjects)) {
    die("<h3>⚠️ No subjects allocated to you for this batch.</h3>");
}

// ✅ Build dynamic placeholders for subject filtering
$placeholders = implode(',', array_fill(0, count($teacherSubjects), '?'));
$paramTypes = str_repeat('s', count($teacherSubjects)); // subject is varchar

// ✅ Handle Delete (with subject check!)
if (isset($_GET['delete'])) {
    $file_id = intval($_GET['delete']);

    // ✅ Delete only if file belongs to teacher's allocated subjects
    $deleteSql = "
        SELECT file_path FROM files 
        WHERE id = ? AND college_id = ? AND batch_id = ? AND subject IN ($placeholders)
    ";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ii" . $paramTypes, $file_id, $college_id, $batch_id, ...$teacherSubjects);
    $stmt->execute();
    $res = $stmt->get_result();
    $file = $res->fetch_assoc();

    if ($file) {
        $filePath = $file['file_path'];
        if (file_exists($filePath)) unlink($filePath);

        $delete = $conn->prepare("
            DELETE FROM files 
            WHERE id = ? AND college_id = ? AND batch_id = ? AND subject IN ($placeholders)
        ");
        $delete->bind_param("ii" . $paramTypes, $file_id, $college_id, $batch_id, ...$teacherSubjects);
        $delete->execute();
    }

    header("Location: files_admin.php");
    exit();
}

// ✅ Fetch file types
$file_types = [];
$type_query = $conn->prepare("SELECT id, type_name FROM file_types ORDER BY type_name ASC");
$type_query->execute();
$type_result = $type_query->get_result();
while ($row = $type_result->fetch_assoc()) {
    $file_types[] = $row;
}

// ✅ Fetch files with subject restriction + optional file_type filter
if ($file_type) {
    $sql = "
        SELECT f.id, f.file_name, f.file_path, f.subject, u.admission_no, ft.type_name AS file_type, 
               f.uploaded_at, f.deadline, u.first_name, u.last_name
        FROM files f
        JOIN users u ON f.user_id = u.id
        LEFT JOIN file_types ft ON f.file_type = ft.id
        WHERE f.file_type = ? AND f.college_id = ? AND f.batch_id = ? 
        AND f.subject IN ($placeholders)
        ORDER BY f.uploaded_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii" . $paramTypes, $file_type, $college_id, $batch_id, ...$teacherSubjects);
} else {
    $sql = "
        SELECT f.id, f.file_name, f.file_path, f.subject, u.admission_no, ft.type_name AS file_type, 
               f.uploaded_at, f.deadline, u.first_name, u.last_name
        FROM files f
        JOIN users u ON f.user_id = u.id
        LEFT JOIN file_types ft ON f.file_type = ft.id
        WHERE f.college_id = ? AND f.batch_id = ? 
        AND f.subject IN ($placeholders)
        ORDER BY f.uploaded_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii" . $paramTypes, $college_id, $batch_id, ...$teacherSubjects);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📂 File Management - Teacher</title>
    <link rel="stylesheet" href="files_admin.css">
    <style>
        .btn { padding: 8px 12px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); }
        .modal-content { background:white; margin:10% auto; padding:20px; width:400px; border-radius:8px; }
    </style>
</head>
<body>
<h2>📂 File Management</h2>

<!-- Action Buttons -->
<button class="btn" onclick="document.getElementById('typeModal').style.display='block'">➕ Create File Type</button>
<button class="btn" onclick="document.getElementById('deadlineModal').style.display='block'">📅 Set Deadline</button>
<hr>

<!-- Filter -->
<form method="GET" action="">
    <label>Filter by Type:</label>
    <select name="file_type" onchange="this.form.submit()">
        <option value="0">All</option>
        <?php foreach ($file_types as $t): ?>
            <option value="<?= $t['id'] ?>" <?= $file_type == $t['id'] ? "selected" : "" ?>>
                <?= htmlspecialchars($t['type_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<table border="1" cellpadding="8">
    <tr>
        <th>SL No</th>
        <th>Admission No</th>
        <th>File Name</th>
        <th>Subject</th>
        <th>Type</th>
        <th>Uploaded By</th>
        <th>Uploaded At</th>
        <th>Deadline</th>
        <th>Preview</th>
        <th>Download</th>
        <th>Action</th>
    </tr>
    <?php 
    $sl = 1;
    while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $sl++ ?></td>
        <td><?= htmlspecialchars($row['admission_no'] ?? 'N/A') ?></td>
        <td><?= htmlspecialchars($row['file_name']) ?></td>
        <td><?= htmlspecialchars($row['subject'] ?? 'N/A') ?></td>
        <td><?= htmlspecialchars($row['file_type'] ?? 'Unknown') ?></td>
        <td><?= htmlspecialchars($row['first_name']." ".$row['last_name']) ?></td>
        <td><?= $row['uploaded_at'] ?></td>
        <td><?= $row['deadline'] ?? "N/A" ?></td>
        <td><button class="preview-btn" data-file="<?= $row['file_path']; ?>">Preview</button></td>
        <td><a href="<?= $row['file_path'] ?>" target="_blank">Download</a></td>
        <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this file?')">🗑️ Delete</a></td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- (Modals stay same as your code) -->
<!-- Create File Type Modal -->
<div id="typeModal" class="modal">
    <div class="modal-content">
        <span style="float:right; cursor:pointer;" onclick="document.getElementById('typeModal').style.display='none'">&times;</span>
        <h3>Create New File Type</h3>
        <form action="create_file_type.php" method="POST">
            <input type="text" name="type_name" placeholder="Enter new type (e.g. Assignment 1)" required><br><br>
            <button class="btn" type="submit">Create</button>
        </form>
    </div>
</div>

<!-- Set Deadline Modal -->
<div id="deadlineModal" class="modal">
    <div class="modal-content">
        <span style="float:right; cursor:pointer;" onclick="document.getElementById('deadlineModal').style.display='none'">&times;</span>
        <h3>Set Deadline</h3>
        <form action="set_deadline.php" method="POST">
            <label for="file_type">Choose Type:</label><br>
            <select name="file_type" id="file_type" required>
                <?php foreach ($file_types as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['type_name']) ?></option>
                <?php endforeach; ?>
            </select><br><br>
            <label for="deadline">Deadline:</label><br>
            <input type="datetime-local" name="deadline" required><br><br>
            <button class="btn" type="submit">Set Deadline</button>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="modal">
    <div class="modal-content">
        <span id="closePreview" style="cursor:pointer; float:right;">&times;</span>
        <iframe id="previewFrame" src="" width="100%" height="500px"></iframe>
    </div>
</div>

<script>
document.querySelectorAll('.preview-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        let file = this.getAttribute('data-file');
        document.getElementById('previewFrame').src = file;
        document.getElementById('previewModal').style.display = 'block';
    });
});
document.getElementById('closePreview').onclick = function() {
    document.getElementById('previewModal').style.display = 'none';
    document.getElementById('previewFrame').src = "";
};
</script>

</body>
</html>
