<?php
session_start();
include __DIR__ . '/../../include/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$request_id = $data['request_id'];
$action = $data['action'];
$user_id = $_SESSION['id'];

if ($action == "accepted") {
    // Get sender
    $stmt = $conn->prepare("SELECT sender_id FROM friend_requests WHERE request_id=?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $sender_id = $res['sender_id'];

    // Update request
    $stmt = $conn->prepare("UPDATE friend_requests SET status='accepted' WHERE request_id=?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();

    // Insert into friendships (both ways)
    $stmt = $conn->prepare("INSERT INTO friendships (user_id, friend_id) VALUES (?, ?), (?, ?)");
    $stmt->bind_param("iiii", $user_id, $sender_id, $sender_id, $user_id);
    $stmt->execute();

    echo json_encode(["message" => "Friend request accepted!"]);
} else {
    $stmt = $conn->prepare("DELETE FROM friend_requests WHERE request_id=?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    echo json_encode(["message" => "Friend request declined!"]);
}

createNotification($conn, $sender_id, "friend_accept", "Your friend request was accepted");
