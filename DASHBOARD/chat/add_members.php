<?php
session_start();
header('Content-Type: application/json');
include __DIR__ . '/../../include/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['group_id'], $data['friend_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$group_id = intval($data['group_id']);
$friend_id = intval($data['friend_id']);

// Check if current user is admin
$check = $conn->prepare("SELECT created_by FROM chat_groups WHERE id=?");
$check->bind_param("i", $group_id);
$check->execute();
$result = $check->get_result()->fetch_assoc();

if (!$result || $result['created_by'] != $user_id) {
    echo json_encode(["success" => false, "message" => "Only group admin can add members"]);
    exit;
}

// Check if already in group
$chk = $conn->prepare("SELECT * FROM group_members WHERE group_id=? AND user_id=?");
$chk->bind_param("ii", $group_id, $friend_id);
$chk->execute();

if ($chk->get_result()->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "User already in group"]);
    exit;
}

// Add member
$stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $group_id, $friend_id);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Member added successfully"]);
?>
