<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || !isset($_POST['group_id']) || !isset($_POST['member_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$admin_id = intval($_SESSION['id']);
$group_id = intval($_POST['group_id']);
$member_id = intval($_POST['member_id']);

// verify current user is admin
$check = $conn->prepare("SELECT created_by FROM chat_groups WHERE id = ?");
$check->bind_param("i", $group_id);
$check->execute();
$result = $check->get_result();
$row = $result->fetch_assoc();

if (!$row || intval($row['created_by']) !== $admin_id) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

// perform removal
$stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->bind_param("ii", $group_id, $member_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Member removed successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to remove member"]);
}
?>
