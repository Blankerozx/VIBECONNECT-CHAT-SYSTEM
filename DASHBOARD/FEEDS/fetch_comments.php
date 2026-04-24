<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

if (!isset($_GET['post_id'])) {
    echo json_encode([]);
    exit;
}

$post_id = intval($_GET['post_id']);

$sql = "SELECT c.id, c.comment, c.created_at, u.username
        FROM post_comments c
        JOIN users u ON u.id = c.user_id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = [
        "id" => $row["id"],
        "username" => $row["username"],
        "comment" => $row["comment"],
        "created_at" => $row["created_at"]
    ];
}

echo json_encode($comments);
