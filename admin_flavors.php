<?php
// Include the admin session check
require_once 'admin_session_check.php';
require 'db_connect.php';

// Handle form submission for adding new flavor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_flavor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Handle image upload
    $image_path = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/flavors/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }
    
    // Insert flavor into database
    $sql = "INSERT INTO flavors (name, description, price, image_path, is_available) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $name, $description, $price, $image_path, $is_available);
    
    if ($stmt->execute()) {
        $success_message = "New flavor added successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Handle delete flavor
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get image path before deletion
    $image_query = $conn->prepare("SELECT image_path FROM flavors WHERE id = ?");
    $image_query->bind_param("i", $id);
    $image_query->execute();
    $image_result = $image_query->get_result();
    
    if ($image_result->num_rows > 0) {
        $image_data = $image_result->fetch_assoc();
        $image_path = $image_data['image_path'];
        
        // Delete the flavor from the database
        $delete_query = $conn->prepare("DELETE FROM flavors WHERE id = ?");
        $delete_query->bind_param("i", $id);
        
        if ($delete_query->execute()) {
            // Delete the image file if it exists
            if (!empty($image_path) && file_exists($image_path)) {
                unlink($image_path);
            }
            
            $success_message = "Flavor deleted successfully!";
        } else {
            $error_message = "Error deleting flavor: " . $delete_query->error;
        }
        
        $delete_query->close();
    }
    
    $image_query->close();
}

// Fetch all flavors
$flavors_query = "SELECT * FROM flavors ORDER BY name ASC";
$flavors_result = $conn->query($flavors_query);
$flavors = [];

if ($flavors_result && $flavors_result->num_rows > 0) {
    while ($row = $flavors_result->fetch_assoc()) {
        $flavors[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flavors - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .add-flavor-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .add-flavor-form h2 {
            margin-bottom: 20px;
            color: #d32f2f;
        }
        
        .form-row {
            margin-bottom: 20px;
        }
        
        .form-row label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-row input[type="text"],
        .form-row input[type="number"],
        .form-row textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: Arial, sans-serif;
        }
        
        .form-row textarea {
            height: 120px;
            resize: vertical;
        }
        
        .flavor-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 4px;
            object-fit: cover;
        }
        
        .action-column {
            width: 150px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-available {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-unavailable {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .flavor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .flavor-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .flavor-card-image {
            height: 180px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .flavor-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .flavor-card-details {
            padding: 15px;
        }
        
        .flavor-card-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #d32f2f;
        }
        
        .flavor-card-description {
            color: #666;
            margin-bottom: 10px;
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .flavor-card-price {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .flavor-card-status {
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .flavor-card-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .btn-delete-confirm {
            background-color: #f44336;
            color: white;
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
                    <li><a href="admin_flavors.php" class="active"><i class="fas fa-ice-cream"></i> Flavors</a></li>
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
                <h1><i class="fas fa-ice-cream"></i> Manage Flavors</h1>
                <div class="user-menu">
                    <span>Admin</span>
                    <img src="images/admin-avatar.png" alt="Admin" class="avatar">
                </div>
            </header>
            
            <main>
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="add-flavor-form">
                    <h2><i class="fas fa-plus-circle"></i> Add New Flavor</h2>
                    
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-row">
                            <label for="name">Flavor Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-row">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>
                        
                        <div class="form-row">
                            <label for="price">Price ($)</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-row">
                            <label for="image">Flavor Image</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        
                        <div class="form-row">
                            <label>
                                <input type="checkbox" name="is_available" checked>
                                Available for Purchase
                            </label>
                        </div>
                        
                        <div class="form-row">
                            <button type="submit" name="add_flavor" class="btn btn-primary">Add Flavor</button>
                        </div>
                    </form>
                </div>
                
                <div class="data-table">
                    <h2><i class="fas fa-list"></i> Current Flavors</h2>
                    
                    <?php if (count($flavors) > 0): ?>
                        <div class="flavor-grid">
                            <?php foreach ($flavors as $flavor): ?>
                                <div class="flavor-card">
                                    <div class="flavor-card-image">
                                        <?php if (!empty($flavor['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($flavor['image_path']); ?>" alt="<?php echo htmlspecialchars($flavor['name']); ?>">
                                        <?php else: ?>
                                            <i class="fas fa-ice-cream fa-3x" style="color: #ddd;"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flavor-card-details">
                                        <h3 class="flavor-card-name"><?php echo htmlspecialchars($flavor['name']); ?></h3>
                                        <p class="flavor-card-description"><?php echo htmlspecialchars($flavor['description']); ?></p>
                                        <p class="flavor-card-price">$<?php echo number_format($flavor['price'], 2); ?></p>
                                        
                                        <div class="flavor-card-status">
                                            <?php if ($flavor['is_available']): ?>
                                                <span class="status-badge status-available">Available</span>
                                            <?php else: ?>
                                                <span class="status-badge status-unavailable">Unavailable</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flavor-card-actions">
                                            <a href="edit_flavor.php?id=<?php echo $flavor['id']; ?>" class="btn btn-primary">Edit</a>
                                            <a href="admin_flavors.php?delete=<?php echo $flavor['id']; ?>" class="btn btn-secondary" onclick="return confirm('Are you sure you want to delete this flavor?')">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No flavors found. Add your first flavor above!</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        // Add any JavaScript functionality here if needed
    </script>
</body>
</html> 