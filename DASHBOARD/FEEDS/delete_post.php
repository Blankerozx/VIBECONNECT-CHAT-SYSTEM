<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || !isset($_POST['post_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$user_id = $_SESSION['id'];
$post_id = intval($_POST['post_id']);

// Verify ownership
$check = $conn->prepare("SELECT user_id, image FROM posts WHERE id = ?");
$check->bind_param("i", $post_id);
$check->execute();
$res = $check->get_result();
$post = $res->fetch_assoc();

if (!$post) {
    echo json_encode(["success" => false, "message" => "Post not found"]);
    exit;
}

if ($post['user_id'] !== $user_id) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

// Delete image if exists
if ($post['image'] && file_exists($post['image'])) {
    unlink($post['image']);
}

// Delete post
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
echo json_encode(["success" => $stmt->execute()]);
?>
