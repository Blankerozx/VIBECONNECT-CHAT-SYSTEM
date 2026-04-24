<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['id'];

// ✅ Updated to match your actual table: `friendships`
$sql = "SELECT u.id, u.username 
        FROM friendships f
        JOIN users u ON u.id = f.friend_id 
        WHERE f.user_id = ?
        UNION
        SELECT u.id, u.username
        FROM friendships f
        JOIN users u ON u.id = f.user_id
        WHERE f.friend_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = $row;
}

echo json_encode($friends);
?>
