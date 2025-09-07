<?php
session_start();
// ✅ Only allow students
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
    exit;
}

// ✅ Prevent cached version from being shown after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ClassConnect - Student Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
  <style>
    /* Navbar buttons */
    .nav-buttons {
      display: flex;
      gap: 10px;
      position: absolute;
      top: 20px;
      right: 20px;
    }

    .nav-buttons button {
      padding: 5px 12px;
      font-weight: bold;
      background-color: white;
      color: black;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .nav-buttons button:hover {
      background-color: #f0f0f0;
    }

    /* Profile popup */
    .popup {
      display: none;
      position: fixed;
      z-index: 9999;
      padding-top: 100px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }

    .popup-content {
      background-color: white;
      color: black;
      margin: auto;
      padding: 20px;
      width: 300px;
      border-radius: 10px;
      font-family: 'Segoe UI', sans-serif;
    }

    .close {
      float: right;
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
      <button id="profileBtn">👤 Profile</button>
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
      <p>Your all-in-one student dashboard</p>
    </div>
    <div class="button-grid">
      <button onclick="window.location.href='classfeed.php';">📝 Class Feed</button>
      <button onclick="window.location.href='upload_file.php';">📁 Files</button>
      <button onclick="window.location.href='feedback.html';">💬 Feedback</button>
      <button onclick="window.location.href='timetable_uploaded.php';">📅 Timetable</button>
      <button onclick="window.location.href='https://www.mgu.ac.in/examinations/results/';">📈 Results</button>
      <button onclick="window.location.href='vote_poll.php';">📊 Polls</button>
      <button onclick="window.location.href='notification_student.php';">🔔 Notifications</button>
      <button onclick="window.location.href='corner.php';">Teachers Corner</button>
      <button onclick="window.location.href='questionpapersstudent.php';">Question Papers</button>
      <button onclick="window.location.href='memories.php';">Rewind</button>
    </div>
  </main>

  <script>
    const profileBtn = document.getElementById("profileBtn");
    const profilePopup = document.getElementById("profilePopup");
    const closeBtn = document.querySelector(".close");

    profileBtn.onclick = () => {
      fetch("get_profile.php")
        .then(res => res.json())
        .then(data => {
          document.getElementById('profileBtn').innerText = data.name;
          document.getElementById('name').innerText = data.name;
          document.getElementById('admission_no').innerText = data.admission_no;
          document.getElementById('register_no').innerText = data.register_no;
          document.getElementById('dob').innerText = data.dob;
          document.getElementById('course').innerText = data.course;
          document.getElementById('email').innerText = data.email;
          document.getElementById('role').innerText = data.role;
          document.getElementById('class').innerText = data.class;

          profilePopup.style.display = 'block';
        })
        .catch(error => {
          console.error("Error fetching profile:", error);
          alert("⚠️ Failed to load profile");
        });
    };

    closeBtn.onclick = () => { profilePopup.style.display = 'none'; };
    window.onclick = (e) => { if (e.target === profilePopup) profilePopup.style.display = 'none'; };

    window.addEventListener("DOMContentLoaded", () => {
      fetch("get_profile.php")
        .then(res => res.json())
        .then(data => {
          document.getElementById('profileBtn').innerText = data.name;
        });
    });
  </script>
  <script src="toogletheme"></script>
</body>
</html>
