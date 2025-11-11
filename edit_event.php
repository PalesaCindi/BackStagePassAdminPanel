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

// Get event ID from URL
if (!isset($_GET['id'])) {
    die("No event ID provided");
}
$event_id = intval($_GET['id']);

// Fetch event data
$result = $conn->query("SELECT * FROM even_info WHERE event_id = $event_id");
if ($result->num_rows == 0) {
    die("Event not found");
}
$event = $result->fetch_assoc();

// Fetch all artists
$artists = $conn->query("SELECT * FROM artistprofile ORDER BY name ASC");

// Handle form submission (Update Event)
if (isset($_POST['update_event'])) {
    $event_name = $conn->real_escape_string($_POST['event_name']);
    $location = $conn->real_escape_string($_POST['location']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $description = $conn->real_escape_string($_POST['description']);
    $artist_id = intval($_POST['artist_id']);
    $status = $_POST['status'];

    // Handle image upload
    $image_path = $event['image_path']; // keep old image if not replaced
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "./uploads/events/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_path = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    $sql = "UPDATE even_info SET
                event_name='$event_name',
                location='$location',
                date='$date',
                time='$time',
                description='$description',
                artist_id=$artist_id,
                status='$status',
                image_path='$image_path'
            WHERE event_id=$event_id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Event updated successfully'); window.location.href='even_info.php';</script>";
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
<title>Edit Event</title>
<style>
body { font-family: 'Poppins', sans-serif; background:#121212; color:#fff; padding:20px; }
form { background:#1e1e1e; padding:20px; border-radius:8px; max-width:600px; margin:auto; }
input, select, textarea { width:100%; padding:8px; margin-top:5px; border-radius:6px; border:none; margin-bottom:10px; }
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

<h1>Edit Event</h1>

<form method="POST" enctype="multipart/form-data">
    <label>Event Name:</label>
    <input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required>

    <label>Location:</label>
    <input type="text" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>

    <label>Date:</label>
    <input type="date" name="date" value="<?= $event['date'] ?>" required>

    <label>Time:</label>
    <input type="time" name="time" value="<?= $event['time'] ?>" required>

    <label>Description:</label>
    <textarea name="description"><?= htmlspecialchars($event['description']) ?></textarea>

    <label>Artist:</label>
    <select name="artist_id">
        <option value="">-- Select Artist --</option>
        <?php while($artist = $artists->fetch_assoc()): ?>
            <option value="<?= $artist['artist_id'] ?>" <?= ($artist['artist_id']==$event['artist_id'])?'selected':'' ?>>
                <?= htmlspecialchars($artist['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Status:</label>
    <select name="status">
        <option value="upcoming" <?= $event['status']=='upcoming'?'selected':'' ?>>Upcoming</option>
        <option value="completed" <?= $event['status']=='completed'?'selected':'' ?>>Completed</option>
        <option value="cancelled" <?= $event['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
    </select>

    <label>Image:</label>
    <?php if($event['image_path']): ?>
        <br><img src="<?= $event['image_path'] ?>" alt="Event Image" style="max-width:150px;"><br>
    <?php endif; ?>
    <input type="file" name="image"><br>

    <button type="submit" name="update_event" class="save">Save Changes</button>
    <a href="even_info.php" class="cancel">Cancel</a>
</form>

</body>
</html>
