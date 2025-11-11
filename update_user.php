<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "backstage_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $user_id = intval($_GET['id']);

    if ($action == "accept") {
        $status = "accepted";
    } elseif ($action == "reject") {
        $status = "rejected";
    } else {
        $status = "pending";
    }

    $sql = "UPDATE users SET status='$status' WHERE id=$user_id";

    if ($conn->query($sql) === TRUE) {
        // redirect back to users page
        header("Location: users.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
