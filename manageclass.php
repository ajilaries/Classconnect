<?php
session_start();
include "config.php";

// Only admin
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.html");
    exit();
}

$msg = '';

// ----- Add new file type -----
if(isset($_POST['submit'])){
    $type_name = trim($_POST['type_name']);
    if($type_name){
        // Check if already exists
        $check = $conn->prepare("SELECT id FROM file_types WHERE type_name=?");
        $check->bind_param("s",$type_name);
        $check->execute();
        $check->store_result();
        if($check->num_rows > 0){
            $msg = "❌ File type already exists!";
        } else {
            $insert = $conn->prepare("INSERT INTO file_types (type_name) VALUES (?)");
            $insert->bind_param("s",$type_name);
            if($insert->execute()){
                $msg = "✅ File type added successfully!";
            } else {
                $msg = "❌ Database error!";
            }
        }
    } else {
        $msg = "❌ Enter a valid file type name!";
    }
}

// ----- Handle deletion of a single file type -----
if(isset($_GET['delete_type'])){
    $del_id = intval($_GET['delete_type']);

    // Optional: remove all files related to this type
    $conn->query("DELETE FROM uploads WHERE type_id=$del_id"); 

    $conn->query("DELETE FROM file_types WHERE id=$del_id");
    header("Location: manageclass.php");
    exit();
}

// ----- Handle clearing an entire batch -----
if(isset($_GET['clear_batch'])){
    $batch_id = intval($_GET['clear_batch']);

    // Delete all ClassConnect posts
    $conn->query("DELETE FROM classfeed WHERE batch_id=$batch_id");

    // Delete all uploaded files
    $conn->query("DELETE FROM uploads WHERE batch_id=$batch_id");

    // Delete all teacher allocations
    $conn->query("DELETE FROM teacher_allocations WHERE batch_id=$batch_id");

    // Delete all students
    $conn->query("DELETE FROM users WHERE batch_id=$batch_id AND role='student'");

    // Optionally delete the batch itself
    $conn->query("DELETE FROM batches WHERE id=$batch_id");

    $msg = "✅ Batch cleared successfully!";
}

// Fetch existing file types
$types = $conn->query("SELECT id, type_name FROM file_types ORDER BY type_name ASC");

// Fetch all batches
$batches = $conn->query("SELECT id, batch_name, class_code FROM batches ORDER BY batch_name ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Classes & File Types - Admin</title>
<style>
body{font-family:sans-serif; padding:20px; background:#f7f9fc;}
h2{margin-bottom:15px; color:#276cdb;}
input, button{padding:6px; margin:5px;}
button{cursor:pointer; border:none; background:#276cdb; color:#fff; border-radius:4px;}
button:hover{background:#1d4ed8;}
table{border-collapse:collapse; width:100%; margin-top:20px; background:#fff; border-radius:8px; overflow:hidden;}
th, td{border:1px solid #ccc; padding:8px;}
th{background:#e0e7ff;}
a.delete, a.clear { color:#ef4444; text-decoration:none; font-weight:bold; }
a.delete:hover, a.clear:hover { color:#b91c1c; }
.msg{margin:10px 0; font-weight:bold;}
.container {max-width:900px; margin:auto;}
</style>
</head>
<body>
<div class="container">
<h2>🗂 Manage Base File Types</h2>
<?php if($msg) echo "<div class='msg'>{$msg}</div>"; ?>

<form method="post">
    <input type="text" name="type_name" placeholder="Enter file type name" required>
    <button type="submit" name="submit">Add File Type</button>
</form>

<table>
    <thead>
        <tr>
            <th>SL No</th>
            <th>Type Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; while($row = $types->fetch_assoc()): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['type_name']) ?></td>
            <td>
                <a href="manageclass.php?delete_type=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this file type and all associated files?')">🗑️ Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h2>📂 Manage Batches</h2>
<table>
    <thead>
        <tr>
            <th>SL No</th>
            <th>Batch Name</th>
            <th>Class Code</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; while($batch = $batches->fetch_assoc()): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($batch['batch_name']) ?></td>
            <td><?= htmlspecialchars($batch['class_code']) ?></td>
            <td>
                <a href="manageclass.php?clear_batch=<?= $batch['id'] ?>" class="clear" onclick="return confirm('⚠️ Clear this batch? All students, files, posts, and allocations will be deleted!')">🗑️ Clear Batch</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>
</body>
</html>
