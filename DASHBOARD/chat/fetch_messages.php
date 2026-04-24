<?php
session_start();
include __DIR__ . '/../../include/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['id'];
$receiver_id = $_GET['receiver_id'];

$sql = "SELECT sender_id, message, sent_at 
        FROM private_messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY sent_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        "message" => $row['message'],
        "is_sender" => $row['sender_id'] == $user_id
    ];
}

echo json_encode($messages);
