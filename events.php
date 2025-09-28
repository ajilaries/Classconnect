<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// ✅ Session values
$user_id       = $_SESSION['user_id'];
$role          = $_SESSION['role'] ?? null;
$department_id = $_SESSION['department_id'] ?? null;
$batch_id      = $_SESSION['batch_id'] ?? null;

// ✅ Build SQL
if ($role === 'admin') {
    // Admin sees all events
    $sql = "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) AS creator_name
            FROM events e
            JOIN users u ON u.id = e.created_by
            ORDER BY e.event_date DESC, e.created_at DESC";

    $stmt = $conn->prepare($sql);
} else {
    // Teachers & Students: see events relevant to them
    $sql = "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) AS creator_name
        FROM events e
        JOIN users u ON u.id = e.created_by
        WHERE 
          (e.visibility = 'department' AND e.department_id = ?)
          OR (e.visibility = 'batch' AND e.batch_id = ?)
        ORDER BY e.event_date DESC, e.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $department_id, $batch_id);
}

// ✅ Execute and fetch results
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events & Notices</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f8fc; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        h1 { text-align: center; margin-bottom: 15px; }
        .event-card { background: white; border-radius: 12px; padding: 15px 20px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .event-title { font-size: 18px; font-weight: bold; margin: 0 0 5px 0; }
        .event-meta { font-size: 14px; color: gray; margin-bottom: 10px; }
        .event-desc { margin-bottom: 10px; }
        .attachment-link { display: inline-block; margin-top: 5px; color: #007BFF; text-decoration: none; }
        .attachment-link:hover { text-decoration: underline; }
        .create-btn { display: inline-block; background: #28a745; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; margin-bottom: 20px; }
        .create-btn:hover { background: #218838; }
    </style>
</head>
<body>
<div class="container">
    <h1>📢 Events & Notices</h1>

    <?php if (in_array($role, ['admin', 'teacher'])): ?>
        <a class="create-btn" href="create_events.php">+ Create New Event</a>
    <?php endif; ?>

    <?php if (empty($events)): ?>
        <p style="text-align:center; color: gray;">No events or notices posted yet.</p>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <div class="event-title"><?= htmlspecialchars($event['title']); ?></div>
                <div class="event-meta">
                    Posted by <?= htmlspecialchars($event['creator_name']); ?> 
                    on <?= date("d M Y, h:i A", strtotime($event['created_at'])); ?>
                    <?php if ($event['visibility'] === 'department'): ?>
                        <span style="color:#007BFF;">(Department Event)</span>
                    <?php elseif ($event['visibility'] === 'batch'): ?>
                        <span style="color:#28a745;">(Batch Event)</span>
                    <?php endif; ?>
                </div>
                <div class="event-desc"><?= nl2br(htmlspecialchars($event['description'])); ?></div>
                <?php if (!empty($event['event_date'])): ?>
                    <p><strong>📅 Date:</strong> <?= htmlspecialchars($event['event_date']); ?></p>
                <?php endif; ?>
                <?php if (!empty($event['event_time'])): ?>
                    <p><strong>⏰ Time:</strong> <?= htmlspecialchars($event['event_time']); ?></p>
                <?php endif; ?>
                <?php if (!empty($event['attachment'])): ?>
                    <a class="attachment-link" href="<?= htmlspecialchars($event['attachment']); ?>" target="_blank">📎 View Attachment</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
