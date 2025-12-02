<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ice_cream_shop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update order status
if (isset($_POST['complete_order']) && isset($_POST['order_id'])) {
    $orderID = intval($_POST['order_id']);
    $updateQuery = "UPDATE orders SET status='Completed' WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $orderID);

    if ($stmt->execute()) {
        header("Location: admin_orders.php?success=Order Updated");
    } else {
        echo "Error updating order: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
