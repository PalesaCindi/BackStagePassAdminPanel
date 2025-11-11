<?php
session_start();

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
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Generate a unique admin ID
        $admin_id = uniqid('admin_');
        
        // Securely hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = "admin";

        //  FIXED SQL statement (removed extra commas)
        $stmt = $conn->prepare("INSERT INTO admin (admin_id, username, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $admin_id, $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $message = "Account created successfully! <a href='login.php'>Login here</a>";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Backstage Pass</title>
<style>
/* Full page styling */
 body{
        display: flex;
        font-family: Arial, Helvetica, sans-serif;
        justify-content: center;
        align-items: center;
        background: url(./assets/BACKSTAGE_PICTURES/club.jpg) no-repeat;
        min-height: 100vb;
        background-size: cover;
        background-position: center;
       }

/* Signup container */
.signup-container {
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

/* Titles */
.signup-container h1 {
    font-size: 28px;
    color: #ffcc00;
    margin-bottom: 10px;
}

.signup-container h2 {
    font-size: 20px;
    margin-bottom: 25px;
}

/* Form fields */
.signup-form {
    text-align: left;
}

.input-group {
    margin-bottom: 15px;
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

/* Submit button */
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

/* Login link */
.login-text {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
}

.login-text a {
    color: #ffcc00;
    text-decoration: none;
}

.login-text a:hover {
    text-decoration: underline;
}

/* Message styling */
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

<div class="signup-container">
    <h1>Sign Up Page</h1>
    <h2>Create Account</h2>

    <?php if($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="signup-form">
        <div class="input-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required />
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required />
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required />
        </div>

        <div class="input-group">
            <label for="confirm-password">Confirm Password</label>
            <input type="password" name="confirm-password" id="confirm-password" required />
        </div>

        <button type="submit" class="btn">Sign Up</button>
    </form>

    <p class="login-text">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

</body>
</html>
