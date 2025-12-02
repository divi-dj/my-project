<?php
session_start();
include("db_connect.php"); // Ensure this file contains a valid $conn connection

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user details
$sql = "SELECT ID, Username, Fullname FROM registration WHERE Username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['ID'];
    $fullname = $user['Fullname'];
} else {
    header("Location: logout.php");
    exit();
}

// Fetch Order History
$order_sql = "SELECT id, ice_cream_name, quantity, total_price, order_status, order_date 
              FROM orders 
              WHERE user_id = ?";
$order_stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($order_stmt, "i", $user_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);

// Fetch Review History
$review_sql = "SELECT id, ice_cream_name, rating, review_text, review_date 
               FROM reviews 
               WHERE user_id = ?";
$review_stmt = mysqli_prepare($conn, $review_sql);
mysqli_stmt_bind_param($review_stmt, "i", $user_id);
mysqli_stmt_execute($review_stmt);
$review_result = mysqli_stmt_get_result($review_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/profile-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #ff6b6b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #ff6b6b;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($fullname); ?>!</h2>
    <p>Your Profile Information</p>
    
    <h3>Order History</h3>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Ice Cream Name</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Order Date</th>
        </tr>
        <?php while ($order = mysqli_fetch_assoc($order_result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($order['id']); ?></td>
                <td><?php echo htmlspecialchars($order['ice_cream_name']); ?></td>
                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                <td>$<?php echo htmlspecialchars($order['total_price']); ?></td>
                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
            </tr>
        <?php } ?>
    </table>

    <h3>Reviews</h3>
    <table>
        <tr>
            <th>Review ID</th>
            <th>Ice Cream Name</th>
            <th>Rating</th>
            <th>Review Text</th>
            <th>Review Date</th>
        </tr>
        <?php while ($review = mysqli_fetch_assoc($review_result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($review['id']); ?></td>
                <td><?php echo htmlspecialchars($review['ice_cream_name']); ?></td>
                <td><?php echo htmlspecialchars($review['rating']); ?>/5</td>
                <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                <td><?php echo htmlspecialchars($review['review_date']); ?></td>
            </tr>
        <?php } ?>
    </table>

    <a href="logout.php">Logout</a>
    <a href="index.php">Home</a>
</div>

</body>
</html>
