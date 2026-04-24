<?php
session_start();
require_once "db.php";

$username = trim($_POST['username']);
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    if (password_verify($password, $row['password'])) {
        // ✅ Keep this consistent across your app
        $_SESSION['id'] = $row['id']; 
        $_SESSION['username'] = $row['username'];
        // $_SESSION['role'] = $row['role'] ?? 'user';
        // $_SESSION['profile_pic'] = $row['profile_pic'] ?? 'default.png';

        // Redirect based on role
        header("Location: ../DASHBOARD/userdashboard.php");
        exit;
    } else {
        echo "Incorrect password!";
    }
} else {
    echo "User not found!";
}

mysqli_stmt_close($stmt);
