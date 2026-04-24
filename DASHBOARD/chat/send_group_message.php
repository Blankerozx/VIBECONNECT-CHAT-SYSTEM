<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['id'];

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Debug log (optional — helpful for troubleshooting)
file_put_contents("group_debug.log", print_r($data, true));

// Validate input
if (empty($data['group_id']) || empty($data['message'])) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid request",
        "received" => $data
    ]);
    exit;
}

$group_id = intval($data['group_id']);
$message = trim($data['message']);

if ($message === '') {
    echo json_encode(["success" => false, "error" => "Empty message"]);
    exit;
}

// ✅ Insert into database — match your SQL structure
$sql = "INSERT INTO group_messages (group_id, user_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "SQL Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("iis", $group_id, $user_id, $message);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Insert failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();


createNotification($conn, $receiver_id, "message", "You received a new message");

