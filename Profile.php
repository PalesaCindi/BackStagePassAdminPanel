<?php
include("./config/db.php");
$msg = "";

// -------- Add New Artist --------
if (isset($_POST['add_artist'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $image = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = $_FILES['image']['type'];

        if (in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = uniqid().'.'.$extension;

            $upload_dir = __DIR__ . "/assets/BACKSTAGE_PICTURES/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $upload_path = $upload_dir . $image;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $msg = "Error uploading image.";
            }
        } else {
            $msg = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        }
    }

    if (empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO profile(`name`, `genre`, `image_path`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $genre, $image);
        if ($stmt->execute()) {
            $msg = "Artist profile added successfully.";
        } else {
            $msg = "Error adding artist: " . $conn->error;
        }
        $stmt->close();
    }
}


// -------- Edit Artist --------
if (isset($_POST['edit_artist'])) {
    $artist_id = (int)$_POST['artist_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $image_sql = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = $_FILES['image']['type'];
        if (in_array($file_type, $allowed_types)) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_image = uniqid().'.'.$extension;
            $upload_path = "./assets/BACKSTAGE_PICTURES/".$new_image;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $result = $conn->query("SELECT image_path FROM profile WHERE artist_id=$artist_id");
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $old_image_path = "./assets/BACKSTAGE_PICTURES/".$row['image_path'];
                    if (file_exists($old_image_path)) unlink($old_image_path);
                }
                $image_sql = ", image_path='$new_image'";
            }
        } else {
            $msg = "Invalid file type for new image.";
        }
    }

    if (empty($msg)) {
        $sql = "UPDATE profile SET name='$name', genre='$genre' $image_sql WHERE artist_id=$artist_id";
        if ($conn->query($sql)) {
            $msg = "Artist profile updated successfully.";
        } else {
            $msg = "Error updating artist: ".$conn->error;
        }
    }
}


// -------- Delete Artist --------
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $result = $conn->query("SELECT * FROM profile WHERE id = $id");
        if ($result->num_rows>0){
            $row = $result->fetch_assoc();
            $image_path = "./assets/BACKSTAGE_PICTURES/".$row['image'];
            if(file_exists($image_path)) unlink($image_path);
        }
        $conn->query("DELETE FROM profile WHERE id=$id");
        $msg = "Artist profile deleted successfully.";
    }
}

// -------- Fetch All Artists --------
$artists = $conn->query("SELECT * FROM profile ORDER BY name ASC");

// -------- Fetch Artist for Editing --------
$edit_artist = null;
if(isset($_GET['edit'])){
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM profile WHERE artist_id=$id");
    if($res->num_rows>0){
        $edit_artist = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Artist Profiles</title>
<link rel="stylesheet" href="./assets/css/style.css">

</head>
<style>
 body { font-family: 'Poppins', sans-serif;  padding:20px;top:30px;

 
}

/* Page Navigation Tabs (Contracts / Endorsements */
.page-nav {
  margin-bottom: 25px;
  border-bottom: 2px solid #6a6c6eff;
  padding-bottom: 10px;
  text-align: center;
  
}

.page-nav ul {
  display:inline-flex;
  gap: 15px;
  list-style: none;
  margin: 0;
  padding: 0;
  justify-content: center; /* Centers the nav horizontally */
  align-items: center;
   width: fit-content; 
}

.page-nav ul li a {
  display: inline-block;
  padding: 10px 18px;
  border-radius: 8px;
  text-decoration: none;
  background: #faf5f5ff;
  color: #0d0e0dff;
  font-family: "Playpen Sans", 'cursive';
  font-weight: 500;
  transition: all 0.3s ease;
}
.page-nav a {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}
.page-nav ul li a:hover {
  background: #dd2534ff;
  color: #fff;
}

/* Alerts (Success / Error) */
.alert {
  padding: 12px 15px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-weight: 500;
}

.alert-success {
  background-color: rgba(76, 175, 80, 0.15);
  color: #4caf50;
  border-left: 4px solid #4caf50;
}

.alert-error {
  background-color: rgba(244, 67, 54, 0.15);
  color: #f44336;
  border-left: 4px solid #f44336;
}

/* Artist Form */
.artist-form {
  background: #373738ff;
  padding: 25px;
  font-family: "Playpen Sans", 'cursive';
  border-radius: 12px;
  max-width: 600px;
  margin-bottom: 40px;
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.97);
}
.h2 {
  margin-top: 0;
  margin-bottom: 15px;
  font-family: "Playpen Sans", 'cursive';
  text-align:center; 
  color: #0d0e0dff;
}
.artist-form h3 {
  margin-top: 0;
  margin-bottom: 15px;
  font-family: "Playpen Sans", 'cursive';
  text-align:center; 
  color: #0d0e0dff;
}

.artist-form label {
  display: block;
  margin-top: 12px;
  margin-bottom: 6px;
  font-weight: 500;
}

.artist-form input[type="text"],
.artist-form input[type="url"],
.artist-form select,
.artist-form textarea {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 8px;
  background: #fcfcfcff;
  color: #292828ff;
  outline: none;
}

.artist-form input[type="file"] {
  background: #0935e6ff;
  border-radius: 8px;
  padding: 8px;
  color: #fff;
}

.artist-form button {
  margin-top: 18px;
  padding: 10px 20px;
  border: none;
  background: #45a29e;
  color: #fff;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.artist-form button:hover {
  background: #66fcf1;
  color: #000;
}

.artist-form a {
  margin-left: 15px;
  color: #f44336;
  text-decoration: none;
}

.artist-form a:hover {
  text-decoration: underline;
}

/* Artist Grid Display */
.artist-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.artist-card {
  background: #1f2b38ff;
  border-radius: 12px;
  padding: 15px;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0);
}

.artist-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0 15px rgba(102,252,241,0.3);
}

.artist-card img {
  width: 100%;
  height: 210px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 10px;
}

.artist-card p {
  margin: 6px 0;
  font-size: 14px;
  color: #c5c6c7;
}

.artist-card strong {
  color: #66fcf1;
  font-size: 16px;
}

.artist-card a {
  color: #66fcf1;
  text-decoration: none;
  font-weight: 500;
}

.artist-card a:hover {
  text-decoration: underline;
}

</style>
<body>
<?php include("./includes/topbar.php"); ?>
<div class="container">
<?php include("./includes/sidebar.php"); ?>


<h2>Artist Profiles</h2>

<?php if($msg): ?>
<div class="alert <?= strpos($msg,'Error')!==false ? 'alert-error':'alert-success';?>">
    <?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="artist-form">
    <?php if($edit_artist): ?>
        <input type="hidden" name="artist_id" value="<?= $edit_artist['artist_id'] ?>">
        <input type="hidden" name="edit_artist" value="1">
        <h3>Edit Artist</h3>
    <?php else: ?>
        <input type="hidden" name="add_artist" value="1">
        <h3>Add Artist</h3>
    <?php endif; ?>

    <label>Artist Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($edit_artist['name'] ?? '') ?>" required>

    <label>Genre:</label>
    <select name="genre" required>
        <option value="">--Select Genre--</option>
        <?php
        $genres = ['Hip Hop','Amapiano','Lekompo','Popular'];
        foreach($genres as $g){
            $selected = ($edit_artist && ($edit_artist['genre'] ?? '') == $g) ? 'selected' : '';
            echo "<option value='$g' $selected>$g</option>";
        }
        ?>
    </select>

    <label>Upload Image:</label>
    <input type="file" name="image">
    <?php if($edit_artist && !empty($edit_artist['image_path'])): ?>
        <p>Current Image:<br>
        <img src="./assets/BACKSTAGE_PICTURES/<?= htmlspecialchars($edit_artist['image_path']) ?>" width="100" alt="Artist Image"></p>
    <?php endif; ?>

   

    <button type="submit"><?= $edit_artist ? 'Update Artist' : 'Add Artist' ?></button>
    <?php if($edit_artist): ?>
        <a href="artistprofile.php">Cancel</a>
    <?php endif; ?>
</form>

<hr>

<h3>All Artists</h3>
<div class="artist-grid">
    <?php while($row = $artists->fetch_assoc()): ?>
        <div class="artist-card">
            <img src="./assets/BACKSTAGE_PICTURES/<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <p><strong><?= htmlspecialchars($row['name']) ?></strong></p>
            <p>Genre: <?= htmlspecialchars($row['genre']) ?></p>
            <a href="?edit=<?= $row['artist_id'] ?>">Edit</a> | 
            <a href="?delete=<?= $row['artist_id'] ?>" onclick="return confirm('Are you sure you want to delete this artist?')">Delete</a>
        </div>
    <?php endwhile; ?>
</div>

</div>
</body>
</html>