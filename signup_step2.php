<?php
session_start();
include "config.php";

// 1️⃣ Ensure Step 1 was completed
if (!isset($_SESSION['temp_user_id'])) {
    die("⛔ Session expired. Please start signup again.");
}

$temp_user_id = intval($_SESSION['temp_user_id']);

// 2️⃣ Fetch user's college_id
$stmt = $conn->prepare("SELECT college_id FROM users WHERE id = ?");
$stmt->bind_param("i", $temp_user_id);
$stmt->execute();
$stmt->bind_result($college_id);
$stmt->fetch();
$stmt->close();

if (!$college_id) {
    die("⛔ User not found. Please start signup again.");
}

// 3️⃣ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_code = trim($_POST['class_code']);

    if (empty($class_code)) {
        $error = "⚠️ Please enter the class code.";
    } else {
        // 4️⃣ Verify class exists for this college
        $stmt = $conn->prepare("
            SELECT b.id AS batch_id, d.id AS department_id
            FROM batches b
            JOIN departments d ON b.department_id = d.id
            WHERE b.class_code = ? AND d.college_id = ?
        ");
        $stmt->bind_param("si", $class_code, $college_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($batch_id, $department_id);

        if ($stmt->num_rows === 0) {
            $error = "❌ Invalid class code for your college. Contact admin.";
        } else {
            $stmt->fetch();
            
            // 5️⃣ Update user with batch_id & department_id
            $update = $conn->prepare("UPDATE users SET batch_id = ?, department_id = ? WHERE id = ?");
            $update->bind_param("iii", $batch_id, $department_id, $temp_user_id);

            if ($update->execute()) {
                unset($_SESSION['temp_user_id']);
                $_SESSION['user_id'] = $temp_user_id;

                // ✅ Redirect to dashboard
                header("Location: studentdash.php");
                exit();
            } else {
                $error = "❌ Database error: " . $update->error;
            }
            $update->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Signup - Step 2</title>
<link rel="stylesheet" href="signup.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo"><h2>CLASSCONNECT</h2></div>
        <p>Enter your class code to join your classroom</p>
    </div>
</header>
<main>
<div class="signup-box">
    <h2>SIGNUP - Step 2</h2>

    <?php if (!empty($error)) {
        echo "<p style='color:red; font-weight:bold;'>$error</p>";
    } ?>

    <form action="" method="post" onsubmit="return validateForm();">
        <input type="text" name="class_code" id="class_code" placeholder="Enter class code" required>
        <br><br>
        <input type="submit" value="Join Class">
    </form>
</div>
</main>
<script>
function validateForm() {
    const classCode = document.getElementById("class_code").value.trim();
    if (!classCode) {
        alert("⚠️ Please enter your class code.");
        return false;
    }
    return true;
}
</script>
</body>
</html>
