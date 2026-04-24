<?php
require_once "db.php";

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Always hash passwords
$email = $_POST['email'];
// $role = "user"; // default role

$sql = "INSERT INTO users (username, password, email ) VALUES (?, ?, ? )";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "sss", $username, $password, $email);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../DASHBOARD/userdashboard.php");
        echo "successful";
    } else {
        echo "Failed to add record: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "SQL error: " . mysqli_error($conn);
}
?>
