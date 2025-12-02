<?php
// Include the session check
require_once 'session_check.php';

require 'db_connect.php';

// Get total sales
$salesQuery = $conn->query("SELECT SUM(total_price) AS total_sales FROM orders WHERE status='Completed'");
$salesData = $salesQuery->fetch_assoc();
$totalSales = $salesData['total_sales'] ?? 0;

// Get top flavors
$topFlavorsQuery = $conn->query("SELECT flavor, COUNT(*) AS count FROM orders GROUP BY flavor ORDER BY count DESC LIMIT 5");
$topFlavors = [];
while ($row = $topFlavorsQuery->fetch_assoc()) {
    $topFlavors[] = $row;
}

// Get current user info
$username = $_SESSION['username'];
$userQuery = $conn->query("SELECT * FROM registration WHERE Username = '$username'");
$userData = $userQuery->fetch_assoc();

// Get recent orders for this user
$ordersQuery = $conn->query("SELECT * FROM orders WHERE username = '$username' ORDER BY order_date DESC LIMIT 5");
$recentOrders = [];
while ($row = $ordersQuery->fetch_assoc()) {
    $recentOrders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Ice Cream Wonderland</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff5e1;
        }
        
        header {
            background: #d32f2f;
            color: white;
            padding: 10px 0;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            font-size: 1rem;
            font-weight: bold;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .container h1 {
            font-size: 1.6rem;
            margin: 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
            padding: 0;
            margin: 0;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: #ffccbc;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            padding: 0 20px;
        }

        .user-profile {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .user-profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .dashboard-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card {
            background: #ffe0b2;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 10px;
        }

        .stats-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #ffccbc;
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #d32f2f;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f5f5f5;
        }

        .btn {
            display: inline-block;
            background: #ff3d00;
            color: white;
            padding: 8px 15px;
            font-size: 0.9rem;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #c62828;
        }

        footer {
            background-color: #d32f2f;
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
            font-size: 0.9rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            padding: 0 20px;
        }
        
        .footer-section h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: #ff6b6b;
        }
        
        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-section ul li {
            margin-bottom: 8px;
        }
        
        .footer-section ul li a {
            color: #ffccbc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section ul li a:hover {
            color: white;
            text-decoration: underline;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-links a {
            color: white;
            background-color: rgba(255,255,255,0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background-color: #ff6b6b;
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php" style="color: white; text-decoration: none;">Ice Cream Wonderland</a></h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="user-profile">
            <img src="images/user-profile.jpg" alt="User Profile" onerror="this.src='images/default-user.jpg'">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>Member since: <?php echo isset($userData['created_at']) ? date('F d, Y', strtotime($userData['created_at'])) : 'N/A'; ?></p>
            <a href="#" class="btn">Edit Profile</a>
        </div>

        <div class="dashboard-content">
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($recentOrders); ?></div>
                    <div class="stat-label">Orders</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value">$<?php echo number_format($totalSales, 2); ?></div>
                    <div class="stat-label">Total Spent</div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="card-title">📋 Your Recent Orders</div>
                <?php if (count($recentOrders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Flavor</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo $order['flavor']; ?></td>
                            <td><?php echo isset($order['order_date']) ? date('M d, Y', strtotime($order['order_date'])) : 'N/A'; ?></td>
                            <td><?php echo $order['status']; ?></td>
                            <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>You haven't placed any orders yet.</p>
                <a href="order.php" class="btn">Place Your First Order</a>
                <?php endif; ?>
            </div>

            <div class="dashboard-card">
                <div class="card-title">🍦 Popular Flavors</div>
<ul>
    <?php foreach ($topFlavors as $flavor): ?>
        <li><?php echo $flavor['flavor'] . " - " . $flavor['count'] . " orders"; ?></li>
    <?php endforeach; ?>
</ul>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>Ice Cream Wonderland brings you the finest ice cream made with premium ingredients and crafted with love.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="flavor.php">Menu</a></li>
                    <li><a href="review.php">Write a Review</a></li>
                    <li><a href="order.php">Order Now</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contact Info</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Ice Cream Way, Wonderland</li>
                    <li><i class="fas fa-phone"></i> (555) 123-4567</li>
                    <li><i class="fas fa-envelope"></i> info@icecreamwonderland.com</li>
                    <li><i class="fas fa-clock"></i> Mon-Sun: 10:00 AM - 10:00 PM</li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Customer Support</h3>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Delivery Information</a></li>
                    <li><a href="#">Refund Policy</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Ice Cream Wonderland - All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>
