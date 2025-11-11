<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "back_stage_pass database";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -------- Confirm / Cancel Booking --------
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $booking_id = intval($_GET['id']);

    if ($action == "confirm") {
        $status = "confirmed";
    } elseif ($action == "cancel") {
        $status = "cancelled";
    } else {
        $status = "pending";
    }

    $conn->query("UPDATE booking_info SET status='$status' WHERE booking_id=$booking_id");
    header("Location: booking_info.php");
    exit;
}

// -------- Fetch All Bookings --------
$sql = "
SELECT 
  b.booking_id, 
  b.user_name, 
  b.user_email, 
  b.status, 
  b.booking_date,
  e.event_name, 
  p.name AS artist_name
FROM booking_info b
JOIN event_info e ON e.event_id = e.event_id
JOIN profile p ON e.artist_id = e.artist_id
ORDER BY b.booking_date DESC";

$bookings = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Management</title>
<link rel="stylesheet" href="./assets/css/style.css">
<link rel="stylesheet" href="./assets/js/script.js">

<!-- ===== CSS STYLES ===== -->
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 0;
}
h1 {
    text-align: center;
    color: #e0123f;
    margin-top: 30px;
    font-weight: 600;
}
table {
    width: 90%;
    border-collapse: collapse;
    margin: 30px auto;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}
th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}
th {
    background: #e0123f;
    color: white;
    font-weight: 600;
}
tr:hover {
    background: #f9f9f9;
}
.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.2s;
}
.btn:hover {
    opacity: 0.85;
}
.confirm { background: #28a745; color: #fff; }
.cancel { background: #dc3545; color: #fff; }
.pending { background: #ffcc00; color: #000; padding: 4px 10px; border-radius: 6px; }
.confirmed { color: #28a745; font-weight: 600; }
.cancelled { color: #dc3545; font-weight: 600; }

/* Optional responsive design */
@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }
  th { display: none; }
  td {
    padding: 10px;
    text-align: right;
    position: relative;
  }
  td::before {
    content: attr(data-label);
    position: absolute;
    left: 15px;
    text-align: left;
    font-weight: bold;
  }
}
</style>

<script>
function confirmAction(action, id){
    if(confirm("Are you sure you want to " + action + " this booking?")){
        window.location.href = "booking_info.php?action=" + action + "&id=" + id;
    }
}
</script>
</head>

<body>
<link rel="stylesheet" href="./assets/css/style.css">
<link rel="stylesheet" href="./assets/js/script.css">

<?php include("./includes/topbar.php"); ?>
<?php include("./includes/sidebar.php"); ?>

<h1>Booking Information</h1>

<table>
    <tr>
        <th>ID</th>
        <th>User Name</th>
        <th>Email</th>
        <th>Event</th>
        <th>Artist</th>
        <th>Status</th>
        <th>Booking Date</th>
        <th>Action</th>
    </tr>

    <?php if ($bookings && $bookings->num_rows > 0): ?>
        <?php while($row = $bookings->fetch_assoc()): ?>
            <tr>
                <td data-label="ID"><?= $row['booking_id'] ?></td>
                <td data-label="User Name"><?= htmlspecialchars($row['user_name']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($row['user_email']) ?></td>
                <td data-label="Event"><?= htmlspecialchars($row['event_name']) ?></td>
                <td data-label="Artist"><?= htmlspecialchars($row['artist_name']) ?></td>
                <td data-label="Status">
                    <?php if($row['status'] == 'pending'): ?>
                        <span class="pending"><?= ucfirst($row['status']) ?></span>
                    <?php elseif($row['status'] == 'confirmed'): ?>
                        <span class="confirmed">Confirmed</span>
                    <?php else: ?>
                        <span class="cancelled">Cancelled</span>
                    <?php endif; ?>
                </td>
                <td data-label="Booking Date"><?= $row['booking_date'] ?></td>
                <td data-label="Action">
                    <?php if($row['status']=='pending'): ?>
                        <button class="btn confirm" onclick="confirmAction('confirm', <?= $row['booking_id'] ?>)">Confirm</button>
                        <button class="btn cancel" onclick="confirmAction('cancel', <?= $row['booking_id'] ?>)">Cancel</button>
                    <?php elseif($row['status']=='confirmed'): ?>
                        <span class="confirmed">Confirmed</span>
                    <?php else: ?>
                        <span class="cancelled">Cancelled</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8" style="text-align:center; color:#555;">No bookings found</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
