<?php
session_start();
include __DIR__ . '/../../include/db.php';

if (!isset($_SESSION['id'])) {
    exit("Not logged in");
}

$sender_id = $_SESSION['id'];
$receiver_id = $_POST['receiver_id'];

// Prevent adding yourself
if ($sender_id == $receiver_id) {
    exit("Cannot add yourself");
}

// Check if already sent
$check = $conn->prepare("
SELECT * FROM friend_requests 
WHERE sender_id = ? AND receiver_id = ? AND status = 'pending'
");
$check->bind_param("ii", $sender_id, $receiver_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    exit("Already sent");
}

// Insert request
$stmt = $conn->prepare("
INSERT INTO friend_requests (sender_id, receiver_id)
VALUES (?, ?)
");
$stmt->bind_param("ii", $sender_id, $receiver_id);
$stmt->execute();

echo "success";
?>