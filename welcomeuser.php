<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: signup.html");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Welcome</title>
  <style>
    body { 
      display: flex; justify-content: center; align-items: center; 
      height: 100vh; font-family: Arial, sans-serif; 
      background: #f0f4f8;
    }
    .box {
      background: white; padding: 40px; border-radius: 12px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.1); text-align: center;
    }
    h1 { margin: 0; font-size: 28px; color: #333; }
  </style>
  <script>
    setTimeout(() => {
      <?php if ($role === "admin") { ?>
        window.location.href = "admindash.html";
      <?php } else { ?>
        window.location.href = "studentdash.html";
      <?php } ?>
    }, 3000); // 3 sec wait
  </script>
</head>
<body>
  <div class="box">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?> 🎉</h1>
    <p>Redirecting to your <?php echo $role; ?> dashboard...</p>
  </div>
</body>
</html>
