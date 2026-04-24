<?php
session_start();
include __DIR__ . '/../../include/db.php';

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['id'];
$data = json_decode(file_get_contents("php://input"), true);
$group_name = trim($data['group_name']);

if ($group_name == '') {
    echo json_encode(["success" => false, "message" => "Group name required"]);
    exit;
}

// ✅ Add created_at timestamp properly
$created_at = date('Y-m-d H:i:s');

$sql = "INSERT INTO chat_groups (group_name, created_by, created_at) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $group_name, $user_id, $created_at);

if ($stmt->execute()) {
    $group_id = $stmt->insert_id;

    // Add the creator as group member (admin)
    $conn->query("INSERT INTO group_members (group_id, user_id, is_admin) VALUES ($group_id, $user_id, 1)");

    echo json_encode(["success" => true, "group_id" => $group_id]);
} else {
    echo json_encode(["success" => false, "message" => "Error creating group: " . $stmt->error]);
}
?>
