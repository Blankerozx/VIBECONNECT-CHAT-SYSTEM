<?php
session_start();
include __DIR__ . '/../../include/db.php';

$id = $_POST['id'];

$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo "success";