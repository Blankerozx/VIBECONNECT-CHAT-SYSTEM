<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || !isset($_POST['post_id']) || empty($_POST['comment'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$user_id = intval($_SESSION['id']);
$post_id = intval($_POST['post_id']);
$comment = trim($_POST['comment']);

$stmt = $conn->prepare("INSERT INTO post_comments (post_id, user_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $post_id, $user_id, $comment);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add comment"]);
}
