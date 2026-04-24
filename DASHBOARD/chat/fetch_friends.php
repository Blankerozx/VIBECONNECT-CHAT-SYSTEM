<?php
session_start();
include __DIR__ . '/../../include/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['id'];

$sql = "SELECT DISTINCT u.id, u.username 
        FROM friendships f
        JOIN users u 
          ON (u.id = CASE 
                        WHEN f.user_id = ? THEN f.friend_id
                        ELSE f.user_id
                     END)
        WHERE (f.user_id = ? OR f.friend_id = ?)";


$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = $row;
}

if (empty($friends)) {
    echo json_encode(["debug" => "No friends found for user_id = $user_id"]);
    exit;
}

echo json_encode($friends);
