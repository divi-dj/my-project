<?php
$conn = new mysqli("localhost", "root", "", "ice_cream_shop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all reviews
$result = $conn->query("SELECT * FROM reviews ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
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
        .review-box {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .review-name {
            font-weight: bold;
            color: #ff6b6b;
        }
        .review-rating {
            color: gold;
        }
    </style>
</head>
<body>

    <h2>What Our Customers Say</h2>
    <div class="container">
        <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="review-box">
            <p class="review-name"><?php echo $row['name']; ?> (<?php echo $row['rating']; ?> ⭐)</p>
            <p><?php echo $row['review']; ?></p>
        </div>
        <?php } ?>
    </div>

</body>
</html>

<?php
$conn->close();
?>
