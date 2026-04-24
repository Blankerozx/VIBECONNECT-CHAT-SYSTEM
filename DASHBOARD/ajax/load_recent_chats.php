<?php
session_start();
include __DIR__ . '/../../include/db.php';

$user_id = $_SESSION['id'];

$query = "
SELECT pm.message, pm.sent_at, u.username
FROM private_messages pm
JOIN users u ON 
    (u.id = pm.sender_id OR u.id = pm.receiver_id)
WHERE (pm.sender_id = ? OR pm.receiver_id = ?)
AND u.id != ?
ORDER BY pm.sent_at DESC
LIMIT 5
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div class='chat-preview'>
            <strong>{$row['username']}</strong>
            <p>{$row['message']}</p>
          </div>";
}
?>