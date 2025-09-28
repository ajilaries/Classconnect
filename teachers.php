<?php
session_start();
include "config.php";

// ✅ Check admin login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "⛔ Unauthorized!";
    exit;
}

// ✅ Handle deallocation (if admin clicked delete)
if (isset($_GET['delete_allocation'])) {
    $alloc_id = intval($_GET['delete_allocation']);
    $stmt = $conn->prepare("DELETE FROM teacher_allocations WHERE id = ?");
    $stmt->bind_param("i", $alloc_id);
    $stmt->execute();
    header("Location: teachers.php?success=deleted");
    exit;
}

// ✅ Fetch teachers with allocations
$query = "
SELECT 
    u.id AS teacher_id, 
    u.first_name, 
    u.last_name, 
    u.email, 
    ta.id AS allocation_id,
    b.batch_name, 
    d.department_name, 
    ta.subject
FROM users u
LEFT JOIN teacher_allocations ta ON u.id = ta.teacher_id
LEFT JOIN batches b ON ta.batch_id = b.id
LEFT JOIN departments d ON ta.department_id = d.id
WHERE u.role = 'teacher'
ORDER BY u.first_name ASC
";

$result = $conn->query($query);

$teachers = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['teacher_id'];
    if (!isset($teachers[$id])) {
        $teachers[$id] = [
            'id' => $id,
            'name' => $row['first_name'] . " " . $row['last_name'],
            'email' => $row['email'],
            'allocations' => []
        ];
    }

    if (!empty($row['subject'])) {
        $teachers[$id]['allocations'][] = [
            'allocation_id' => $row['allocation_id'],
            'subject' => $row['subject'],
            'batch' => $row['batch_name'],
            'department' => $row['department_name']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teachers & Allocations</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 90%; margin: auto; border-collapse: collapse; background: #f9f9f9; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: top; }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .allocations { font-size: 14px; color: #333; }
        .allocation-item { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .no-allocations { color: #888; font-style: italic; }
        a.button {
            display: inline-block;
            padding: 5px 10px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover { background: #45a049; }
        a.delete {
            color: white;
            background: red;
            padding: 2px 8px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
        }
        a.delete:hover { background: darkred; }
    </style>
</head>
<body>

<h1>Teachers & Allocated Subjects</h1>

<?php if (empty($teachers)): ?>
    <p style="text-align:center; color:#888;">No teachers found.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Teacher</th>
            <th>Email</th>
            <th>Allocations</th>
            <th>Action</th>
        </tr>
        <?php foreach ($teachers as $teacher): ?>
            <tr>
                <td><?= htmlspecialchars($teacher['name']) ?></td>
                <td><?= htmlspecialchars($teacher['email']) ?></td>
                <td class="allocations">
                    <?php if (!empty($teacher['allocations'])): ?>
                        <?php foreach ($teacher['allocations'] as $alloc): ?>
                            <div class="allocation-item">
                                <span>
                                    <strong><?= htmlspecialchars($alloc['subject']) ?></strong>
                                    (<?= htmlspecialchars($alloc['batch']) ?> - <?= htmlspecialchars($alloc['department']) ?>)
                                </span>
                                <a class="delete" href="teachers.php?delete_allocation=<?= urlencode($alloc['allocation_id']) ?>"
                                   onclick="return confirm('Are you sure you want to deallocate this subject?');">
                                   ❌
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="no-allocations">No allocations yet</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="button" href="allocate_teacher.php?teacher_id=<?= urlencode($teacher['id']) ?>">
                        Allocate
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
