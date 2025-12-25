<?php
session_start();
include "config.php";

if (isset($_POST['go'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Get values from row
        $password = $row['password'];
        $role = $row['role'];

        // Password check (you can use password_verify if it's hashed)
        if ($password == $password) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['first_name'];
            $_SESSION['name'] = $row['last_name'];
            $_SESSION['role'] = $role;

            echo "Welcome buddy";

            // Redirect based on role
            if ($role == 'admin') {
                header("Location: admindash.html"); // teacher is admin
                exit();
            } else {
                header("Location: studentdash.html");
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Fetch user info
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // ✅ Verify password
        if (password_verify($password, $row['password'])) {

            // ✅ Set basic session variables
            $_SESSION['user_id']      = $row['id'];
            $_SESSION['first_name']  = $row['first_name'];
            $_SESSION['last_name']   = $row['last_name'];
            $_SESSION['role']        = $row['role'];
            $_SESSION['college_id']  = $row['college_id'];

            // ✅ STUDENT LOGIN (Fetch batch + department info)
            if ($row['role'] === 'student') {
                $batch_id = $row['batch_id'];

                if (!empty($batch_id)) {
                    // Store immediately, so session always has batch_id
                    $_SESSION['batch_id'] = $batch_id;

                    // Fetch batch + department info for convenience
                    $batch_stmt = $conn->prepare("
                        SELECT b.class_code, d.id AS department_id, d.department_name
                        FROM batches b
                        JOIN departments d ON b.department_id = d.id
                        WHERE b.id = ?
                    ");
                    $batch_stmt->bind_param("i", $batch_id);
                    $batch_stmt->execute();
                    $batch_info_res = $batch_stmt->get_result();

                    if ($batch_info_res->num_rows === 1) {
                        $batch = $batch_info_res->fetch_assoc();
                        $_SESSION['class_code']      = $batch['class_code'];
                        $_SESSION['department_id']   = $batch['department_id'];
                        $_SESSION['department_name'] = $batch['department_name'];
                    }
                    $batch_stmt->close();
                } else {
                    echo "<script>alert('No batch assigned for this student. Contact admin.'); window.location='login.html';</script>";
                    exit();
                }
            }

            // ✅ TEACHER LOGIN (Check allocated batches)
            if ($row['role'] === 'teacher') {
                $alloc_stmt = $conn->prepare("
                SELECT ta.batch_id, ta.subject, b.class_code
                FROM teacher_allocations ta
                JOIN batches b ON ta.batch_id = b.id
                WHERE ta.teacher_id = ?
                ");

                $alloc_stmt->bind_param("i", $row['id']);
                $alloc_stmt->execute();
                $alloc_res = $alloc_stmt->get_result();

                if ($alloc_res->num_rows === 1) {
                $alloc = $alloc_res->fetch_assoc();
                $_SESSION['batch_id']   = $alloc['batch_id'];
                $_SESSION['class_code'] = $alloc['class_code'];
                $_SESSION['subject']    = $alloc['subject'];  // <-- store subject too
                header("Location: teacherdash.php");
                exit();
                }
                 else {
                    $_SESSION['teacher_batches'] = $alloc_res->fetch_all(MYSQLI_ASSOC);
                    header("Location: choose_class.php");
                    exit();
                }
            }

            // ✅ Redirect based on role
            switch ($row['role']) {
                case 'super_admin':
                    header("Location: superadmindash.php");
                    break;
                case 'admin':
                    header("Location: admindash.php");
                    break;
                case 'student':
                    header("Location: studentdash.php");
                    break;
                default:
                    header("Location: login.html");
            }
            exit();

        } else {
            echo "<script>alert('Wrong password!'); window.location='login.html';</script>";
            exit();
        }

    } else {
        echo "<script>alert('Invalid email!'); window.location='login.html';</script>";
        exit();
    }

    $stmt->close();
}

$conn->close();
?>


