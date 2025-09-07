<?php
include "config.php";
session_start();

// ✅ Fetch all departments
$departments = mysqli_query($conn, "SELECT * FROM departments ORDER BY department_name ASC");

// ✅ Get selected values (if any)
$selected_department = $_GET['department'] ?? '';
$selected_batch = $_GET['batch'] ?? '';

// ✅ Fetch batches for selected department
$batches = [];
if (!empty($selected_department)) {
    $batches = mysqli_query($conn, "SELECT * FROM batches WHERE department_id = '$selected_department' ORDER BY batch_name ASC");
}

// ✅ Fetch students based on filters
$students = [];
if (!empty($selected_department) && !empty($selected_batch)) {
    $sql = "SELECT u.id, u.first_name, u.last_name, u.admission_no, u.register_no, u.dob, 
                   u.email, u.course, u.created_at, b.batch_name, d.department_name
            FROM users u
            INNER JOIN batches b ON u.batch_id = b.id
            INNER JOIN departments d ON b.department_id = d.id
            WHERE b.department_id = '$selected_department' AND b.id = '$selected_batch' AND u.role = 'student'
            ORDER BY u.first_name ASC";
    $students = mysqli_query($conn, $sql);
}
?>

<h2 style="text-align:center;">👩‍🎓 Admin - Student List</h2>

<form method="GET" style="text-align:center; margin-bottom: 20px;">
    <!-- Department Dropdown -->
    <label><b>Department:</b></label>
    <select name="department" onchange="this.form.submit()">
        <option value="">-- Select Department --</option>
        <?php while ($dept = mysqli_fetch_assoc($departments)) { ?>
            <option value="<?= $dept['id'] ?>" <?= ($selected_department == $dept['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($dept['department_name']) ?>
            </option>
        <?php } ?>
    </select>

    <!-- Batch Dropdown -->
    <?php if (!empty($selected_department)) { ?>
        <label><b>Batch:</b></label>
        <select name="batch">
            <option value="">-- Select Batch --</option>
            <?php while ($batch = mysqli_fetch_assoc($batches)) { ?>
                <option value="<?= $batch['id'] ?>" <?= ($selected_batch == $batch['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($batch['batch_name']) ?>
                </option>
            <?php } ?>
        </select>
        <button type="submit">View Students</button>
    <?php } ?>
</form>

<?php if (!empty($selected_department) && !empty($selected_batch) && mysqli_num_rows($students) > 0) { ?>
    <table border="1" cellpadding="10" cellspacing="0" style="margin:auto; border-collapse: collapse; font-family: Arial;">
        <tr style="background-color: #2c3e50; color: white;">
            <th>Sl. No</th>
            <th>Admission No</th>
            <th>Name</th>
            <th>DOB</th>
            <th>Register No</th>
            <th>Email</th>
            <th>Course</th>
            <th>Batch</th>
            <th>Department</th>
            <th>Created At</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php
        $sl = 1;
        while ($row = mysqli_fetch_assoc($students)) {
            $fullName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
            echo "<tr>
                    <td>{$sl}</td>
                    <td>{$row['admission_no']}</td>
                    <td>{$fullName}</td>
                    <td>{$row['dob']}</td>
                    <td>{$row['register_no']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['course']}</td>
                    <td>{$row['batch_name']}</td>
                    <td>{$row['department_name']}</td>
                    <td>{$row['created_at']}</td>
                    <td><a href='usersedit.php?id={$row['id']}' style='color:green;'>Edit</a></td>
                    <td><a href='usersdelete.php?id={$row['id']}' style='color:red;' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a></td>
                  </tr>";
            $sl++;
        }
        ?>
    </table>
<?php } elseif (!empty($selected_department) && !empty($selected_batch)) { ?>
    <p style="text-align:center; color:red;">No students found for this batch.</p>
<?php } ?>
