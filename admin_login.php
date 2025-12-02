<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* General Styling */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background:rgb(237, 115, 227);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Login Container */
.login-container {
    background:rgb(244, 133, 178);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    width: 350px;
}

.login-container h2 {
    color: white;
    margin-bottom: 20px;
}

/* Form Fields */
.login-container label {
    display: block;
    text-align: left;
    font-size: 14px;
    margin: 10px 0 5px;
    color: white;
}

.login-container input {
    width: 90%;
    padding: 10px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    margin-bottom: 10px;
}

/* Button */
.login-container button {
    background: #ff69b4;
    color: white;
    padding: 10px;
    border: none;
    width: 100%;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: 0.3s;
}

.login-container button:hover {
    background: #ff1493;
}

/* Error Message */
.error-msg {
    color: white;
    background: #ff4d4d;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}
    </style>
</head>
<body>
    <section class="login-container">
        <h2>Admin Login</h2>
        <form action="admin_auth.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </section>
</body>
</html>
