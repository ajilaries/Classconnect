<?php
session_start();

// ✅ Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
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

</head>
<body>
    <link rel="stylesheet" href="dashboard.css">
  <!-- Top Navbar -->
  <header class="navbar">
    <div class="logo">ClassConnect</div>
    <div class="nav-buttons">
      <!-- Add this somewhere in your header -->
<button id="profileBtn">👤 Profile</button>

<!-- Hidden popup box -->
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

<style>
  #profileBtn {
    position: absolute;
    top: 30px;
    right:150px;
    padding: 5px 10px;
    font-weight: bold;
    background-color: white;
    color: black;
    border: none;
    border-radius: 8px;
    cursor: pointer;
  }

  .popup {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 100px;
    left: 0px;
    top: 100px;
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
<script>const profileBtn = document.getElementById('profileBtn');
const profilePopup = document.getElementById('profilePopup');
const closeBtn = document.querySelector('#profilePopup .close'); // ✅ select the close button properly

// Open popup
profileBtn.onclick = () => {
  profilePopup.style.display = 'block';
};

// Close popup
closeBtn.onclick = () => {
  profilePopup.style.display = 'none';
};

// Close if clicked outside popup content
window.onclick = (e) => {
  if (e.target === profilePopup) {
    profilePopup.style.display = 'none';
  }
};

profileBtn.onclick = () => {
  fetch("get_profile.php")
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        alert("⚠️ " + data.error);
        return;
      }

      document.getElementById('profileBtn').innerText = data.name;

      // Show/hide fields depending on role
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
        // Hide student-specific fields
        document.querySelector("#admission_no").parentElement.style.display = "none";
        document.querySelector("#register_no").parentElement.style.display = "none";
        document.querySelector("#dob").parentElement.style.display = "none";
        document.querySelector("#course").parentElement.style.display = "none";
        document.querySelector("#class").parentElement.style.display = "none";

        // Show teacher info
        document.getElementById('name').innerText = data.name;
        document.getElementById('email').innerText = data.email;
        document.getElementById('role').innerText = data.role;

        // You can add a subject/department placeholder in HTML
        if (document.getElementById('subject'))
            document.getElementById('subject').innerText = data.subject ?? "N/A";
        if (document.getElementById('department'))
            document.getElementById('department').innerText = data.department_name ?? "N/A";
      }

      profilePopup.style.display = 'block';
    })
    .catch(err => console.error("Profile fetch error:", err));
};

// Optional: Fetch profile once on page load to set button text immediately
window.addEventListener("DOMContentLoaded", () => {
  fetch("get_profile.php")
    .then(res => res.json())
    .then(data => {
      document.getElementById('profileBtn').innerText = data.name;
    });
});

  closeBtn.onclick = () => {
    profilePopup.style.display = 'none';
  };

  window.onclick = function(e) {
    if (e.target === profilePopup) {
      profilePopup.style.display = 'none';
    }
  };
</script>


      <button onclick="toggleTheme()" id="themeToggle" title="Dark">🌙</button>
      <button onclick="toggleTheme()" id="themeToggle" title="Bright"></button>
    </div>
  </header>

  <!-- Main Content -->
  <main class="main-content">
    <div class="welcome">
      <h1>Welcome to ClassConnect</h1>
      <p>Your all-in-one student dashboard</p>
    </div>
    <div class="button-grid">
      <button onclick="window.location.href='classfeed.php';">📝 Class Feed</button>
      <button onclick="window.location.href='files_admin.php';">📁 Files</button>
      <button onclick="window.location.href='feedback.html';">💬 Feedback</button>
      <button onclick="window.location.href='timetable.html';"><h2>📅 Timetable</h2></button>
      <button onclick="window.location.href='https://www.mgu.ac.in/examinations/results/';"><h2>📈 Results</h2></button>
      <button onclick="window.location.href='admin_poll.html';">📊 Polls</button>
      <button onclick="window.location.href='notification.php';">🔔 Notifications</button>
      <button onclick="window.location.href='userslist.php';">Users</button>
      <button onclick="window.location.href='corner_admin.php';">TEACHERS CORNER</button>
      <button onclick="window.location.href='questionpapers.php';">Question papers</button>
    </div>
  </main>
  
  <!-- Sections -->
  <div id="profile" class="section">
    <h2>👤 Profile</h2>
    <p>Your personal info.</p>
  </div>
  <div id="feed" class="section">
    <button onclick="window.location.href='classfeed.php';">📝 Class Feed</button>
  </div>
  <div id="files" class="section">
    <button onclick="window.location.href='files_admin.php';"></button>
  </div>
  <div id="feedback" class="section">
    <button onclick="window.location.href='feedback.html';"></button>
  </div>
  <div id="timetable" class="section">
  <h2>📅 Timetable</h2>
  </div>
  <div id="results" class="section">
    <a href="https://www.mgu.ac.in/examinations/results/">Results</a>
  </div>
  <div id="polls" class="section">
    <button onclick="window.location.href='admin_poll.html';"></button>
  </div>
  <div id="notifications" class="section">
    <button onclick="window.location.href='notification.php';"></button>
  </div>
  <div id="Users" class="section">
    <button onclick="window.location.href='userslist.php';"></button>
  </div>
  <div id="corner" class="section">
   <button onclick="window.location.href='corner.html';"></button>
  </div>
  <div id="qns-papaer" class="section">
   <button onclick="window.location.href='questiopapers.php';"></button>
  </div>

  <script src="studentdash.js"></script>
</body>
</html>
