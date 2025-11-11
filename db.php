<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "back_stage_pass database";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for emoji support
$conn->set_charset("utf8mb4");
?>