<?php
include "config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "⛔ Access denied!";
    exit;
}

if (!isset($_GET['subject_id'])) {
    echo "⚠️ Subject ID missing.";
    exit;
}

$subject_id = intval($_GET['subject_id']);

// Optional: delete any related question papers first
mysqli_query($conn, "DELETE FROM question_papers WHERE subject_id = $subject_id");

// Now delete the subject
$delete = mysqli_query($conn, "DELETE FROM subjects WHERE id = $subject_id");

if ($delete) {
    echo "<script>
            alert('✅ Subject deleted successfully!');
            window.location.href = 'corner_admin.php'; // redirect back to admin panel
          </script>";
} else {
    echo "❌ Error deleting subject: " . mysqli_error($conn);
}
?>
