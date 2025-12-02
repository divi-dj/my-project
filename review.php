<?php
// Include the session check
require_once 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a Review - Ice Cream Wonderland</title>
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

        .review-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #d32f2f;
            margin-bottom: 30px;
        }

        .review-form label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
        }

        .review-form input,
        .review-form select,
        .review-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .review-form textarea {
            height: 150px;
            resize: vertical;
        }

        .rating-select {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .rating-select label {
            display: inline-block;
            cursor: pointer;
            font-size: 1.5rem;
            color: #ccc;
            margin: 0;
        }

        .rating-select input[type="radio"] {
            display: none;
        }

        .rating-select label:hover,
        .rating-select label:hover ~ label,
        .rating-select input[type="radio"]:checked ~ label {
            color: #ffb700;
        }

        .review-form button {
            display: block;
            background: #ff3d00;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin: 30px auto 0;
            transition: background 0.3s;
        }

        .review-form button:hover {
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
                    <li><a href="flavor.php">Menu</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="review-container">
        <h1>Write Your Review</h1>
        <form class="review-form" action="submit_review.php" method="post">
            <label for="flavor">Select Flavor</label>
            <select id="flavor" name="flavor" required>
                <option value="">-- Select a Flavor --</option>
                <option value="Chocolate Bliss">Chocolate Bliss</option>
                <option value="Strawberry Delight">Strawberry Delight</option>
                <option value="Classic Vanilla">Classic Vanilla</option>
                <option value="Mint Chocolate Chip">Mint Chocolate Chip</option>
                <option value="Caramel Swirl">Caramel Swirl</option>
                <option value="Cookie Dough">Cookie Dough</option>
            </select>

            <label>Rate Your Experience</label>
            <div class="rating-select">
                <input type="radio" name="rating" id="star5" value="5" required>
                <label for="star5">★</label>
                <input type="radio" name="rating" id="star4" value="4">
                <label for="star4">★</label>
                <input type="radio" name="rating" id="star3" value="3">
                <label for="star3">★</label>
                <input type="radio" name="rating" id="star2" value="2">
                <label for="star2">★</label>
                <input type="radio" name="rating" id="star1" value="1">
                <label for="star1">★</label>
            </div>

            <label for="review_text">Your Review</label>
            <textarea id="review_text" name="review_text" placeholder="Tell us about your experience..." required></textarea>

            <button type="submit">Submit Review</button>
        </form>
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
                    <li><a href="dashboard.php">Dashboard</a></li>
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