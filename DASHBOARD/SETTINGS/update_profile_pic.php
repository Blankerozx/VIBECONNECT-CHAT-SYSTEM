<?php
session_start();
include __DIR__ . '/../../include/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
  echo json_encode(["success" => false, "message" => "Unauthorized"]);
  exit;
}

$user_id = $_SESSION['id'];
if (!isset($_FILES['profile_pic'])) {
  echo json_encode(["success" => false, "message" => "No file uploaded"]);
  exit;
}

$file = $_FILES['profile_pic'];
$targetDir = "uploads/";
$ext = pathinfo($file["name"], PATHINFO_EXTENSION);
$newName = "user_" . $user_id . "." . $ext;
$targetFile = $targetDir . $newName;

if (move_uploaded_file($file["tmp_name"], $targetFile)) {
  $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
  $stmt->bind_param("si", $targetFile, $user_id);
  $stmt->execute();

  $_SESSION['profile_pic'] = $targetFile;
  echo json_encode(["success" => true, "message" => "Profile picture updated.", "newPath" => $targetFile]);
} else {
  echo json_encode(["success" => false, "message" => "Upload failed"]);
}
?>
