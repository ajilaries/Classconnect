<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'], $_SESSION['batch_id'], $_SESSION['college_id'])) {
    die("❌ Session expired. Please log in again.");
}

$user_id    = $_SESSION['user_id'];
$batch_id   = $_SESSION['batch_id'];
$college_id = $_SESSION['college_id'];

// ✅ Fetch file categories
$catResult = $conn->query("SELECT * FROM file_types ORDER BY type_name ASC");

// ✅ Fetch subjects allocated to student's batch
$subjectStmt = $conn->prepare("
    SELECT DISTINCT subject 
    FROM teacher_allocations
    WHERE batch_id = ? ORDER BY subject ASC
");
$subjectStmt->bind_param("i", $batch_id);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();

$subjects = [];
while ($sub = $subjectResult->fetch_assoc()) $subjects[] = $sub['subject'];
$subjectStmt->close();

// ✅ Handle file upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {
    $fileName      = mysqli_real_escape_string($conn, $_POST['file_name']);
    $file_type_num = intval($_POST['file_type_number']); // numbered type id
    $subject       = mysqli_real_escape_string($conn, $_POST['subject']);

    // 🔹 Verify subject
    $checkSub = $conn->prepare("SELECT subject FROM teacher_allocations WHERE batch_id=? AND subject=? LIMIT 1");
    $checkSub->bind_param("is", $batch_id, $subject);
    $checkSub->execute();
    $resSub = $checkSub->get_result();
    if($resSub->num_rows === 0) {
        die("<script>alert('❌ Invalid subject'); window.location.href='upload_file.php';</script>");
    }
    $checkSub->close();

    // 🔹 Get full numbered type name
// 🔹 Get custom type label
$typeQuery = $conn->prepare("
    SELECT ftn.type_label
    FROM file_type_numbers ftn
    WHERE ftn.id=? AND ftn.batch_id=?
");
$typeQuery->bind_param("ii", $file_type_num, $batch_id);
$typeQuery->execute();
$typeRow = $typeQuery->get_result()->fetch_assoc();
$typeQuery->close();

if(!$typeRow) {
    die("<script>alert('❌ Invalid file type'); window.location.href='upload_file.php';</script>");
}

// Use the custom label directly
$fullTypeName = $typeRow['type_label'];


    // 🔹 Upload file
    $uploadDir = "uploads/";
    if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
    $uniqueName = time() . "_" . basename($_FILES["file"]["name"]);
    $filePath = $uploadDir . $uniqueName;

    if(move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
        $stmt = $conn->prepare("
            INSERT INTO files (user_id, file_name, title, file_type, subject, file_path, college_id, batch_id, uploaded_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("issssiii", $user_id, $fileName, $fullTypeName, $fullTypeName, $subject, $filePath, $college_id, $batch_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('✅ File uploaded successfully'); window.location.href='upload_file.php';</script>";
    } else {
        echo "<script>alert('❌ File upload failed');</script>";
    }
}

// ✅ Fetch student uploads
$myFiles = $conn->prepare("
    SELECT id, title, file_name, file_type, file_path, uploaded_at, subject
    FROM files
    WHERE batch_id=? AND college_id=? AND user_id=?
    ORDER BY uploaded_at DESC
");
$myFiles->bind_param("iii", $batch_id, $college_id, $user_id);
$myFiles->execute();
$myFilesResult = $myFiles->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Files</title>
<link rel="stylesheet" href="upload_files.css">
</head>
<body>
    <a href="studentdash.php" class="btn-back">⬅ Back to Dashboard</a>

<h2>📤 Upload Files</h2>

<!-- Upload button -->
<button id="showUploadBtn">➕ Upload a File</button>

<!-- Hidden form -->
<div id="uploadForm" style="display:none; margin-top:20px;">
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="file_name" placeholder="Enter File Name" required><br><br>

        <!-- Base type dropdown -->
        <select id="categoryDropdown" required>
            <option value="">Select Category</option>
            <?php while($cat = $catResult->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['type_name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <!-- Numbered type dropdown -->
        <select name="file_type_number" id="fileTypeDropdown" required disabled>
            <option value="">Select Type</option>
        </select><br><br>

        <!-- Subject -->
        <select name="subject" required>
            <option value="">Select Subject</option>
            <?php foreach($subjects as $sub): ?>
                <option value="<?= htmlspecialchars($sub) ?>"><?= htmlspecialchars($sub) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="file" name="file" required><br><br>
        <button type="submit" id="uploadBtn" disabled>Upload</button>
    </form>
</div>

<hr>


<h2>📑 Your Uploaded Files</h2>
<?php if($myFilesResult->num_rows>0): ?>
<table border="1" cellpadding="8">
<tr>
    <th>Subject</th>
    <th>Type</th>
    <th>File Name</th>
    <th>Uploaded At</th>
    <th>Actions</th>
</tr>
<?php while($row = $myFilesResult->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['subject'] ?? 'N/A') ?></td>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td><?= htmlspecialchars($row['file_name']) ?></td>
    <td><?= $row['uploaded_at'] ?></td>
    <td><a href="<?= $row['file_path'] ?>" target="_blank">Download</a></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>You haven't uploaded any files yet.</p>
<?php endif; ?>

<script>
    // Toggle upload form
const showUploadBtn = document.getElementById('showUploadBtn');
const uploadForm = document.getElementById('uploadForm');

showUploadBtn.addEventListener('click', () => {
    if (uploadForm.style.display === "none") {
        uploadForm.style.display = "block";
        showUploadBtn.textContent = "✖ Close Upload Form";
    } else {
        uploadForm.style.display = "none";
        showUploadBtn.textContent = "➕ Upload a File";
    }
});


// Elements
const categoryDropdown = document.getElementById('categoryDropdown');
const fileTypeDropdown = document.getElementById('fileTypeDropdown');
const uploadBtn = document.getElementById('uploadBtn');

// Update numbered types based on selected category
categoryDropdown.addEventListener('change', function() {
    const categoryId = this.value;

    // Reset dropdown and disable submit
    fileTypeDropdown.innerHTML = '<option value="">Select Type</option>';
    fileTypeDropdown.disabled = true;
    uploadBtn.disabled = true;

    if (!categoryId) return;

    // Fetch options (plain HTML) from PHP
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "get_custom_file_types.php?category_id=" + categoryId + "&batch_id=<?= $batch_id ?>", true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            fileTypeDropdown.innerHTML = '<option value="">Select Type</option>' + xhr.responseText;
            fileTypeDropdown.disabled = false;
        } else {
            alert('Error loading numbered types');
        }
    };
    xhr.send();
});

// Enable upload button only when numbered type selected
fileTypeDropdown.addEventListener('change', function() {
    uploadBtn.disabled = !this.value;
});

</script>
</body>
</html>
