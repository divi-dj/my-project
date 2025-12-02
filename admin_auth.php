<?php
session_start();

// Hardcoded credentials (Replace with a database system later)
$admin_username = "admin";
$admin_password = "admin"; // In real applications, hash this password.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION["admin_logged_in"] = true;
        header("Location: admin.php");
        exit;
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href = 'admin_login.php';</script>";
    }
}
?>
