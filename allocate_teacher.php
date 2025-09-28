<?php
session_start();
include "config.php";

// ✅ Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("⛔ Unauthorized!");
}

// ✅ Handle Deallocate / Delete / Edit actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    if ($action === 'delete_teacher') {
        $conn->query("DELETE FROM users WHERE id=$id AND role='teacher'");
        header("Location: allocate_teacher.php?department_id=" . ($_GET['department_id'] ?? ''));
        exit;
    }

    if ($action === 'deallocate') {
        $conn->query("DELETE FROM teacher_allocations WHERE id=$id");
        header("Location: allocate_teacher.php?department_id=" . ($_GET['department_id'] ?? ''));
        exit;
    }
}

// ✅ Get department if provided
$departmentId = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;

// ✅ Fetch departments for dropdown
$departments = $conn->query("SELECT * FROM departments ORDER BY department_name ASC")->fetch_all(MYSQLI_ASSOC);

// ✅ Fetch batches for allocation form
$batches = $conn->query("SELECT * FROM batches ORDER BY batch_name ASC")->fetch_all(MYSQLI_ASSOC);

// ✅ Fetch teachers and group allocations
$teachers = [];
if ($departmentId) {
    $stmt = $conn->prepare("
        SELECT u.id AS teacher_id, u.first_name, u.last_name, u.email,
               ta.id AS allocation_id, ta.subject, b.batch_name
        FROM users u
        LEFT JOIN teacher_allocations ta ON u.id = ta.teacher_id
        LEFT JOIN batches b ON ta.batch_id = b.id
        WHERE u.role='teacher' AND u.department_id = ?
        ORDER BY u.first_name
    ");
    $stmt->bind_param("i", $departmentId);
    $stmt->execute();
    $result = $stmt->get_result();


    while ($row = $result->fetch_assoc()) {
        $tid = $row['teacher_id'];
        if (!isset($teachers[$tid])) {
            $teachers[$tid] = [
                'teacher_id'  => $row['teacher_id'],
                'first_name'  => $row['first_name'],
                'last_name'   => $row['last_name'],
                'email'       => $row['email'],
                'allocations' => []
            ];
        }
        if ($row['allocation_id']) {
            $teachers[$tid]['allocations'][] = [
                'allocation_id' => $row['allocation_id'],
                'batch_name'    => $row['batch_name'],
                'subject'       => $row['subject']
            ];
        }
    }
}

// ✅ Handle allocation / edit submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['allocate_teacher'])) {
    $teacher_id    = intval($_POST['teacher_id']);
    $department_id = intval($_POST['department_id']);
    $batch_id      = intval($_POST['batch_id']);
    $subject       = trim($_POST['subject']);
    $allocation_id = isset($_POST['allocation_id']) ? intval($_POST['allocation_id']) : null;

    if ($teacher_id && $department_id && $batch_id && $subject !== '') {
        if ($allocation_id) {
            // Update existing allocation
            $stmt = $conn->prepare("UPDATE teacher_allocations SET batch_id=?, subject=? WHERE id=?");
            $stmt->bind_param("isi", $batch_id, $subject, $allocation_id);
        } else {
            // New allocation
            $stmt = $conn->prepare("INSERT INTO teacher_allocations (teacher_id, department_id, batch_id, subject) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $teacher_id, $department_id, $batch_id, $subject);
        }
        $stmt->execute();
        header("Location: allocate_teacher.php?department_id=" . $department_id);
        exit;
    } else {
        $error = "⚠️ Please fill all fields to allocate a subject.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Management</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
.container { max-width: 1100px; margin: auto; }
h1 { text-align: center; margin-bottom: 20px; }
button { background: #007bff; color: #fff; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin: 2px; }
button:hover { background: #0056b3; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: top; }
th { background: #eee; }
form { margin-top: 10px; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); max-width: 600px; }
label { display: block; margin-top: 10px; font-weight: bold; }
input, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
.error { color: red; margin-top: 10px; }
</style>
<script>
function toggleForm(id) {
    let form = document.getElementById("allocateForm_" + id);
    if (form) {
        form.style.display = form.style.display === "block" ? "none" : "block";
    }
}
</script>
</head>
<body>

<div class="container">
<h1>Teacher Management</h1>

<!-- Add Teacher Button -->
<form action="add_teacher.php" method="get">
    <button type="submit">➕ Add Teacher</button>
</form>
<form action="admindash.php" method="get" style="display:inline-block; margin-bottom: 10px;">
    <button type="submit" style="background:#28a745;">⬅ Back to Dashboard</button>
</form>

<!-- Select Department -->
<form method="get">
    <label>Select Department:</label>
    <select name="department_id" onchange="this.form.submit()">
        <option value="">-- Select Department --</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= $d['id'] ?>" <?= ($departmentId == $d['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['department_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>


<?php if ($departmentId): ?>
    <?php if (count($teachers) > 0): ?>
        <h2>Teachers in Department</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Allocations</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($teachers as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['first_name'] . " " . $t['last_name']) ?></td>
                <td><?= htmlspecialchars($t['email']) ?></td>
                <td>
                    <?php if (count($t['allocations']) > 0): ?>
                        <ul style="margin:0; padding-left:20px;">
                            <?php foreach ($t['allocations'] as $alloc): ?>
                                <li>
                                    <strong><?= htmlspecialchars($alloc['batch_name']) ?>:</strong> 
                                    <?= htmlspecialchars($alloc['subject']) ?>
                                    <button type="button" onclick="toggleForm('edit_<?= $alloc['allocation_id'] ?>')">Edit</button>
                                    <a href="?action=deallocate&id=<?= $alloc['allocation_id'] ?>&department_id=<?= $departmentId ?>">
                                        <button type="button">Deallocate</button>
                                    </a>
                                    <!-- Edit form for this allocation -->
                                    <div id="allocateForm_edit_<?= $alloc['allocation_id'] ?>" style="display:none; margin-top:10px;">
                                        <form method="POST">
                                            <input type="hidden" name="allocation_id" value="<?= $alloc['allocation_id'] ?>">
                                            <input type="hidden" name="teacher_id" value="<?= $t['teacher_id'] ?>">
                                            <input type="hidden" name="department_id" value="<?= $departmentId ?>">
                                            <label>Select Batch</label>
                                            <select name="batch_id" required>
                                                <?php foreach ($batches as $b): ?>
                                                    <option value="<?= $b['id'] ?>" <?= ($b['batch_name']==$alloc['batch_name']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($b['batch_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label>Subject</label>
                                            <input type="text" name="subject" value="<?= htmlspecialchars($alloc['subject']) ?>" required>
                                            <input type="hidden" name="allocate_teacher" value="1">
                                            <button type="submit">Update Allocation</button>
                                        </form>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <em>No allocations</em>
                    <?php endif; ?>
                </td>
                <td>
                    <button type="button" onclick="toggleForm('new_<?= $t['teacher_id'] ?>')">➕ Add Allocation</button>
                    <div id="allocateForm_new_<?= $t['teacher_id'] ?>" style="display:none; margin-top:10px;">
                        <form method="POST">
                            <input type="hidden" name="teacher_id" value="<?= $t['teacher_id'] ?>">
                            <input type="hidden" name="department_id" value="<?= $departmentId ?>">
                            <label>Select Batch</label>
                            <select name="batch_id" required>
                                <option value="">-- Select Batch --</option>
                                <?php foreach ($batches as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['batch_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label>Subject</label>
                            <input type="text" name="subject" placeholder="Enter subject" required>
                            <input type="hidden" name="allocate_teacher" value="1">
                            <button type="submit">Allocate</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No teachers found in this department.</p>
    <?php endif; ?>
<?php endif; ?>

</div>
</body>
</html>
