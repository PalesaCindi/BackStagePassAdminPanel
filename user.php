<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "back_stage_pass database";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle accept/reject actions
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
        header("Location: users.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch all users correctly
$sql = "SELECT id, username, email,password, status, created_at FROM user ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users | Admin Panel</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/js/script.css">
    
    <style>
body {
    color: #fff;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #ffffffff;
}
.container {
    margin-left: 250px; /* adjust if you have a sidebar */
    padding: 20px;
    top:30px;
    position: relative;
}
h1 {
    color: #ff0000ff;
    text-align: center;
    margin-bottom: 30px;
}
table {
    width: 100%;
    border-collapse: collapse;
    background-color: #1e1e1e;
    border-radius: 10px;
    overflow: hidden;
}
th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #333;
}
th {
    background-color: #333;
    color: #ffcc00;
}
tr:hover {
    background-color: #222;
}
.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.3s;
}
.accept {
    background-color: #28a745;
    color: white;
}
.reject {
    background-color: #dc3545;
    color: white;
}
.accept:hover {
    background-color: #218838;
}
.reject:hover {
    background-color: #c82333;
}
.status-pill {
    padding: 5px 10px;
    border-radius: 8px;
    font-weight: 600;
    text-transform: capitalize;
}
.status-pill.accepted {
    background-color: #28a745;
    color: white;
}
.status-pill.rejected {
    background-color: #dc3545;
    color: white;
}
.status-pill.pending {
    background-color: #ffcc00;
    color: black;
}
</style>
<script>
function confirmAction(action, id) {
    if (confirm("Are you sure you want to " + action + " this user?")) {
        window.location.href = "users.php?action=" + action + "&id=" + id;
    }
}
</script>
</head>
<body>

    <!----------Topbar----------->
    <?php include("./includes/topbar.php");?>

    <!----------Sidebar---------->
    <?php include ("./includes/sidebar.php");?>

    <!----------Main Content---------->
    <main class="container">
        <h1>Registered Users</h1>
        <table>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Registered On</th><th>Action</th>
            </tr>

            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = (int)$row['id'];
                    $name = htmlspecialchars($row['name']);
                    $email = htmlspecialchars($row['email']);
                    $status = htmlspecialchars($row['status']);
                    $created = htmlspecialchars($row['created_at']);
                    $statusClass = strtolower($status);

                    echo "<tr>
                            <td>{$id}</td>
                            <td>{$name}</td>
                            <td>{$email}</td>
                            <td><span class='status-pill {$statusClass}'>{$status}</span></td>
                            <td>{$created}</td>
                            <td>";

                    if ($status === 'pending') {
                        echo "<button class='btn accept' onclick=\"confirmAction('accept', {$id})\">Accept</button>
                              <button class='btn reject' onclick=\"confirmAction('reject', {$id})\">Reject</button>";
                    } elseif ($status === 'accepted') {
                        echo "<span style='color:#28a745;font-weight:700;'>Accepted</span>";
                    } else {
                        echo "<span style='color:#dc3545;font-weight:700;'>Rejected</span>";
                    }

                    echo "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center; padding:18px;'>No registered users found.</td></tr>";
            }

            $conn->close();
            ?>
        </table>
        </main>
</body>
</html>
