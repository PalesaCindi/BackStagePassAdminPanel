<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "back_stage_pass database"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// -------- Add Event --------
if (isset($_POST['add_event'])) {
    $event_name = $conn->real_escape_string($_POST['event_name']);
    $location = $conn->real_escape_string($_POST['location']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $artist_id = intval($_POST['artist_id']);
    $status = $conn->real_escape_string($_POST['status']);

    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/events/"; // fixed path
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_name = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $_FILES['image']['name']);
        $image_path = $target_dir . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    $sql = "INSERT INTO event_info(event_name, location, date, time, artist_id, status, image_path, created_at)
            VALUES ('$event_name', '$location', '$date', '$time', $artist_id, '$status', '$image_path', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Event added successfully'); window.location.href='event_info.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// -------- Delete Event --------
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    $res = $conn->query("SELECT image_path FROM event_info WHERE event_id=$delete_id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (!empty($row['image_path']) && file_exists($row['image_path'])) unlink($row['image_path']);
    }

    $conn->query("DELETE FROM event_info WHERE event_id=$delete_id");
    echo "<script>window.location.href='event_info.php';</script>";
}

// -------- Edit Event --------
$edit_event = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $res = $conn->query("SELECT * FROM event_info WHERE event_id=$edit_id");
    if ($res && $res->num_rows > 0) {
        $edit_event = $res->fetch_assoc();
    }
}

// -------- Update Event --------
if (isset($_POST['update_event'])) {
    $id = intval($_POST['event_id']);
    $event_name = $conn->real_escape_string($_POST['event_name']);
    $location = $conn->real_escape_string($_POST['location']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $artist_id = intval($_POST['artist_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $image_path = $_POST['current_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/events/"; // fixed path
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_name = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $_FILES['image']['name']);
        $image_path = $target_dir . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    $sql = "UPDATE event_info SET 
            event_name='$event_name', location='$location', date='$date', time='$time',
            artist_id=$artist_id, status='$status', image_path='$image_path'
            WHERE event_id=$id";

    if ($conn->query($sql)) {
        echo "<script>alert('Event updated successfully'); window.location.href='event_info.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// -------- Fetch Events --------
$events = $conn->query(
    "SELECT e.event_id, e.event_name, e.location, e.date, e.time, e.artist_id, e.status, e.image_path, e.created_at,
            a.artist_name AS artist_name
     FROM event_info e
     LEFT JOIN artistprofile_details a ON e.artist_id = a.artist_id
     ORDER BY e.event_name DESC"
);

// -------- Fetch Artists --------
$artists = $conn->query("SELECT * FROM artistprofile_details ORDER BY artist_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Events Information</title>
<link rel="stylesheet" href="./assets/css/style.css">
<style>
body { font-family: 'Poppins', sans-serif;  color:#fff; padding:20px; }
h1 { text-align:center; color:#ffcc00; }
table { width:100%; border-collapse:collapse; background:#1e1e1e; margin-top:20px; }
th, td { padding:10px; border-bottom:1px solid #e0123fff; text-align:left; }
th { background:#333; }
tr:hover { background:#222; }
.btn { padding:6px 10px; border-radius:6px; cursor:pointer; border:none; font-weight:500; margin-right:5px;}
.add { background:#28a745; color:#fff; }
.edit { background:#ffc107; color:#000; }
.delete { background:#dc3545; color:#fff; }
form input, form select, form textarea { width:90%; padding:8px; margin-top:5px; border-radius:7px; border:none; margin-bottom:10px;}
form { background: #535353ff; 
    padding:15px; border-radius:8px; max-width:500px; margin:auto;}
img { max-width:100px; border-radius:6px; margin-top:5px;}
</style>
<script>
function confirmDelete(id) {
    if(confirm("Are you sure you want to delete this event?")) {
        window.location.href = "event_info.php?delete_id=" + id;
    }
}
</script>
</head>
<body>

<?php include("./includes/topbar.php");?>
<?php include("./includes/sidebar.php");?>

<h1>Event Info</h1>

<!-- Add/Edit Event Form -->
<form method="POST" enctype="multipart/form-data">
    <h2><?= $edit_event ? 'Edit Event' : 'Add Event' ?></h2>
    <input type="hidden" name="event_id" value="<?= $edit_event['event_id'] ?? '' ?>">
    <input type="hidden" name="current_image" value="<?= $edit_event['image_path'] ?? '' ?>">

    <label>Event Name:</label>
    <input type="text" name="event_name" value="<?= htmlspecialchars($edit_event['event_name'] ?? '') ?>" required>

    <label>Location:</label>
    <input type="text" name="location" value="<?= htmlspecialchars($edit_event['location'] ?? '') ?>" required>

    <label>Date:</label>
    <input type="date" name="date" value="<?= $edit_event['date'] ?? '' ?>" required>

    <label>Time:</label>
    <input type="time" name="time" value="<?= $edit_event['time'] ?? '' ?>" required>

    <label>Artist:</label>
    <select name="artist_id" required>
        <option value="">-- Select Artist --</option>
        <?php
        $artists->data_seek(0); // Reset artist pointer
        while($artist = $artists->fetch_assoc()):
        ?>
            <option value="<?= $artist['artist_id'] ?>" <?= ($edit_event && $edit_event['artist_id']==$artist['artist_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($artist['artist_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Status:</label>
    <select name="status" required>
        <?php
        $statuses = ['upcoming','completed','cancelled'];
        foreach($statuses as $status):
        ?>
            <option value="<?= $status ?>" <?= ($edit_event && $edit_event['status']==$status) ? 'selected' : '' ?>>
                <?= ucfirst($status) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Event Image:</label>
    <input type="file" name="image">
    <?php if($edit_event && !empty($edit_event['image_path']) && file_exists($edit_event['image_path'])): ?>
        <img src="<?= $edit_event['image_path'] ?>" alt="Event Image">
    <?php endif; ?>

    <button type="submit" name="<?= $edit_event ? 'update_event' : 'add_event' ?>" class="btn <?= $edit_event ? 'edit' : 'add' ?>">
        <?= $edit_event ? 'Update Event' : 'Add Event' ?>
    </button>
</form>

<!-- Events Table -->
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Location</th>
        <th>Date</th>
        <th>Time</th>
        <th>Artist</th>
        <th>Status</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php if($events->num_rows > 0): ?>
        <?php while($row = $events->fetch_assoc()): ?>
            <tr>
                <td><?= $row['event_id'] ?></td>
                <td><?= htmlspecialchars($row['event_name']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['time'] ?></td>
                <td><?= htmlspecialchars($row['artist_name']) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <?php if(!empty($row['image_path']) && file_exists($row['image_path'])): ?>
                        <img src="<?= $row['image_path'] ?>" alt="Event Image">
                    <?php else: ?>
                        <span style="color:#888;">No Image</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="event_info.php?edit_id=<?= $row['event_id'] ?>" class="btn edit">Edit</a>
                    <button onclick="confirmDelete(<?= $row['event_id'] ?>)" class="btn delete">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="9" style="text-align:center;">No events found</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
