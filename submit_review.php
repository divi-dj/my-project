<?php
// Include the session check
require_once 'session_check.php';
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the username from the session
    $username = $_SESSION['username'];
    
    // Get form data
    $flavor = htmlspecialchars($_POST['flavor']);
    $rating = intval($_POST['rating']);
    $review_text = htmlspecialchars($_POST['review_text']);
    
    // Use prepared statement for security
    $stmt = $conn->prepare("INSERT INTO reviews (username, flavor, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $username, $flavor, $rating, $review_text);
    
    if ($stmt->execute()) {
        echo "<script>alert('Review submitted successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>
