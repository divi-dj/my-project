<?php
$conn = new mysqli("localhost", "root", "", "ice_cream_shop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders for customer (Modify as per user session or email-based filtering)
$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Orders</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            background: #f8f9fa;
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
        .status {
            font-weight: bold;
        }
        .pending {
            color: orange;
        }
        .completed {
            color: green;
        }
    </style>
</head>
<body>

    <h2>Your Orders</h2>
    <div class="container">
        <table>
            <tr>
                <th>Order ID</th>
                <th>Flavor</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['flavor']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
                <td class="status <?php echo strtolower($row['status']); ?>">
                    <?php echo $row['status']; ?>
                </td>
            </tr>
            <?php } ?>

        </table>
    </div>

</body>
</html>

<?php
$conn->close();
?>
