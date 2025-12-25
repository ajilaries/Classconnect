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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Student List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #276cdb;
            margin-bottom: 20px;
        }
        .back-btn {
            display: inline-block;
            margin: 10px 0 20px 20px;
            padding: 8px 16px;
            background: #555;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s ease;
        }
        .back-btn:hover {
            background: #333;
        }
        form {
            text-align: center;
            background: #fff;
            padding: 15px;
            display: inline-block;
            margin: 0 auto 20px;
            border-radius: 8px;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
        }
        select, button {
            padding: 8px 12px;
            margin: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            background: #276cdb;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.2s ease;
        }
        button:hover {
            background: #174ea6;
        }
        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 3px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th {
            background-color: #276cdb;
            color: white;
            padding: 10px;
            text-align: center;
        }
        td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            text-decoration: none;
            font-weight: bold;
        }
        a[href*="usersedit"] {
            color: #27ae60;
        }
        a[href*="usersdelete"] {
            color: #e74c3c;
        }
        p {
            text-align: center;
            color: red;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
            th, td {
                padding: 6px;
            }
        }
    </style>
</head>
<body>

<a href="admindash.html" class="back-btn">⬅ Back to Dashboard</a>

<h2> Admin - Student List</h2>

<div style="text-align:center;">
    <form method="GET">
        <label><b>Department:</b></label>
        <select name="department" onchange="this.form.submit()">
            <option value="">-- Select Department --</option>
            <?php while ($dept = mysqli_fetch_assoc($departments)) { ?>
                <option value="<?= $dept['id'] ?>" <?= ($selected_department == $dept['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept['department_name']) ?>
                </option>
            <?php } ?>
        </select>

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
</div>

<?php if (!empty($selected_department) && !empty($selected_batch) && mysqli_num_rows($students) > 0) { ?>
    <table>
        <tr>
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
                    <td><a href='usersedit.php?id={$row['id']}'>Edit</a></td>
                    <td><a href='usersdelete.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a></td>
                  </tr>";
            $sl++;
        }
        ?>
    </table>
<?php } elseif (!empty($selected_department) && !empty($selected_batch)) { ?>
    <p>No students found for this batch.</p>
<?php } ?>

</body>
</html>
