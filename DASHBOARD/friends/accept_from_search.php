<?php
session_start();
include __DIR__ . '/../../include/db.php';

$user_id = $_SESSION['user_id'];
$sender_id = $_POST['sender_id'];

// Update request
$stmt = $conn->prepare("
UPDATE friend_requests 
SET status='accepted' 
WHERE sender_id=? AND receiver_id=? 
AND status='pending'
");
$stmt->bind_param("ii", $sender_id, $user_id);
$stmt->execute();

// Insert into friendships both ways
$stmt = $conn->prepare("
INSERT INTO friendships (user_id, friend_id) 
VALUES (?, ?), (?, ?)
");
$stmt->bind_param("iiii", $user_id, $sender_id, $sender_id, $user_id);
$stmt->execute();

echo "success";
?>