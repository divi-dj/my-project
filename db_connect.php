<?php
// Database configuration
$host = "localhost"; // Change if your database is hosted remotely
$user = "root"; // Default for XAMPP, change if different
$password = ""; // Default for XAMPP, set if you have a password
$database = "ice_cream_shop"; // Name of your database

// Create connection with error handling
try {
    // Create connection
    $conn = new mysqli($host, $user, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set character set to utf8mb4 for proper Unicode support
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log the error (to a file that's not web-accessible)
    error_log("Database connection error: " . $e->getMessage());
    
    // Display a user-friendly message
    if (!headers_sent()) {
        header("HTTP/1.1 500 Internal Server Error");
    }
    die("We're experiencing technical difficulties. Please try again later.");
}
?>
