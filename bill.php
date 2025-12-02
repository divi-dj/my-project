<?php
require_once 'db_connect.php';

// Get order details from URL parameters
$username = $_GET['username'];
$flavor_id = $_GET['flavor'];
$quantity = $_GET['quantity'];
$address = $_GET['address'];
$total_price = $_GET['total_price'];
$order_date = $_GET['order_date'];

// Fetch flavor name
$sql = "SELECT name FROM flavors WHERE id = $flavor_id";
$result = $conn->query($sql);
$flavor = $result->fetch_assoc()['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ice Cream Bill</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #ffdde1, #ee9ca7);
            text-align: center;
            color: #444;
            margin: 0;
            padding: 0;
        }

        .bill-container {
            background: #fff;
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .bill-header {
            color: #ff6b6b;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .bill-info {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .total-price {
            font-size: 20px;
            font-weight: bold;
            color: #d32f2f;
        }

        .submit-button {
            margin-top: 20px;
        }

        .submit-button button {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-button button:hover {
            background-color: #e63946;
        }
    </style>
</head>

<body>
    <div class="bill-container">
        <div class="bill-header">🍦 Ice Cream Bill 🍦</div>
        <div class="bill-info">Flavor: <strong><?php echo $flavor; ?></strong></div>
        <div class="bill-info">Address: <strong><?php echo $address; ?></strong></div>
        <div class="bill-info">Quantity: <strong><?php echo $quantity; ?></strong></div>
        <div class="total-price">Total: ₹<?php echo number_format($total_price, 2); ?></div>
        <div class="bill-info">Date: <strong><?php echo $order_date; ?></strong></div>

        <div class="submit-button">
            <button type="button" onclick="generatePDF()">Download Bill</button>
        </div>
    </div>

    <script>
        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const iceCreamName = "<?php echo $flavor; ?>";
            const address = "<?php echo $address; ?>";
            const quantity = "<?php echo $quantity; ?>";
            const totalPrice = "<?php echo number_format($total_price, 2); ?>";
            const date = "<?php echo $order_date; ?>";

            doc.setFontSize(16);
            doc.text('Ice Cream Bill', 105, 20, { align: 'center' });

            doc.setFontSize(12);
            doc.text(`Flavor: ${iceCreamName}`, 20, 40);
            doc.text(`Address: ${address}`, 20, 50);
            doc.text(`Quantity: ${quantity}`, 20, 60);
            doc.text(`Total: INR ${totalPrice}`, 20, 70);
            doc.text(`Date: ${date}`, 20, 80);

            doc.save('Ice_Cream_Bill.pdf');
        }
    </script>
</body>
</html>
