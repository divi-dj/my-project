<?php
// Include the admin session check
require_once 'admin_session_check.php';
require 'db_connect.php';

// Get some basic stats for the dashboard
$stats = [
    'flavors' => 0,
    'orders' => 0,
    'reviews' => 0,
    'customers' => 0
];

// Count total flavors
$flavor_query = "SELECT COUNT(*) as count FROM flavors";
$flavor_result = $conn->query($flavor_query);
if ($flavor_result && $flavor_row = $flavor_result->fetch_assoc()) {
    $stats['flavors'] = $flavor_row['count'];
}

// Check if orders table exists and count orders (for future implementation)
$conn->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending',
    total_amount DECIMAL(10, 2)
)");

$order_query = "SELECT COUNT(*) as count FROM orders";
$order_result = $conn->query($order_query);
if ($order_result && $order_row = $order_result->fetch_assoc()) {
    $stats['orders'] = $order_row['count'];
}

// Check if reviews table exists and count reviews
$conn->query("CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    rating INT NOT NULL,
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$review_query = "SELECT COUNT(*) as count FROM reviews";
$review_result = $conn->query($review_query);
if ($review_result && $review_row = $review_result->fetch_assoc()) {
    $stats['reviews'] = $review_row['count'];
}

// Count users (future customers)
$user_query = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
$user_result = $conn->query($user_query);
if ($user_result && $user_row = $user_result->fetch_assoc()) {
    $stats['customers'] = $user_row['count'];
}

// Get latest flavors
$latest_flavors_query = "SELECT * FROM flavors ORDER BY created_at DESC LIMIT 5";
$latest_flavors_result = $conn->query($latest_flavors_query);
$latest_flavors = [];

if ($latest_flavors_result && $latest_flavors_result->num_rows > 0) {
    while ($row = $latest_flavors_result->fetch_assoc()) {
        $latest_flavors[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ice Cream Wonderland</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="logo">Ice Cream Admin</div>
            <nav>
                <ul>
                    <li><a href="admin.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="admin_flavors.php"><i class="fas fa-ice-cream"></i> Flavors</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                    <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
                    <li><a href="offers.php"><i class="fas fa-tag"></i> Offers</a></li>
                    <li><a href="admin_users.php"><i class="fas fa-user-shield"></i> Admin Users</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="content">
            <header>
                <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                <div class="user-menu">
                    <span>Admin</span>
                    <img src="images/admin-avatar.png" alt="Admin" class="avatar">
                </div>
            </header>
            
            <main>
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-ice-cream"></i>
                        </div>
                        <h3>Total Flavors</h3>
                        <div class="number"><?php echo $stats['flavors']; ?></div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3>Total Orders</h3>
                        <div class="number"><?php echo $stats['orders']; ?></div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Reviews</h3>
                        <div class="number"><?php echo $stats['reviews']; ?></div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Customers</h3>
                        <div class="number"><?php echo $stats['customers']; ?></div>
                    </div>
                </div>
                
                <div class="data-table">
                    <h2><i class="fas fa-clock"></i> Latest Flavors</h2>
                    <?php if (count($latest_flavors) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Flavor</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest_flavors as $flavor): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($flavor['image_path'])): ?>
                                                <img src="<?php echo htmlspecialchars($flavor['image_path']); ?>" alt="<?php echo htmlspecialchars($flavor['name']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px; vertical-align: middle;">
                                            <?php else: ?>
                                                <i class="fas fa-ice-cream" style="color: #d32f2f; margin-right: 10px; font-size: 1.2rem; vertical-align: middle;"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($flavor['name']); ?>
                                        </td>
                                        <td>₹<?php echo number_format($flavor['price'], 2); ?></td>
                                        <td>
                                            <?php if ($flavor['is_available']): ?>
                                                <span class="status-badge status-available">Available</span>
                                            <?php else: ?>
                                                <span class="status-badge status-unavailable">Unavailable</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="edit_flavor.php?id=<?php echo $flavor['id']; ?>" class="action-btn edit"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="admin_flavors.php?delete=<?php echo $flavor['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this flavor?')"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No flavors found. <a href="admin_flavors.php">Add your first flavor</a>!</p>
                    <?php endif; ?>
                </div>
                
                <div class="data-table">
                    <h2><i class="fas fa-rocket"></i> Quick Actions</h2>
                    <div style="padding: 20px;">
                        <a href="admin_flavors.php" class="btn btn-primary" style="margin-right: 10px;"><i class="fas fa-plus"></i> Add New Flavor</a>
                        <a href="orders.php" class="btn btn-secondary" style="margin-right: 10px;"><i class="fas fa-list"></i> View Orders</a>
                        <a href="reviews.php" class="btn btn-secondary"><i class="fas fa-comment"></i> View Reviews</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        // Add any JavaScript functionality here if needed
    </script>
</body>
</html>
