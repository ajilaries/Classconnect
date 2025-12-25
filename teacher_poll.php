<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    die("Access Denied!");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Poll Dashboard - ClassConnect</title>
<style>
/* Theme color */
:root {
    --theme-color: #276cdb;
    --theme-hover: #276cdb;
    --bg-color: #f5f5f5;
    --text-color: #fff;
}

/* Reset default margins */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    height: 100vh;
    background-color: var(--bg-color);
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

/* Heading */
h1 {
    color: var(--theme-color);
    margin-bottom: 40px;
    text-align: center;
}

/* Button bar */
.button-bar {
    display: flex;
    gap: 20px;
    justify-content: center;
}

.button-bar button {
    padding: 15px 30px;
    border: none;
    border-radius: 8px;
    background-color: var(--theme-color);
    color: var(--text-color);
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

.button-bar button:hover {
    background-color: var(--theme-hover);
    transform: translateY(-2px);
}
</style>
</head>
<body>

<h1>🗳️ Teacher Poll Dashboard</h1>

<div class="button-bar">
    <button onclick="window.location.href='create_poll.php'">Create Poll</button>
    <button onclick="window.location.href='poll_list.php'">View Polls</button>
</div>

</body>
</html>
