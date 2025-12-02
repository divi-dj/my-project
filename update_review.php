<?php
// Include session check for admin authentication
require_once 'admin_session_check.php';
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get review ID and new status from form
    $review_id = intval($_POST['review_id']);
    $status = $_POST['status'];

    // Ensure status is one of the allowed values
    $allowed_statuses = ['Pending', 'Approved', 'Rejected'];
    if (!in_array($status, $allowed_statuses)) {
        die("Invalid status value.");
    }

    // Update the review status in the database using a prepared statement
    $stmt = $conn->prepare("UPDATE reviews SET review_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $review_id);

    if ($stmt->execute()) {
        echo "<script>alert('Review status updated successfully!'); window.location.href='reviews.php';</script>";
    } else {
        echo "Error updating review: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
