<?php
session_start();
include "config.php";

// Check session for logged-in student
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize message variable
$message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data safely
    $feedback_text = trim($_POST['feedback_text'] ?? '');
    $category      = trim($_POST['category'] ?? '');
    $rating        = intval($_POST['rating'] ?? 0);
    $teacher_id    = intval($_POST['teacher_id'] ?? 0);
    $subject       = trim($_POST['subject'] ?? '');
    $is_anonym     = isset($_POST['anonymous']) ? 1 : 0;

    // Determine user_id (NULL if anonymous)
    $user_id = $is_anonym ? null : intval($_SESSION['user_id']);

    if (!empty($feedback_text) && !empty($category) && $rating > 0 && $teacher_id > 0 && !empty($subject)) {

        // Prepared statement with teacher + subject
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, teacher_id, subject, feedback_text, category, rating, is_anonymous) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssii", $user_id, $teacher_id, $subject, $feedback_text, $category, $rating, $is_anonym);

        if ($stmt->execute()) {
            $message = "✅ Feedback submitted successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }

        $stmt->close();

    } else {
        $message = "⚠️ Please fill in all required fields.";
    }
}

$conn->close();
?>

<!-- Display success/error message above your form -->
<?php if (!empty($message)): ?>
    <p style="color: green; font-weight: bold;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
