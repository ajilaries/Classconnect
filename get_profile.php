<?php
include "config.php";
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role === 'student') {
    // Fetch student details
    $stmt = $conn->prepare("
        SELECT u.first_name, u.last_name, u.admission_no, u.register_no, 
               u.dob, u.course, u.email, u.role, 
               d.department_name
        FROM users u
        LEFT JOIN batches b ON u.batch_id = b.id
        LEFT JOIN departments d ON b.department_id = d.id
        WHERE u.id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $response = [
            "name" => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
            "admission_no" => $row['admission_no'],
            "register_no" => $row['register_no'],
            "dob" => $row['dob'],
            "course" => $row['course'],
            "email" => $row['email'],
            "role" => $row['role'],
            "department" => $row['department_name'] ?? "N/A"
        ];
        echo json_encode($response);
    } else {
        echo json_encode(["error" => "student not found"]);
    }

    $stmt->close();

} elseif ($role === 'teacher') {
    // Fetch teacher info
    $stmt = $conn->prepare("SELECT first_name, last_name, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $teacher = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$teacher) {
        echo json_encode(["error" => "teacher not found"]);
        exit;
    }

    // Fetch teacher allocations
    $stmt2 = $conn->prepare("
        SELECT ta.subject, d.department_name, b.class_code AS batch
        FROM teacher_allocations ta
        LEFT JOIN departments d ON ta.department_id = d.id
        LEFT JOIN batches b ON ta.batch_id = b.id
        WHERE ta.teacher_id = ?
    ");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $allocations = [];
    while($row = $result2->fetch_assoc()) {
        $allocations[] = [
            "subject" => $row['subject'],
            "department" => $row['department_name'],
            "batch" => $row['batch']
        ];
    }
    $stmt2->close();

    $current_batch = $_SESSION['class_code'] ?? ($allocations[0]['batch'] ?? null);

    $response = [
        "name" => trim($teacher['first_name'] . ' ' . $teacher['last_name']),
        "email" => $teacher['email'],
        "role" => $teacher['role'],
        "allocations" => $allocations,
        "current_batch" => $current_batch
    ];

    echo json_encode($response);
}

$conn->close();
?>
