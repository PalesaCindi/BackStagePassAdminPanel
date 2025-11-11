<?php
include("./config/db.php");
session_start();

// Initialize messages
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $artist_name = $conn->real_escape_string($_POST['artist_name']);
        $endorsement = $conn->real_escape_string($_POST['endorsement']);
        $bio = $conn->real_escape_string($_POST['bio']);
        $genre = $conn->real_escape_string($_POST['genre']);
        $booking_fee = floatval($_POST['booking_fee']);

        $social_media = json_encode([
            'tiktok' => $_POST['tiktok'] ?? '',
            'twitter' => $_POST['twitter'] ?? '',
            'instagram' => $_POST['instagram'] ?? '',
            'youtube' => $_POST['youtube'] ?? '',
            'applemusic' => $_POST['applemusic'] ?? '',
            'spotify' => $_POST['spotify'] ?? ''
        ]);

        // Handle file upload
        $profile_image = "";
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $upload_dir = "../uploads/artists/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = "artist_" . time() . "_" . uniqid() . "." . $ext;
            $target_file = $upload_dir . $filename;

            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($ext), $allowed)) {
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $profile_image = $filename;
                }
            }
        }

        // Insert into database
        $sql = "INSERT INTO artist_profiles (artist_name, social_media, endorsement, booking_fee, bio, genre, profile_image)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

        $stmt->bind_param("sssdsis", $artist_name, $social_media, $endorsement, $booking_fee, $bio, $genre, $profile_image);

        if ($stmt->execute()) {
            $success_message = "Artist profile created successfully!";
            $_POST = [];
        } else {
            throw new Exception("Failed to create artist profile: " . $stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Artist Profile</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="./assets/css/style.css"> 
<link rel="stylesheet" href="./assets/js/script.js">
<style>
/* Minimal styling for clarity */
body { font-family: Arial; padding: 20px; background: #f4f4f4; }
.container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 12px; }
.form-group { margin-bottom: 15px; }
label { display: block; margin-bottom: 5px; font-weight: bold; }
input[type="text"], input[type="url"], input[type="number"], select, textarea { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
textarea { resize: vertical; min-height: 80px; }
.submit-btn { background: #e0123f; color: white; padding: 12px 30px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
.submit-btn:hover { background: #c81038; }
.alert { padding: 12px; margin-bottom: 15px; border-radius: 6px; }
.alert-success { background: #d4edda; color: #155724; }
.alert-error { background: #f8d7da; color: #721c24; }
</style>
</head>
<body>
<?php include("./includes/topbar.php"); ?> <div class="admin-container"> 
    <?php include("./includes/sidebar.php"); ?>
<div class="container">
    <h2>Add Artist Profile</h2>

    <?php if($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if($error_message): ?>
        <div class="alert alert-error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <!-- Basic Info -->
        <div class="form-group">
            <label>Artist Name *</label>
            <input type="text" name="artist_name" value="<?php echo $_POST['artist_name'] ?? ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Genre</label>
            <select name="genre">
                <option value="">Select Genre</option>
                <option value="Hip Hop">Hip Hop</option>
                <option value="Amapiano">Amapiano</option>
                <option value="R&B">R&B</option>
                <option value="Pop">Pop</option>
                <option value="Maskandi">Maskandi</option>
                <option value="Rock">Rock</option>
                <option value="Jazz">Jazz</option>
                <option value="Electronic">Electronic</option>
                <option value="Classical">Classical</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label>Profile Image</label>
            <input type="file" name="profile_image" accept="image/*">
        </div>

        <div class="form-group">
            <label>Biography</label>
            <textarea name="bio"><?php echo $_POST['bio'] ?? ''; ?></textarea>
        </div>

        <!-- Social Media -->
        <h3>Social Media Links</h3>
        <div class="form-group">
            <label>TikTok</label>
            <input type="url" name="tiktok" value="<?php echo $_POST['tiktok'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label>Twitter</label>
            <input type="url" name="twitter" value="<?php echo $_POST['twitter'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label>Instagram</label>
            <input type="url" name="instagram" value="<?php echo $_POST['instagram'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label>YouTube</label>
            <input type="url" name="youtube" value="<?php echo $_POST['youtube'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label>Apple Music</label>
            <input type="url" name="applemusic" value="<?php echo $_POST['applemusic'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label>Spotify</label>
            <input type="url" name="spotify" value="<?php echo $_POST['spotify'] ?? ''; ?>">
        </div>

        <!-- Professional Info -->
        <div class="form-group">
            <label>Endorsement</label>
            <input type="text" name="endorsement" value="<?php echo $_POST['endorsement'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label>Booking Fee (R)</label>
            <input type="number" name="booking_fee" value="<?php echo $_POST['booking_fee'] ?? '0.00'; ?>" step="0.01" min="0">
        </div>

        <button type="submit" class="submit-btn">Create Artist Profile</button>
    </form>
</div>

</body>
</html>
