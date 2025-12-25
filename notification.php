<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    die("⛔ Unauthorized!");
}

$user_id   = $_SESSION['user_id'];
$role      = $_SESSION['role'];
$college_id = $_SESSION['college_id'];
$batch_id   = $_SESSION['batch_id'];

// Fetch notifications
if ($role === 'student') {
    // Students see notifications targeted to them OR general student notifications for their batch & college
    $query = "
        SELECT * FROM notifications
        WHERE (target_user_id = ? OR (target_user_id IS NULL AND target_role='student'))
          AND college_id = ? AND batch_id = ?
        ORDER BY created_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $college_id, $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // mark as read for this user
    $mark = $conn->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE target_user_id = ? AND college_id = ? AND batch_id = ?
    ");
    $mark->bind_param("iii", $user_id, $college_id, $batch_id);
    $mark->execute();
    $mark->close();

} elseif ($role === 'teacher') {
    // Teachers see notifications targeted to teachers or admin messages, ignore their own classfeed uploads
    $query = "
        SELECT * FROM notifications
        WHERE (target_role='teacher' OR target_user_id = ? OR creator_id != ?)
          AND college_id = ?
        ORDER BY created_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $user_id, $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📢 Notifications</title>
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 30px; }
h2 { margin-bottom: 20px; }
.notification {
    background: #fff; padding: 15px; margin-bottom: 12px;
    border-left: 5px solid #007bff; border-radius: 8px;
    box-shadow: 0 0 5px rgba(0,0,0,0.03);
    transition: background 0.2s ease;
}
.notification:hover { background: #f8f9fa; }
.notification.unread { border-left-color: #ff4757; background: #fff7f7; }
.notification a { text-decoration: none; color: inherit; display: block; }
.file-link { color: #28a745; font-weight: bold; }
.time { color: #666; font-size: 12px; }
.actions { margin-top: 10px; }
.btn { display: inline-block; padding: 6px 12px; margin-right: 8px; border-radius: 4px; text-decoration: none; font-size: 14px; }
.btn-create { background: #28a745; color: #fff; }
.btn-edit { background: #ffc107; color: #000; }
.btn-delete { background: #dc3545; color: #fff; }
</style>
</head>
<body>

<h2>📋 Notifications</h2>

<?php if ($role === 'teacher'): ?>
    <div style="margin-bottom: 15px;">
        <a href="notificationback.php" class="btn btn-create">➕ Create Notification</a>
    </div>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
    <?php
        $isUnread = empty($row['is_read']) || $row['is_read'] == 0;
        $postId   = (int)($row['post_id'] ?? 0);
        $msg      = htmlspecialchars($row['message'] ?? '');
        $created  = htmlspecialchars($row['created_at'] ?? '');
        $file     = htmlspecialchars($row['file_path'] ?? '');
    ?>
    <div class="notification <?= $isUnread ? 'unread' : '' ?>">
        <a href="<?= $postId ? "classfeed.php?post_id=$postId" : "#" ?>">
            <p><strong><?= $msg ?></strong></p>
            <p class="time">🕒 <?= $created ?></p>
            <?php if (!empty($file)): ?>
                <p><span class="file-link">📎 Attached</span></p>
            <?php endif; ?>
        </a>

    
    </div>
<?php endwhile; ?>

</body>
</html>
