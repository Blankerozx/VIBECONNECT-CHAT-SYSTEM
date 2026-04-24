<?php
session_start();
include __DIR__ . '/../../include/db.php';

if (!isset($_GET['group_id'])) {
    echo json_encode(["error" => "Missing group_id"]);
    exit;
}

$group_id = intval($_GET['group_id']);

$query = "
    SELECT 
        u.id, 
        u.username,
        CASE 
            WHEN g.created_by = u.id THEN 'admin' 
            ELSE 'member' 
        END AS role
    FROM group_members gm
    INNER JOIN users u ON gm.user_id = u.id
    INNER JOIN chat_groups g ON gm.group_id = g.id
    WHERE gm.group_id = ?
    ORDER BY 
        CASE WHEN g.created_by = u.id THEN 0 ELSE 1 END,  -- admin first
        u.username ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

header('Content-Type: application/json');
echo json_encode($members);
?>
