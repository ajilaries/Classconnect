<?php
session_start();

// ✅ Only allow admins/teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit;
}

// ✅ Prevent cached version after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClassConnect - Teacher Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
  <style>
    /* Navbar Buttons */
    .nav-buttons {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .nav-buttons button,
    .nav-buttons form button {
      background-color: white;
      color: #276cdb;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .nav-buttons button:hover,
    .nav-buttons form button:hover {
      background-color: #e0e0e0;
      transform: translateY(-1px);
    }

    /* Profile Popup */
    .popup {
      display: none;
      position: fixed;
      z-index: 9999;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }

    .popup-content {
      background-color: white;
      color: black;
      margin: 100px auto;
      padding: 20px;
      width: 320px;
      border-radius: 10px;
      font-family: 'Segoe UI', sans-serif;
      position: relative;
    }

    .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <!-- Top Navbar -->
  <header class="navbar">
    <div class="logo">ClassConnect</div>
    <div class="nav-buttons">
      <!-- Profile Button -->
      <button id="profileBtn">👤 Profile</button>

      <!-- Theme Toggle -->
      <button onclick="toggleTheme()" id="themeToggle" title="Toggle Theme">🌙</button>

      <!-- Logout -->
      <form action="logout.php" method="POST" style="display:inline;">
        <button type="submit">Logout</button>
      </form>
    </div>
  </header>

  <!-- Profile Popup -->
  <div id="profilePopup" class="popup">
    <div class="popup-content">
      <span class="close">&times;</span>
      <h2>My Profile</h2>
      <p><strong>Name:</strong> <span id="name"></span></p>
      <p><strong>Admission No:</strong> <span id="admission_no"></span></p>
      <p><strong>Register No:</strong> <span id="register_no"></span></p>
      <p><strong>Email:</strong> <span id="email"></span></p>
      <p><strong>Role:</strong> <span id="role"></span></p>
      <p><strong>DOB:</strong> <span id="dob"></span></p>
      <p><strong>Class:</strong> <span id="class"></span></p>
      <p><strong>Course:</strong> <span id="course"></span></p>
    </div>
  </div>

  <!-- Main Content -->
  <main class="main-content">
    <div class="welcome">
      <h1>Welcome to ClassConnect</h1>
      <p>Your all-in-one teacher dashboard</p>
    </div>
    <div class="button-grid">
      <button onclick="window.location.href='classfeed.php';">📝 Class Feed</button>
      <button onclick="window.location.href='files_admin.php';">📁 Files</button>
      <button onclick="window.location.href='feedback.html';">💬 Feedback</button>
      <button onclick="window.location.href='timetable.html';">📅 Timetable</button>
      <button onclick="window.location.href='https://www.mgu.ac.in/examinations/results/';">📈 Results</button>
      <button onclick="window.location.href='admin_poll.html';">📊 Polls</button>
      <button onclick="window.location.href='notification.php';">🔔 Notifications</button>
      <button onclick="window.location.href='userslist.php';">Users</button>
      <button onclick="window.location.href='corner.php';">Teachers Corner</button>
      <button onclick="window.location.href='questionpapers.php';">Question Papers</button>
    </div>
  </main>

  <script>
    const profileBtn = document.getElementById('profileBtn');
    const profilePopup = document.getElementById('profilePopup');
    const closeBtn = document.querySelector('#profilePopup .close');

    profileBtn.onclick = () => {
      fetch("get_profile.php")
        .then(res => res.json())
        .then(data => {
          document.getElementById('profileBtn').innerText = data.name;

          if (data.role === "student") {
            document.querySelector("#admission_no").parentElement.style.display = "block";
            document.querySelector("#register_no").parentElement.style.display = "block";
            document.querySelector("#dob").parentElement.style.display = "block";
            document.querySelector("#course").parentElement.style.display = "block";
            document.querySelector("#class").parentElement.style.display = "block";

            document.getElementById('name').innerText = data.name;
            document.getElementById('admission_no').innerText = data.admission_no;
            document.getElementById('register_no').innerText = data.register_no;
            document.getElementById('dob').innerText = data.dob;
            document.getElementById('course').innerText = data.course;
            document.getElementById('email').innerText = data.email;
            document.getElementById('role').innerText = data.role;
            document.getElementById('class').innerText = data.class;
          } else if (data.role === "teacher") {
            // Hide student-only fields
            ["admission_no","register_no","dob","course","class"].forEach(id=>{
              document.getElementById(id).parentElement.style.display = "none";
            });

            document.getElementById('name').innerText = data.name;
            document.getElementById('email').innerText = data.email;
            document.getElementById('role').innerText = data.role;
          }

          profilePopup.style.display = 'block';
        })
        .catch(err => console.error("Profile fetch error:", err));
    };

    closeBtn.onclick = () => { profilePopup.style.display = 'none'; };
    window.onclick = (e) => { if(e.target === profilePopup) profilePopup.style.display = 'none'; };
  </script>

  <script src="studentdash.js"></script>
</body>
</html>
