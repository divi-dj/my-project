<?php
// Include session check for admin authentication
require_once 'admin_session_check.php';
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get order ID and new status from form
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    // Ensure status is one of the allowed values
    $allowed_statuses = ['Pending', 'Completed', 'Canceled'];
    if (!in_array($status, $allowed_statuses)) {
        die("Invalid status value.");
    }

    // Update the order status in the database using a prepared statement
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order status updated successfully!'); window.location.href='orders.php';</script>";
    } else {
        echo "Error updating order: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
