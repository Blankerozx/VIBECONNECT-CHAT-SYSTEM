<?php

if (session_status() === PHP_SESSION_NONE) {
    // session_start();
}

$host = "localhost";
$user = "root";
$pwd = "root";
$dbname = "vibeconnect";

$conn = new mysqli($host, $user, $pwd, $dbname);



if ($conn->connect_error) {
  die("connection failed: " . $conn->connect_error);
}

?>