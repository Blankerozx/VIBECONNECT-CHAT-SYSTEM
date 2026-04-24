<?php
session_start();
include __DIR__ . '/../../include/db.php';

$user_id = $_SESSION['id'];

$query = "
SELECT id, username 
FROM users 
WHERE id != ?
AND id NOT IN (
    SELECT friend_id FROM friendships WHERE user_id = ?
)
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $target_id = $row['id'];

    // Check if request already sent
    $check = $conn->prepare("
        SELECT * FROM friend_requests 
        WHERE sender_id = ? AND receiver_id = ?
        AND status = 'pending'
    ");
    $check->bind_param("ii", $user_id, $target_id);
    $check->execute();
    $sent = $check->get_result();

    echo "<div class='suggestion-card'>";
    echo "<span>{$row['username']}</span>";

    if ($sent->num_rows > 0) {
        echo "<button disabled class='sent-btn'>Request Sent</button>";
    } else {
        echo "<button onclick='addFriend({$target_id}, this)' class='add-btn'>Add</button>";
    }

    echo "</div>";
}
?>