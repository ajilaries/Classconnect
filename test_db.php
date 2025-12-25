<?php
include "config.php";

if ($conn->connect_error) {
    echo "Database connection failed: " . $conn->connect_error;
} else {
    echo "Database connection successful!";
    $conn->close();
}
?>
