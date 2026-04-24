<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || !isset($_POST['group_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$user_id = intval($_SESSION['id']);
$group_id = intval($_POST['group_id']);

// ✅ Get username (for system message)
$user_query = $conn->prepare("SELECT username FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();
$username = $user ? $user['username'] : 'A user';
$user_query->close();

// ✅ Delete from group_members
$stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
$stmt->bind_param("ii", $group_id, $user_id);

if ($stmt->execute()) {
    // ✅ Post system message: "User left the group"
    $system_message = "$username left the group.";

    $msg_stmt = $conn->prepare("INSERT INTO group_messages (group_id, user_id, message) VALUES (?, ?, ?)");
    $msg_stmt->bind_param("iis", $group_id, $user_id, $system_message);
    $msg_stmt->execute();
    $msg_stmt->close();

    echo json_encode(["success" => true, "message" => "You left the group"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to leave group"]);
}

$stmt->close();
$conn->close();
?>
