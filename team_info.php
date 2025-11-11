<?php
include("./config/db.php"); // Connect to your database

$msg = "";

// -------------------- ADD NEW TEAM MEMBER --------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST['update_id'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imagePath = "./assets/BACKSTAGE_PICTURES/" . $imageName;

    if (move_uploaded_file($imageTmp, $imagePath)) {
        $sql = "INSERT INTO team_info (name, description, image_path) VALUES ('$name', '$description', '$imageName')";
        if ($conn->query($sql)) $msg = "Team member added successfully!";
        else $msg = "Error: " . $conn->error;
    } else {
        $msg = "Failed to upload image.";
    }
}

// -------------------- EDIT TEAM MEMBER --------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $imagePath = "./assets/BACKSTAGE_PICTURES/" . $imageName;
        move_uploaded_file($imageTmp, $imagePath);

        $sql = "UPDATE team_info SET name='$name', description='$description', image_path='$imageName' WHERE id=$id";
    } else {
        $sql = "UPDATE team_info SET name='$name', description='$description' WHERE id=$id";
    }

    if ($conn->query($sql)) $msg = "Team member updated successfully!";
    else $msg = "Error updating team member: " . $conn->error;
}

// -------------------- DELETE TEAM MEMBER --------------------
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($id > 0) {
        // Delete image file
        $res = $conn->query("SELECT image_path FROM team_info WHERE id=$id");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $old_image = __DIR__ . "/assets/BACKSTAGE_PICTURES/" . $row['image_path'];
            if (file_exists($old_image)) unlink($old_image);
        }
        // Delete record
        if ($conn->query("DELETE FROM team_info WHERE id=$id")) {
            $msg = "Team member deleted successfully!";
        } else {
            $msg = "Error deleting team member: " . $conn->error;
        }
    }
}

// -------------------- FETCH TEAM MEMBERS --------------------
$teamMembers = $conn->query("SELECT * FROM team_info");
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Team Members Infomation</title>
<link rel="stylesheet" href="./assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <title>Team Management</title>
   
    <style>
        body { font-family: 'Poppins', sans-serif; background:#ffff; color:#fff; padding:20px; }
        .team-container { padding: 30px; background: #fffbfbff; min-height: calc(100vh - 80px); color: #fff; display: flex; flex-direction: column; align-items: center; font-family: "Playpen Sans", 'cursive'; }
        h2, h3 { color: #1d1d1dff; margin-bottom: 10px; font-family: "Playpen Sans", 'cursive'; text-align: center; }
        .message { background: #27ae60; color: #fff; padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.3); }
        form { background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; margin-bottom: 40px; box-shadow: 0 0 8px rgba(0,0,0,0.4); max-width: 600px; font-family: "Playpen Sans", 'cursive'; }
        label { font-weight: 500; display: block; margin-bottom: 8px; color: #161616ff; }
        input[type="text"], textarea, input[type="file"] { width: 95%; padding: 10px; border: 1px solid #333; border-radius: 8px; background: #ffff; color: #131010ff; margin-bottom: 15px; font-size: 14px; }
        textarea { resize: vertical; }
        button { background: #f10f0fff; color: #1d1c1cff; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.3s; }
        button:hover { background: #d4ac0d; }
        a.cancel, a.edit, a.delete { text-decoration: none; padding: 8px 12px; border-radius: 6px; margin: 0 4px; font-weight: 500; }
        a.cancel { background: #555; color: #fff; } a.edit { background: #2980b9; color: #fff; } a.delete { background: #c0392b; color: #fff; }
        a.cancel:hover { background: #666; } a.edit:hover { background: #3498db; } a.delete:hover { background: #e74c3c; }
        .team-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; justify-content: center; align-items: center; width: 100%; }
        .team-card { background: rgba(255,255,255,0.05); border-radius: 16px; padding: 20px; text-align: center; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.4); }
        .team-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.6); }
        .team-card img { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; margin-bottom: 15px; border: 3px solid #f1c40f; }
        .team-card strong { display: block; font-size: 18px; margin-top: 10px; color: #f1c40f; }
        .team-card p { font-size: 14px; color: #0c0b0bff; margin: 10px 0 15px; }
    </style>
</head>
<body>
  <!----------Topbar----------->  
<?php include("./includes/topbar.php");?>

<!----------Sidebar----------->  
<?php include("./includes/sidebar.php");?>

<div class="team-container">
    <h2>Team Management</h2>

    <?php if($msg) echo "<div class='message'>$msg</div>"; ?>

    <!-- ADD / EDIT FORM -->
    <?php
    if (isset($_GET['edit_id'])) {
        $id = $_GET['edit_id'];
        $editMember = $conn->query("SELECT * FROM team_info WHERE id=$id")->fetch_assoc();
        $editName = $editMember['name'];
        $editDesc = $editMember['description'];
    } else {
        $editName = '';
        $editDesc = '';
    }
    ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="update_id" value="<?php echo $_GET['edit_id'] ?? ''; ?>">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo $editName; ?>" required>
        
        <label>Description:</label>
        <textarea name="description" required><?php echo $editDesc; ?></textarea>
        
        <label>Image:</label>
        <input type="file" name="image" <?php echo isset($_GET['edit_id']) ? '' : 'required'; ?>>
        
        <button type="submit"><?php echo isset($_GET['edit_id']) ? 'Update' : 'Add'; ?> Team Member</button>
    </form>

    <!-- TEAM CARDS -->
    <div class="team-grid">
        <?php while($member = $teamMembers->fetch_assoc()): ?>
            <div class="team-card">
                <img src="./assets/BACKSTAGE_PICTURES/<?php echo $member['image_path']; ?>" alt="">
                <strong><?php echo $member['name']; ?></strong>
                <p><?php echo $member['description']; ?></p>
                <a class="edit" href="?edit_id=<?php echo $member['id']; ?>">Edit</a>
                <a class="delete" href="?delete_id=<?php echo $member['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
