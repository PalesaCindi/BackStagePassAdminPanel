<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "backstage_pass_adminpanel";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get booking ID from URL
if (!isset($_GET['id'])) {
    die("No booking ID provided");
}
$booking_id = intval($_GET['id']);

// Fetch booking data
$result = $conn->query("SELECT * FROM booking_info WHERE booking_id = $booking_id");
if ($result->num_rows == 0) {
    die("Booking not found");
}
$booking = $result->fetch_assoc();

// Fetch all events for dropdown
$events = $conn->query("SELECT * FROM even_info ORDER BY date DESC");

// Handle form submission (Update Booking)
if (isset($_POST['update_booking'])) {
    $user_name = $conn->real_escape_string($_POST['user_name']);
    $user_email = $conn->real_escape_string($_POST['user_email']);
    $event_id = intval($_POST['event_id']);
    $status = $_POST['status'];

    $sql = "UPDATE booking_info SET
                user_name='$user_name',
                user_email='$user_email',
                event_id=$event_id,
                status='$status'
            WHERE booking_id=$booking_id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Booking updated successfully'); window.location.href='booking_info.php';</script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Booking</title>
<style>
body { font-family: 'Poppins', sans-serif; background:#121212; color:#fff; padding:20px; }
form { background:#1e1e1e; padding:20px; border-radius:8px; max-width:600px; margin:auto; }
input, select { width:100%; padding:8px; margin-top:5px; border-radius:6px; border:none; margin-bottom:10px; }
button { padding:8px 12px; border:none; border-radius:6px; cursor:pointer; font-weight:500; }
.save { background:#28a745; color:#fff; }
.cancel { background:#dc3545; color:#fff; text-decoration:none; padding:8px 12px; display:inline-block; margin-left:5px;}
</style>
</head>
<body>

<!----------Topbar----------->  
<?php include("./includes/topbar.php");?>

<!----------Sidebar----------->  
<?php include("./includes/sidebar.php");?>

<h1>Edit Booking</h1>

<form method="POST">
    <label>User Name:</label>
    <input type="text" name="user_name" value="<?= htmlspecialchars($booking['user_name']) ?>" required>

    <label>User Email:</label>
    <input type="email" name="user_email" value="<?= htmlspecialchars($booking['user_email']) ?>" required>

    <label>Event:</label>
    <select name="event_id" required>
        <option value="">-- Select Event --</option>
        <?php while($event = $events->fetch_assoc()): ?>
            <option value="<?= $event['event_id'] ?>" <?= ($event['event_id'] == $booking['event_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($event['event_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Status:</label>
    <select name="status">
        <option value="pending" <?= $booking['status']=='pending'?'selected':'' ?>>Pending</option>
        <option value="confirmed" <?= $booking['status']=='confirmed'?'selected':'' ?>>Confirmed</option>
        <option value="cancelled" <?= $booking['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
    </select>

    <button type="submit" name="update_booking" class="save">Save Changes</button>
    <a href="booking_info.php" class="cancel">Cancel</a>
</form>

</body>
</html>
