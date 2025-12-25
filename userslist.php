<?php
include "config.php";
session_start();

// ✅ Make sure teacher/admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    die("⛔ Unauthorized!");
}

$college_id = $_SESSION['college_id'];
$batch_id   = $_SESSION['batch_id'];

// ✅ Fetch only students from this teacher's batch
$sql = "SELECT * FROM users WHERE role='student' AND college_id='$college_id' AND batch_id='$batch_id' ORDER BY first_name ASC";
$result = mysqli_query($conn, $sql);

// Check if query fails
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
} else {
    echo "<h2 style='text-align:center;'>Students in Your Batch</h2>";
    echo "<table border='1' cellpadding='10' cellspacing='0' style='margin: auto; border-collapse: collapse; font-family: Arial; width:90%;'>";
    echo "<tr style='background-color: #2c3e50; color: white;'>
            <th>Sl. No.</th>
            <th>Name</th>
            <th>Admission No</th>
            <th>Email</th>
            <th>Register No</th>
            <th>DOB</th>
            <th>Course</th>
            <th>Created At</th>
        </tr>";

    $slno = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $fullName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);

        echo "<tr>
                <td>" . $slno . "</td>
                <td>" . $fullName . "</td>
                <td>" . htmlspecialchars($row['admission_no']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['register_no']) . "</td>
                <td>" . htmlspecialchars($row['dob']) . "</td>
                <td>" . htmlspecialchars($row['course']) . "</td>
                <td>" . htmlspecialchars($row['created_at']) . "</td>
            </tr>";
        $slno++;
    }

    echo "</table>";
}
?>
