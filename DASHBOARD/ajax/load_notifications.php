<?php
session_start();
include __DIR__ . '/../../include/db.php';

$user_id = $_SESSION['id'];

$stmt = $conn->prepare("
    SELECT id, type, message, created_at, is_read
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 20
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id" => $row['id'],
        "type" => $row['type'],
        "text" => $row['message'],
        "time" => timeAgo($row['created_at']),
        "is_read" => $row['is_read']
    ];
}

echo json_encode($data);

// helper
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    if ($time < 60) return "Just now";
    if ($time < 3600) return floor($time/60)." min ago";
    if ($time < 86400) return floor($time/3600)." hrs ago";
    return floor($time/86400)." days ago";
}