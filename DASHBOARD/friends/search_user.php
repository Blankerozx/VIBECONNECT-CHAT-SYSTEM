<?php
session_start();
include __DIR__ . '/../../include/db.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "You must be logged in."]);
    exit;
}

$currentUserId = $_SESSION['id'];

$input = json_decode(file_get_contents("php://input"), true);
$search = trim($input['query'] ?? "");

if ($search === "") {
    echo json_encode(["status" => "error", "message" => "Please enter a username or email"]);
    exit;
}

/*
   We JOIN friend_requests to detect if:
   - There's a pending or accepted request between the two users
   - Who initiated it (sender/receiver)
*/
$sql = "SELECT 
            u.id, 
            u.username, 
            u.email,
            fr.status,
            CASE 
                WHEN fr.sender_id = ? THEN 'outgoing'
                WHEN fr.receiver_id = ? THEN 'incoming'
                ELSE 'none'
            END AS direction
        FROM users u
        LEFT JOIN friend_requests fr 
            ON (
                (fr.sender_id = u.id AND fr.receiver_id = ?) 
                OR 
                (fr.receiver_id = u.id AND fr.sender_id = ?)
            )
        WHERE (u.username LIKE ? OR u.email LIKE ?)
          AND u.id != ?";

$stmt = $conn->prepare($sql);
$param = "%" . $search . "%";
$stmt->bind_param(
    "iiiissi",
    $currentUserId,  // For CASE direction (sender)
    $currentUserId,  // For CASE direction (receiver)
    $currentUserId,  // For JOIN 1
    $currentUserId,  // For JOIN 2
    $param,          // Search username
    $param,          // Search email
    $currentUserId   // Exclude current user
);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $status = $row['status'] ?? 'none';
    $direction = $row['direction'] ?? 'none';

    // Determine display text
    if ($status === 'pending' && $direction === 'outgoing') {
        $statusText = 'Request Sent';
    } elseif ($status === 'pending' && $direction === 'incoming') {
        $statusText = 'Request Received';
    } elseif ($status === 'accepted') {
        $statusText = 'Friends';
    } else {
        $statusText = 'Add Friend';
    }

    $users[] = [
        "id" => $row['id'],
        "username" => $row['username'],
        "email" => $row['email'],
        "status" => $statusText
    ];
}

if (empty($users)) {
    echo json_encode(["status" => "error", "message" => "No users found"]);
} else {
    echo json_encode(["status" => "success", "users" => $users]);
}
