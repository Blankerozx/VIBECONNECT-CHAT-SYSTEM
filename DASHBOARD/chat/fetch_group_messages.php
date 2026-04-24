<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

// Check if user is logged in
// if (!isset($_SESSION['id'])) {
//     echo json_encode(["error" => "Not logged in"]);
//     exit;
// }

$user_id = $_SESSION['id'];

// Validate group ID
if (!isset($_GET['group_id']) || !is_numeric($_GET['group_id'])) {
    echo json_encode(["error" => "Invalid group ID"]);
    exit;
}

$group_id = intval($_GET['group_id']);

// Fetch all messages for the group
$sql = "SELECT gm.id AS message_id, gm.message, gm.sent_at, u.username, gm.user_id
        FROM group_messages gm
        JOIN users u ON gm.user_id = u.id
        WHERE gm.group_id = ?
        ORDER BY gm.sent_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // Mark if the message belongs to the logged-in user
    $row['is_self'] = ($row['user_id'] == $user_id);
    $messages[] = $row;
}

echo json_encode($messages);

$stmt->close();
$conn->close();
?>
