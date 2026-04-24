<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($_SESSION['id']) || empty($data['current']) || empty($data['newPass'])) {
  echo json_encode(["success" => false, "message" => "Invalid input"]);
  exit;
}

$user_id = $_SESSION['id'];
$current = $data['current'];
$newPass = password_hash($data['newPass'], PASSWORD_DEFAULT);

$check = $conn->prepare("SELECT password FROM users WHERE id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$res = $check->get_result()->fetch_assoc();

if (!$res || !password_verify($current, $res['password'])) {
  echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
  exit;
}

$update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$update->bind_param("si", $newPass, $user_id);

if ($update->execute()) {
  echo json_encode(["success" => true, "message" => "Password updated successfully."]);
} else {
  echo json_encode(["success" => false, "message" => "Password update failed."]);
}
?>
