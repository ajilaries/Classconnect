<?php
session_start();
include "config.php";

// Only teachers
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher'){
    header("Location: login.html");
    exit();
}

$college_id = $_SESSION['college_id'];
$batch_id   = $_SESSION['batch_id'];
$teacher_id = $_SESSION['user_id'];

// Handle creation of custom file type (form submission)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_numbered_type'])){
    $base_type_id = intval($_POST['base_type_id'] ?? 0);
    $new_type_name = trim($_POST['new_type_name'] ?? '');

    if($base_type_id && $new_type_name){
        $insert = $conn->prepare("INSERT INTO file_type_numbers (file_type_id, batch_id, type_label) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $base_type_id, $batch_id, $new_type_name);
        $insert->execute();
        $insert->close();

        $msg = "✅ Created new type: $new_type_name";
    } else {
        $msg = "❌ Select a base type and enter a custom name!";
    }
}

// Fetch teacher's subjects
$stmt = $conn->prepare("SELECT subject FROM teacher_allocations WHERE teacher_id=? AND batch_id=?");
$stmt->bind_param("ii",$teacher_id,$batch_id);
$stmt->execute();
$res = $stmt->get_result();
$teacherSubjects = [];
while($row = $res->fetch_assoc()) $teacherSubjects[] = $row['subject'];
if(empty($teacherSubjects)) die("<h3>⚠️ No subjects allocated to you for this batch.</h3>");

// Fetch files uploaded by students
$placeholders = implode(',', array_fill(0,count($teacherSubjects),'?'));
$paramTypes = str_repeat('s',count($teacherSubjects));
$sql = "SELECT f.id,f.file_name,f.file_path,f.subject,u.admission_no,f.title AS file_type,
        f.uploaded_at,f.deadline,u.first_name,u.last_name
        FROM files f
        JOIN users u ON f.user_id=u.id
        WHERE f.college_id=? AND f.batch_id=? AND f.subject IN ($placeholders)
        ORDER BY f.uploaded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii".$paramTypes,$college_id,$batch_id,...$teacherSubjects);
$stmt->execute();
$result = $stmt->get_result();
$filesArray = [];
while($row = $result->fetch_assoc()) $filesArray[] = $row;

// Fetch base file types
$baseTypes = [];
$res = $conn->query("SELECT id,type_name FROM file_types ORDER BY type_name ASC");
while($row = $res->fetch_assoc()) $baseTypes[] = $row;

// Fetch all custom file types for this batch (for dropdowns)
$customTypes = [];
$res = $conn->query("SELECT ftn.id, ft.type_name, ftn.type_label 
                     FROM file_type_numbers ftn
                     JOIN file_types ft ON ftn.file_type_id=ft.id
                     WHERE ftn.batch_id=$batch_id 
                     ORDER BY ft.type_name, ftn.type_label ASC");
while($row = $res->fetch_assoc()) $customTypes[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📂 File Management</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 20px;
    background: #f8f9fa;
    color: #333;
}

h2 {
    margin-bottom: 20px;
    color: #007bff;
}

h3 {
    margin-top: 30px;
    color: #444;
    border-left: 5px solid #007bff;
    padding-left: 10px;
}

button {
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    background: #007bff;
    color: #fff;
    font-weight: 600;
    transition: 0.3s;
}
button:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

input, select {
    padding: 8px 10px;
    margin: 5px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

label {
    font-weight: 600;
    margin-right: 6px;
}

form {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
    width: 400px;
    margin-top: 10px;
}

ul {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
    list-style-type: none;
}
ul li {
    padding: 6px 0;
    border-bottom: 1px solid #eee;
}
ul li:last-child {
    border-bottom: none;
}

table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
    background: #fff;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}
th, td {
    border: 1px solid #e0e0e0;
    padding: 10px;
    font-size: 14px;
}
th {
    background: #007bff;
    color: #fff;
    text-align: left;
}
tr:nth-child(even) {
    background: #f9f9f9;
}

a {
    color: #007bff;
    text-decoration: none;
    font-weight: 600;
}
a:hover {
    text-decoration: underline;
}

.filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    background: #fff;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
}
.btn-link {
    display: inline-block;
    padding: 6px 12px;
    background: #28a745;
    color: #fff !important;
    border-radius: 6px;
    transition: 0.3s;
}
.btn-link:hover {
    background: #218838;
    text-decoration: none;
}
</style>
</head>
<body>

<h2>📂 File Management</h2>

<?php if(!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<!-- Filters -->
<div class="filters">
    <label>Base Type:</label>
    <select id="filterBaseType">
        <option value="">All</option>
        <?php foreach($baseTypes as $bt): ?>
        <option value="<?= $bt['id'] ?>"><?= htmlspecialchars($bt['type_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Custom Type:</label>
    <select id="filterCustomType" disabled>
        <option value="">All</option>
    </select>

    <label>Search:</label>
    <input type="text" id="searchInput" placeholder="Search files...">

    <button onclick="searchInput.value=''; renderTable()">Clear Search</button>
</div>

<!-- File Type Buttons -->
<h3>File Types</h3>
<div style="margin-bottom:15px;">
    <button type="button" onclick="toggleSection('createForm')">➕ Create New File Type</button>
    <button type="button" onclick="toggleSection('existingList')">📋 View Existing File Types</button>
</div>

<!-- Create Custom File Type Form (Initially Hidden) -->
<div id="createForm" style="display:none;">
    <form method="post">
        <label>Base Type:</label>
        <select name="base_type_id" required>
            <option value="">Select Base Type</option>
            <?php foreach($baseTypes as $bt): ?>
            <option value="<?= $bt['id'] ?>"><?= htmlspecialchars($bt['type_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label>Custom File Type Name:</label>
        <input type="text" name="new_type_name" placeholder="Enter name e.g., Assignment 2" required>
        <br><br>
        <button type="submit" name="create_numbered_type">Create</button>
    </form>
</div>

<!-- Existing Custom Types (Initially Hidden) -->
<div id="existingList" style="display:none; margin-top:20px;">
    <h3>Existing Custom Types</h3>
    <ul>
    <?php foreach($customTypes as $ct): ?>
        <li><?= htmlspecialchars($ct['type_name'] . " → " . $ct['type_label']) ?></li>
    <?php endforeach; ?>
    </ul>
</div>

<!-- Files Table -->
<table id="filesTable">
<thead>
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
</tr>
</thead>
<tbody>
<?php foreach($filesArray as $idx=>$f): ?>
<tr>
<td><?= $idx+1 ?></td>
<td><?= $f['admission_no']??'N/A' ?></td>
<td><?= htmlspecialchars($f['file_name']) ?></td>
<td><?= htmlspecialchars($f['subject']??'N/A') ?></td>
<td><?= htmlspecialchars($f['file_type']??'Unknown') ?></td>
<td><?= htmlspecialchars($f['first_name'].' '.$f['last_name']) ?></td>
<td><?= $f['uploaded_at'] ?></td>
<td><?= $f['deadline']??'N/A' ?></td>
<td><a href="<?= $f['file_path'] ?>" target="_blank" class="btn-link">Preview</a></td>
<td><a href="<?= $f['file_path'] ?>" download class="btn-link">Download</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<script>
const tableBody = document.querySelector('#filesTable tbody');
const filterBase = document.getElementById('filterBaseType');
const filterCustom = document.getElementById('filterCustomType');
const searchInput = document.getElementById('searchInput');

// Toggle Sections
function toggleSection(id){
    const section = document.getElementById(id);
    section.style.display = (section.style.display === "none" || section.style.display === "") ? "block" : "none";
}

// Load custom types dynamically based on selected base type
filterBase.addEventListener('change', function(){
    const baseId = this.value;
    filterCustom.innerHTML = '<option value="">All</option>';
    filterCustom.disabled = true;

    if(!baseId) { renderTable(); return; }

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "get_custom_file_types.php?base_type_id=" + baseId + "&batch_id=<?= $batch_id ?>", true);
    xhr.onload = function(){
        if(xhr.status === 200){
            filterCustom.innerHTML += xhr.responseText;
            filterCustom.disabled = false;
            renderTable();
        } else alert('❌ Error loading custom types');
    };
    xhr.send();
});

filterCustom.addEventListener('change', renderTable);
searchInput.addEventListener('input', renderTable);

function renderTable(){
    const filterBaseVal = filterBase.selectedOptions[0].text.toLowerCase();
    const filterCustomVal = filterCustom.value.toLowerCase();
    const search = searchInput.value.toLowerCase();

    const rows = tableBody.querySelectorAll('tr');
    rows.forEach(row => {
        const type = row.cells[4].textContent.toLowerCase();
        const fileName = row.cells[2].textContent.toLowerCase();
        const uploadedBy = row.cells[5].textContent.toLowerCase();

        let show = true;
        if(filterBaseVal && !type.includes(filterBaseVal)) show=false;
        if(filterCustomVal && !type.includes(filterCustomVal)) show=false;
        if(search && !fileName.includes(search) && !uploadedBy.includes(search)) show=false;

        row.style.display = show ? '' : 'none';
    });
}
</script>

</body>
</html>
