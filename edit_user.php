<?php
session_start();
include "config.php";

// ✅ Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "⛔ Unauthorized!";
    exit;
}

// ✅ Validate user_id from GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("⚠️ No user selected.");
}

$user_id = intval($_GET['id']);

// ✅ Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("⚠️ User not found.");
}
$user = $result->fetch_assoc();

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = trim($_POST['email']);
    $role        = $_POST['role'];
    $class_code  = trim($_POST['class_code']);
    $batch_id    = intval($_POST['batch_id']);

    $update = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=?, class_code=?, batch_id=? WHERE id=?");
    $update->bind_param("sssssii", $first_name, $last_name, $email, $role, $class_code, $batch_id, $user_id);

    if ($update->execute()) {
        header("Location: classes.php?success=1");
        exit;
    } else {
        echo "❌ Error updating user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
        button { margin-top: 15px; background: #007bff; color: white; border: none; padding: 10px; width: 100%; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .back { display: block; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit User</h2>
    <form method="POST">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Role</label>
        <select name="role" required>
            <option value="student" <?php echo $user['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
            <option value="teacher" <?php echo $user['role'] === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>

        <label>Class Code</label>
        <input type="text" name="class_code" value="<?php echo htmlspecialchars($user['class_code']); ?>">

        <label>Batch</label>
        <select name="batch_id" required>
            <?php
            $batches = $conn->query("SELECT * FROM batches");
            while ($batch = $batches->fetch_assoc()) {
                $selected = $batch['id'] == $user['batch_id'] ? "selected" : "";
                echo "<option value='{$batch['id']}' $selected>{$batch['batch_name']} ({$batch['class_code']})</option>";
            }
            ?>
        </select>

        <button type="submit">Update User</button>
    </form>

    <a href="classes.php" class="back">⬅ Back to Classes</a>
</div>

</body>
</html>
