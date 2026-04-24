<?php
session_start();
include __DIR__ . '/../../include/db.php';

// Ensure the user is logged in
// if (!isset($_SESSION['id'])) {
//     echo json_encode([]);
//     exit;
// }

$user_id = $_SESSION['id'];

$sql = "SELECT g.id AS group_id, g.group_name, g.created_by, g.created_at
        FROM chat_groups g
        JOIN group_members gm ON g.id = gm.group_id
        WHERE gm.user_id = ?
        ORDER BY g.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[] = $row;
}

echo json_encode($groups);
?>
