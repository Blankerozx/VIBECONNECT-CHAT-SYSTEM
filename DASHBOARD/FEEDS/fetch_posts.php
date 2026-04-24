<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

// Make sure the user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['id'];

// ✅ Fetch posts from user and their friends
$sql = "
SELECT 
    p.id, 
    p.content, 
    p.image, 
    p.created_at, 
    u.username, 
    u.profile_pic,
    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id) AS like_count,
    (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) AS comment_count,
    (SELECT COUNT(*) FROM post_likes WHERE post_id = p.id AND user_id = ?) AS liked_by_user,
    CASE WHEN p.user_id = ? THEN 1 ELSE 0 END AS is_owner
FROM posts p
JOIN users u ON u.id = p.user_id
WHERE 
    p.user_id = ?
    OR p.user_id IN (
        SELECT friend_id FROM friendships WHERE user_id = ?
        UNION
        SELECT user_id FROM friendships WHERE friend_id = ?
    )
ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = [
        'id' => $row['id'],
        'username' => $row['username'],
        'profile_pic' => $row['profile_pic'] ?: 'images/default.png',
        'content' => $row['content'],
        'image' => $row['image'],
        'created_at' => $row['created_at'],
        'like_count' => (int)$row['like_count'],
        'comment_count' => (int)$row['comment_count'],
        'liked' => $row['liked_by_user'] > 0,
        'is_owner' => $row['is_owner']
    ];
}

echo json_encode($posts);
?>
