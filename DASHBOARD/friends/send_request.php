<?php
session_start();
include "../../include/db.php"; // adjust path as needed

include __DIR__ . '/../../include/notify.php';

// Simulate login (for now)
if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = 1; // replace later with real login session
}

$sender_id = $_SESSION['id'];

// Decode raw JSON body
$input = json_decode(file_get_contents("php://input"), true);

// Debugging helper (optional, remove later)
// file_put_contents("debug.log", print_r($input, true));

$receiver_id = isset($input['receiver_id']) ? intval($input['receiver_id']) : 0;

if ($receiver_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid receiver ID"]);
    exit;
}

// Insert request
$stmt = $conn->prepare("INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("ii", $sender_id, $receiver_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Friend request sent"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}


// after sending request
createNotification($conn, $receiver_id, "friend_request", "You have a new friend request");
