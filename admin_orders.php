<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ice_cream_shop"); // Change as per your DB settings

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all orders
$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background: #ff6b6b;
            color: white;
        }
        .status-btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            color: white;
            border-radius: 5px;
        }
        .pending {
            background: orange;
        }
        .completed {
            background: green;
        }
        .logout {
            background: red;
            padding: 10px;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>Admin Order Management</h2>
    <div class="container">
        <table>
            <tr>
                <th>Order ID</th>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Address</th>
                <th>Payment</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['flavor']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['address']; ?></td>
                <td><?php echo $row['payment_method']; ?></td>
                <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
                <td><span class="<?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                <td>
                    <?php if ($row['status'] === 'Pending') { ?>
                        <form method="POST" action="update_order_status.php">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="complete_order" class="status-btn completed">Mark Complete</button>
                        </form>
                    <?php } else { ?>
                        <span class="status-btn completed">Completed</span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>

        </table>
    </div>

    <form action="logout.php" method="POST">
        <button type="submit" class="logout">Logout</button>
    </form>

</body>
</html>

<?php
$conn->close();
?>
