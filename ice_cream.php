<?php
session_start();
require 'db_connect.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Handle Add Ice Cream Flavor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_flavor'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Image Upload Handling
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO ice_cream (name, price, description, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $description, $image);
    $stmt->execute();
}

// Handle Delete Ice Cream Flavor
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM ice_cream WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ice_cream.php");
    exit;
}

// Fetch Ice Cream Flavors
$result = $conn->query("SELECT * FROM ice_cream");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ice Cream Management</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="ice_cream.php" class="active">Ice Cream Management</a></li>
                <li><a href="reviews.php">Customer Reviews</a></li>
                <li><a href="admin_users.php">Admin Users</a></li>
                <li><a href="offers.php">Limited-Time Offers</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <h2>🍦 Ice Cream Management</h2>

            <!-- Add Ice Cream Flavor Form -->
            <section class="add-flavor">
                <h3>Add New Flavor</h3>
                <form action="ice_cream.php" method="POST" enctype="multipart/form-data">
                    <label>Name:</label>
                    <input type="text" name="name" required>

                    <label>Price ($):</label>
                    <input type="number" name="price" step="0.01" required>

                    <label>Description:</label>
                    <textarea name="description" rows="3"></textarea>

                    <label>Image:</label>
                    <input type="file" name="image" accept="image/*" required>

                    <button type="submit" name="add_flavor">Add Flavor</button>
                </form>
            </section>

            <!-- Manage Ice Cream Flavors -->
            <section class="manage-flavors">
                <h3>Manage Existing Flavors</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="uploads/<?php echo $row['image']; ?>" width="50"></td>
                            <td><?php echo $row['name']; ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td>
                                <a href="edit_ice_cream.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                                <a href="ice_cream.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
