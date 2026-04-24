<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($_SESSION['id']) || empty($data['username']) || empty($data['email'])) {
  echo json_encode(["success" => false, "message" => "Invalid input"]);
  exit;
}

$user_id = $_SESSION['id'];
$username = trim($data['username']);
$email = trim($data['email']);

$stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssi", $username, $email, $user_id);

if ($stmt->execute()) {
  $_SESSION['username'] = $username;
  $_SESSION['email'] = $email;
  echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
} else {
  echo json_encode(["success" => false, "message" => "Update failed."]);
}
?>
