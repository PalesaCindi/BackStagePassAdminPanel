<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin Panel </title>
    <link rel = "stylesheet" href="./assets/css/style.css">
</head>
<body>

    <!----------Topbar----------->
    <?php include("./includes/topbar.php");?>

    <!----------Sidebar---------->
    <?php include ("./includes/sidebar.php");?>

    <!----------Main Content---------->
    
   </main>

   <div class="container">
    <h1>Goodbye!</h1>
    <p>You have successfully signed out.</p>
    <p>Redirecting to <a href="login.php" class="btn">Login Page</a>...</p>
</div>

</body>
</html>