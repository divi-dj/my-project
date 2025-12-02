<?php
// Include the admin session check
require_once 'admin_session_check.php';
require 'db_connect.php';

// Fetch all reviews
$reviewsQuery = $conn->query("SELECT * FROM reviews ORDER BY id DESC");

// Check for query error
if (!$reviewsQuery) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Ice Cream Wonderland</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="logo">Ice Cream Admin</div>
            <nav>
                <ul>
                    <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="admin_flavors.php"><i class="fas fa-ice-cream"></i> Flavors</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="reviews.php" class="active"><i class="fas fa-star"></i> Reviews</a></li>
                    <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
                    <li><a href="offers.php"><i class="fas fa-tag"></i> Offers</a></li>
                    <li><a href="admin_users.php"><i class="fas fa-user-shield"></i> Admin Users</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="content">
            <header>
                <h1><i class="fas fa-star"></i> Customer Reviews</h1>
                <div class="user-menu">
                    <span>Admin</span>
                    <img src="images/admin-avatar.png" alt="Admin" class="avatar">
                </div>
            </header>
            
            <main>
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Review</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($review = $reviewsQuery->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $review['id']; ?></td>
                                <td><?php echo htmlspecialchars($review['username']); ?></td>
                                <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                                <td><?php echo $review['rating']; ?> ⭐</td>
                                <td>
                                <span class="status-badge status-<?php echo strtolower($review['review_status']); ?>">
                                <?php echo $review['review_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="update_review.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                        <select name="status">
                                            <option value="Pending" <?php echo ($review['review_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Approved" <?php echo ($review['review_status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                            <option value="Rejected" <?php echo ($review['review_status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <button type="submit" class="action-btn edit"><i class="fas fa-save"></i> Update</button>
                                    </form>
                                    <a href="delete_review.php?id=<?php echo $review['id']; ?>" onclick="return confirm('Are you sure?')" class="action-btn delete"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
