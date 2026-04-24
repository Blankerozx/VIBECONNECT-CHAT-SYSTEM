<?php
session_start();
include __DIR__ . '/../../include/db.php';
$user_id = $_SESSION['id'];
$query = $_GET['query'];

$stmt = $conn->prepare("
SELECT id, username 
FROM users 
WHERE username LIKE CONCAT('%', ?, '%')
AND id != ?
");
$stmt->bind_param("si", $query, $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $target_id = $row['id'];

    // 1️⃣ Check if already friends
    $checkFriend = $conn->prepare("
        SELECT * FROM friendships 
        WHERE user_id = ? AND friend_id = ?
    ");
    $checkFriend->bind_param("ii", $user_id, $target_id);
    $checkFriend->execute();
    $friendResult = $checkFriend->get_result();

    // 2️⃣ Check if request already sent
    $checkSent = $conn->prepare("
        SELECT * FROM friend_requests 
        WHERE sender_id = ? AND receiver_id = ? 
        AND status = 'pending'
    ");
    $checkSent->bind_param("ii", $user_id, $target_id);
    $checkSent->execute();
    $sentResult = $checkSent->get_result();

    // 3️⃣ Check if request received
    $checkReceived = $conn->prepare("
        SELECT * FROM friend_requests 
        WHERE sender_id = ? AND receiver_id = ?
        AND status = 'pending'
    ");
    $checkReceived->bind_param("ii", $target_id, $user_id);
    $checkReceived->execute();
    $receivedResult = $checkReceived->get_result();

    echo "<div class='search-item'>";
    echo "<span>{$row['username']}</span>";

    if ($friendResult->num_rows > 0) {
        echo "<button disabled class='friend-btn'>Friends</button>";

    } elseif ($sentResult->num_rows > 0) {
        echo "<button disabled class='sent-btn'>Request Sent</button>";

    } elseif ($receivedResult->num_rows > 0) {
        echo "<button onclick='acceptRequestFromSearch({$target_id})' class='accept-btn'>Accept</button>";

    } else {
        echo "<button onclick='addFriend({$target_id}, this)' class='add-btn'>Add</button>";
    }

    echo "</div>";
}
?>