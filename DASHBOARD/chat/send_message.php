<?php
session_start();
include __DIR__ . '/../../include/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$sender_id = $_SESSION['id'];
$receiver_id = $data['receiver_id'];
$message = trim($data['message']);

if ($message == '') {
    echo json_encode(["status" => "error", "msg" => "Empty message"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO private_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);
$stmt->execute();

echo json_encode(["status" => "success"]);

createNotification($conn, $receiver_id, "message", "You received a new message");
