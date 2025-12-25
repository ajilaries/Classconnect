<?php
session_start();
include "config.php";

// ✅ Check teacher login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "⛔ Unauthorized!";
    exit;
}

$teacher_id = $_SESSION['user_id'];

// ✅ Fetch all allocations for this teacher
$stmt = $conn->prepare("
    SELECT ta.id AS allocation_id, ta.batch_id, b.batch_name, d.department_name, ta.subject, b.class_code
    FROM teacher_allocations ta
    JOIN batches b ON ta.batch_id = b.id
    JOIN departments d ON ta.department_id = d.id
    WHERE ta.teacher_id = ?
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$allocations = $result->fetch_all(MYSQLI_ASSOC);

// ✅ If no allocations found
if (empty($allocations)) {
    echo "<p>⚠️ You have not been allocated to any classes yet. Please contact admin.</p>";
    exit;
}

// ✅ If only one allocation → skip this page and set directly
if (count($allocations) === 1) {
    $_SESSION['class_context'] = $allocations[0]['class_code'];
    $_SESSION['batch_id'] = $allocations[0]['batch_id']; // ✅ FIXED
    $_SESSION['subject']    = $selected['subject']; 
    header("Location: teacherdash.php");
    exit;
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['allocation_id'])) {
    $selected = intval($_POST['allocation_id']);
    foreach ($allocations as $alloc) {
        if ($alloc['allocation_id'] == $selected) {
            $_SESSION['class_context'] = $alloc['class_code'];
            $_SESSION['batch_id'] = $alloc['batch_id']; // ✅ FIXED
            header("Location: teacherdash.php");
            exit;
        }
    }
    $error = "⚠️ Invalid selection.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Your Class</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f4f4f4; }
        h1 { text-align: center; }
        .class-card { background: white; padding: 15px; margin: 10px auto; width: 400px;
            border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        button { padding: 10px 15px; background: #4CAF50; color: white;
            border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>

<h1>Choose Your Class</h1>

<?php if (!empty($error)): ?>
    <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <?php foreach ($allocations as $alloc): ?>
        <div class="class-card">
            <label>
                <input type="radio" name="allocation_id" value="<?= $alloc['allocation_id'] ?>" required>
                <strong><?= htmlspecialchars($alloc['batch_name']) ?></strong> <br>
                Department: <?= htmlspecialchars($alloc['department_name']) ?> <br>
                Subject: <?= htmlspecialchars($alloc['subject']) ?>
            </label>
        </div>
    <?php endforeach; ?>

    <button type="submit">Continue</button>
</form>

</body>
</html>
