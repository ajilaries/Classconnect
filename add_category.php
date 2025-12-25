<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category_name']);

    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO file_categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            echo "✅ Category added successfully!";
        } else {
            echo "❌ Error: " . $conn->error;
        }

        $stmt->close();
    }
    $conn->close();
}
?>
