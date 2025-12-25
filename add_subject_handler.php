<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = trim($_POST["subject_name"]);

    // Check if subject already exists
    $check = mysqli_query($conn, "SELECT * FROM subjects WHERE name='$subject_name'");
    if (mysqli_num_rows($check) > 0) {
        echo "Subject already exists!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO subjects (name) VALUES ('$subject_name')");
        if ($insert) {
            echo "Subject added successfully!";
            header("Location: questionpapers.php"); // Redirect after successful insert
            exit;
        } else {
            echo "Error adding subject: " . mysqli_error($conn);
        }
    }
}
?>


