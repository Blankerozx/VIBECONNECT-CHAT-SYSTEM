<?php
session_start();
include __DIR__ . '/../../include/db.php';

$user_id = $_SESSION['id'];

$query = "
SELECT posts.content, posts.image, posts.created_at, users.username
FROM posts
JOIN friendships ON friendships.friend_id = posts.id
JOIN users ON users.id = posts.id
WHERE friendships.user_id = ?
ORDER BY posts.created_at DESC
LIMIT 5
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    echo "<div class='post'>";
    echo "<h4>{$row['username']}</h4>";
    echo "<p>{$row['content']}</p>";

    if (!empty($row['image'])) {
        echo "<img src='../FEEDS/uploads/{$row['image']}' class='post-img'>";
    }

    echo "<small>{$row['created_at']}</small>";
    echo "</div>";
}
?>