<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form method="POST" action="forgot_password_process.php">
        <input type="email" name="email" placeholder="Enter your email" required><br><br>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
