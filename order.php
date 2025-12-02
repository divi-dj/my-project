<?php
// Include database connection
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['customer_name'];
    $flavor = $_POST['flavor_id'];
    $quantity = $_POST['quantity'];
    $toppings = isset($_POST['toppings']) ? implode(', ', $_POST['toppings']) : '';
    $special_instructions = ''; // Assuming this field is not present in the form
    $delivery_method = $_POST['payment'];
    $delivery_address = $_POST['address'];
    $total_price = 0; // Calculate total price based on flavor and quantity
    $status = 'Pending';
    $order_date = date('Y-m-d H:i:s');

    // Check if username exists in registration table
    $sql = "SELECT Username FROM registration WHERE Username = '$username'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 0) {
        echo "Error: Username does not exist.";
        exit();
    }

    // Calculate total price
    $sql = "SELECT price FROM flavors WHERE id = $flavor";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $total_price = $row['price'] * $quantity;
    }

    // Insert data into orders table
    $sql = "INSERT INTO orders (username, flavor, size, quantity, toppings, special_instructions, delivery_method, delivery_address, total_price, status, order_date) 
            VALUES ('$username', '$flavor', '', '$quantity', '$toppings', '$special_instructions', '$delivery_method', '$delivery_address', '$total_price', '$status', '$order_date')";

    if (mysqli_query($conn, $sql)) {
        // Redirect to bill.php with necessary data
        header("Location: bill.php?username=$username&flavor=$flavor&quantity=$quantity&address=$delivery_address&total_price=$total_price&order_date=$order_date");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Ice Cream 🍦</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/heart.webp') no-repeat center center/cover;
            /* Set background */
            color: #333;
            overflow-x: hidden;
        }

        header {
            background-color: rgba(255, 77, 109, 0.9);
            /* Slight transparency */
            color: white;
            padding: 1rem 0;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: color 0.3s, transform 0.2s;
        }

        nav ul li a:hover {
            color: #ffdde1;
            transform: scale(1.1);
        }

        /* Order Form Section */
        .order-form-section {
            max-width: 600px;
            margin: 3rem auto;
            background-color: rgba(255, 255, 255, 0.95);
            /* Soft white with transparency */
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            position: relative;
            animation: popUp 1s ease-in-out;
        }

        .sticker {
            width: 80px;
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
        }

        h2 {
            color: #ff4d6d;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ffb3c1;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: 0.3s ease-in-out;
            margin-bottom: 15px;
        }

        input:focus,
        select:focus {
            border-color: #ff4d6d;
            transform: scale(1.02);
        }

        /* 🧁 Toppings Section (Neatly Aligned) */
        .toppings {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* Two columns for better alignment */
            gap: 0.8rem;
            margin: 1rem 0;
            text-align: left;
        }

        .toppings label {
            display: flex;
            align-items: center;
            font-size: 1rem;
            background: rgba(255, 223, 230, 0.7);
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }

        .toppings label:hover {
            background: rgba(255, 150, 160, 0.7);
        }

        .toppings input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        /* 🍦 Cute Button */
        .submit-btn {
            background-color: #ff4d6d;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: transform 0.2s ease-in-out;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background-color: #e63950;
            transform: scale(1.05);
        }

        footer {
            text-align: center;
            background-color: rgba(255, 77, 109, 0.9);
            color: white;
            padding: 1rem;
            margin-top: 2rem;
        }

        /* ✨ Cute Animations ✨ */
        @keyframes popUp {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 600px) {
            .order-form-section {
                width: 90%;
                padding: 1.5rem;
            }

            .sticker {
                width: 60px;
            }

            .toppings {
                grid-template-columns: 1fr;
            }

            /* Stack toppings on small screens */
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1>Order Ice Cream 🍨</h1>
            <nav>
                <ul>
                    <li><a href="index.php">🏠 Home</a></li>
                    <li><a href="flavor.php">🍦 Flavors</a></li>
                    <li><a href="review.php">⭐ Reviews</a></li>
                    <li><a href="order.php">🛒 Order</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="order-form-section">
            <img src="images/stcker.png" alt="Ice Cream" class="sticker"> <!-- Cute sticker -->
            <form class="order-form" action="order.php" method="POST">
                <h2>Ice Cream Order Form 🍨</h2>

                <label for="flavor">Choose Your Flavor:</label>
                <select id="flavor" name="flavor_id" required>
                    <?php
                    // Query to get available flavors
                    $sql = "SELECT id, name, price FROM flavors WHERE is_available = 1 ORDER BY name";
                    $result = $conn->query($sql);
                    
                    // Generate options dynamically
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<option value="'.$row['id'].'" data-price="'.$row['price'].'">'.$row['name'].' 🍦</option>';
                        }
                    } else {
                        // Fallback options if no flavors found in database
                        echo '<option value="1">Vanilla 🍦</option>';
                        echo '<option value="2">Chocolate 🍫</option>';
                        echo '<option value="3">Strawberry 🍓</option>';
                        echo '<option value="4">Mint 🍃</option>';
                        echo '<option value="5">Caramel 🍯</option>';
                    }
                    ?>
                </select>

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" required>

                <label for="toppings">Select Toppings:</label>
                <div class="toppings">
                    <label for="sprinkles">
                        <input type="checkbox" id="sprinkles" name="toppings[]" value="sprinkles">
                        ✨ Sprinkles
                    </label>

                    <label for="choco_chips">
                        <input type="checkbox" id="choco_chips" name="toppings[]" value="choco_chips">
                        🍫 Chocolate Chips
                    </label>

                    <label for="nuts">
                        <input type="checkbox" id="nuts" name="toppings[]" value="nuts">
                        🌰 Crushed Nuts
                    </label>

                    <label for="syrup">
                        <input type="checkbox" id="syrup" name="toppings[]" value="syrup">
                        🍯 Chocolate Syrup
                    </label>
                </div>

                <label for="customer_name">Your Username:</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Enter your username" required>

                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="address">📍 Delivery Address:</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required>

                <label for="payment">💳 Payment Mode:</label>
                <select id="payment" name="payment" required>
                    <option value="Cod">💵 Cash on Delivery</option>
                    <option value="Card">💳 Debit/Credit Card</option>
                    <option value="UPI">📲 UPI</option>
                    <option value="Amazon pay">🛒 Amazon Pay</option>
                </select>

                <input type="submit" class="submit-btn" value="🍦 Place Order">
            </form>
        </section>
    </main>

    <footer>
        <p>Made with ❤️ by Ice Cream Wonderland &copy; 2025</p>
    </footer>
</body>

</html>