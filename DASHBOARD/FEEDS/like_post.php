<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || !isset($_POST['post_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$user_id = intval($_SESSION['id']);
$post_id = intval($_POST['post_id']);

// Check if already liked
$check = $conn->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
$check->bind_param("ii", $post_id, $user_id);
$check->execute();
$liked = $check->get_result()->num_rows > 0;

if ($liked) {
    $stmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $liked = false;
} else {
    $stmt = $conn->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $liked = true;
}

// Get updated like count
$count = $conn->prepare("SELECT COUNT(*) AS total FROM post_likes WHERE post_id = ?");
$count->bind_param("i", $post_id);
$count->execute();
$total = $count->get_result()->fetch_assoc()['total'];

echo json_encode(["success" => true, "liked" => $liked, "like_count" => $total]);
