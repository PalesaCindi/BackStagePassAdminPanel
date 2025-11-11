<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "back_stage_pass database";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    //  Correct SQL: Select only where username matches
    $stmt = $conn->prepare("SELECT admin_id, username, email, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        //  Bind all selected columns
        $stmt->bind_result($id, $db_username, $db_email, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            //  Save session data
            $_SESSION['username'] = $db_username;
            $_SESSION['admin_id'] = $id;
            $_SESSION['email'] = $db_email;

            header("Location: index.php");
            exit();
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Username not found.";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> BACKSTAGE PASS </title>
<style>

body {
    height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
    background:url(./assets/BACKSTAGE_PICTURES/club2.png)repeat;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-container {
      width: 100%;
    max-width: 600px;
    background: rgba(0,0,0,0.6);  /* semi-transparent black */
    color: #fff;
    border-radius: 12px;
    padding: 30px;
    box-sizing: border-box;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
}

.login-container h1 {
    font-size: 28px;
    color: #ffcc00;
    margin-bottom: 10px;
}

.login-container h2 {
    font-size: 20px;
    margin-bottom: 25px;
}

.input-group {
    margin-bottom: 15px;
    text-align: left;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    color: #ccc;
    font-size: 14px;
}

.input-group input {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 6px;
    background-color: #2a2a2a;
    color: #fff;
    font-size: 14px;
    box-sizing: border-box;
}

.input-group input:focus {
    border: 1px solid #ffcc00;
    outline: none;
}

.btn {
    width: 100%;
    background-color: #ffcc00;
    color: #111;
    padding: 12px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

.btn:hover {
    background-color: #e6b800;
}

.login-text {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
}

.login-text a {
    color: #ffcc00;
    text-decoration: none;
}

.message {
    color: #ff6666;
    margin-bottom: 15px;
    font-size: 14px;
}
.message a {
    color: #ffcc00;
    text-decoration: none;
}
</style>
</head>
<body>

<div class="login-container">
    <h1>Login Page</h1>
    

    <?php if($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="input-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required />
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required />
        </div>

        <button type="submit" class="btn">Login</button>
    </form>

    <p class="login-text">
        Don't have an account? <a href="signup.php">Sign Up</a>
    </p>
</div>

</body>
</html>
