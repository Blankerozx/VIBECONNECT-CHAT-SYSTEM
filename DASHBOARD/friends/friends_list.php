<?php
session_start();
include __DIR__ . '/../../include/db.php';
$user_id = $_SESSION['id'];

$sql = "SELECT u.id, u.username, u.email
        FROM friendships f
        JOIN users u ON f.friend_id = u.id
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = $row;
}
echo json_encode($friends);
