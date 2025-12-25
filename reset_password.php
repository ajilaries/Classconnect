<?php
$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>
    <form method="POST" action="reset_password_process.php">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="password" name="new_password" placeholder="Enter new password" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
