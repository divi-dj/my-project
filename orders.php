<?php
// Include the admin session check
require_once 'admin_session_check.php';
require 'db_connect.php';

// Fetch orders
$ordersQuery = $conn->query("SELECT o.*, f.name as flavor_name 
                           FROM orders o 
                           LEFT JOIN flavors f ON o.flavor = f.id 
                           ORDER BY o.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Ice Cream Wonderland</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fff8e1;
            color: #f57f17;
        }

        .status-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-canceled {
            background-color: #ffebee;
            color: #c62828;
        }

        .action-form {
            display: flex;
            gap: 10px;
        }

        select {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        
        @media (max-width: 768px) {
            .action-form {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="logo">Ice Cream Admin</div>
            <nav>
                <ul>
                    <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="admin_flavors.php"><i class="fas fa-ice-cream"></i> Flavors</a></li>
                    <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
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
                <h1><i class="fas fa-shopping-cart"></i> Order Management</h1>
                <div class="user-menu">
                    <span>Admin</span>
                    <img src="images/admin-avatar.png" alt="Admin" class="avatar">
                </div>
            </header>
            
            <main>
                <div class="data-table">
                    <?php if ($ordersQuery && $ordersQuery->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Flavor</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $ordersQuery->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td>
                                            <?php echo isset($order['customer_name']) ? htmlspecialchars($order['customer_name']) : 'N/A'; ?>
                                            <?php if(isset($order['email'])): ?>
                                                <div style="font-size: 0.8rem; color: #666;"><?php echo htmlspecialchars($order['email']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo isset($order['flavor_name']) ? htmlspecialchars($order['flavor_name']) : htmlspecialchars($order['flavor']); ?></td>
                                        <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                        <td><?php echo isset($order['order_date']) ? date('M d, Y', strtotime($order['order_date'])) : 'N/A'; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form class="action-form" action="update_order.php" method="POST">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="status">
                                                    <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Completed" <?php echo ($order['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="Canceled" <?php echo ($order['status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                                                </select>
                                                <button type="submit" class="action-btn edit"><i class="fas fa-save"></i> Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>No Orders Yet</h3>
                            <p>There are no orders in the system yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
