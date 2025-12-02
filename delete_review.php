<?php
// Include session check for admin authentication
require_once 'admin_session_check.php';
require 'db_connect.php';

if (isset($_GET['id'])) {
    $review_id = intval($_GET['id']);

    // Delete the review using a prepared statement
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);

    if ($stmt->execute()) {
        echo "<script>alert('Review deleted successfully!'); window.location.href='reviews.php';</script>";
    } else {
        echo "Error deleting review: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
