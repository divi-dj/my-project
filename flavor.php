<?php
// Include the session check
require_once 'session_check.php';
// Include database connection
require_once 'db_connect.php';
// Session is already started in session_check.php
$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Get flavors with active offer information
$today = date('Y-m-d');
$sql = "SELECT f.*, 
        o.discount, 
        o.start_date, 
        o.end_date 
        FROM flavors f 
        LEFT JOIN limited_offers o ON f.id = o.flavor_id 
            AND o.start_date <= '$today' 
            AND o.end_date >= '$today' 
        WHERE f.is_available = 1 
        ORDER BY f.name ASC";

$result = $conn->query($sql);
$flavors = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flavors[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Flavors - Ice Cream Wonderland</title>
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

        .page-title {
            text-align: center;
            margin: 40px 0;
            color: #d32f2f;
            font-size: 2.5rem;
        }

        .flavors-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            padding: 0 20px 50px;
        }

        .flavor-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .flavor-card:hover {
            transform: translateY(-10px);
        }

        .flavor-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .flavor-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .flavor-card:hover .flavor-image img {
            transform: scale(1.1);
        }

        .flavor-details {
            padding: 20px;
        }

        .flavor-name {
            font-size: 1.5rem;
            color: #d32f2f;
            margin-bottom: 10px;
        }

        .flavor-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .flavor-price {
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .order-btn {
            display: inline-block;
            background: #ff3d00;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .order-btn:hover {
            background: #c62828;
        }

        footer {
            background-color: #d32f2f;
            color: white;
            padding: 40px 0 20px;
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

        /* Additional styles for displaying discounts */
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #d32f2f;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 10px;
        }
        
        .discounted-price {
            color: #d32f2f;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .offer-expiry {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 15px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php" style="color: white; text-decoration: none;">Ice Cream Wonderland</a></h1>
            <nav>
                <ul>
                    <?php if($loggedIn): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="signup.php">Signup</a></li>
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <h1 class="page-title">Our Delicious Flavors</h1>

    <div class="flavors-container">
        <?php if (count($flavors) > 0): ?>
            <?php foreach ($flavors as $flavor): 
                // Calculate discounted price if there's an active offer
                $has_discount = !empty($flavor['discount']);
                $original_price = $flavor['price'];
                $discounted_price = $has_discount ? $original_price * (1 - $flavor['discount'] / 100) : $original_price;
            ?>
                <div class="flavor-card">
                    <div class="flavor-image">
                        <?php if (!empty($flavor['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($flavor['image_path']); ?>" alt="<?php echo htmlspecialchars($flavor['name']); ?>">
                        <?php else: ?>
                            <img src="images/default-flavor.jpg" alt="<?php echo htmlspecialchars($flavor['name']); ?>">
                        <?php endif; ?>
                        
                        <?php if ($has_discount): ?>
                            <div class="discount-badge">
                                <?php echo $flavor['discount']; ?>% OFF
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flavor-details">
                        <h2 class="flavor-name"><?php echo htmlspecialchars($flavor['name']); ?></h2>
                        <p class="flavor-description"><?php echo htmlspecialchars($flavor['description']); ?></p>
                        
                        <?php if ($has_discount): ?>
                            <div class="flavor-price">
                                <span class="original-price">₹<?php echo number_format($original_price, 2); ?></span>
                                <span class="discounted-price">₹<?php echo number_format($discounted_price, 2); ?></span>
                            </div>
                            <p class="offer-expiry">Offer ends: <?php echo date('M d, Y', strtotime($flavor['end_date'])); ?></p>
                        <?php else: ?>
                            <p class="flavor-price">₹<?php echo number_format($flavor['price'], 2); ?></p>
                        <?php endif; ?>
                        
                        <a href="order.php?flavor=<?php echo $flavor['id']; ?>" class="order-btn">Order Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-flavors">
                <h2>No flavors available at the moment.</h2>
                <p>Please check back later for our delicious offerings!</p>
            </div>
        <?php endif; ?>
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
                <h3>Newsletter</h3>
                <p>Subscribe to our newsletter for updates on new flavors and special offers!</p>
                <form>
                    <input type="email" placeholder="Enter your email" style="width: 100%; padding: 8px; margin-top: 10px; border: none; border-radius: 4px;">
                    <button type="submit" style="background: #ff6b6b; border: none; color: white; padding: 8px 15px; margin-top: 10px; border-radius: 4px; cursor: pointer;">Subscribe</button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Ice Cream Wonderland - All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>