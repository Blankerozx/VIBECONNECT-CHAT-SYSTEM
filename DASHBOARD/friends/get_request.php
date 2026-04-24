<?php
session_start();
include __DIR__ . '/../../include/db.php';

// Ensure session is active
if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['id'];

/*
 Fetch all friend requests where the logged-in user is the receiver (pending only)
 and join with users table to show sender info.
*/
$sql = "SELECT 
            fr.request_id, 
            u.id AS sender_id, 
            u.username, 
            u.email, 
            fr.status, 
            fr.created_at
        FROM friend_requests fr
        INNER JOIN users u 
            ON fr.sender_id = u.id
        WHERE fr.receiver_id = ? 
          AND fr.status = 'pending'
        ORDER BY fr.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);
