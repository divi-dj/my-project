<?php
// Include the admin session check
require_once 'admin_session_check.php';
require 'db_connect.php';

// Check if flavor ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_flavors.php");
    exit;
}

$flavor_id = intval($_GET['id']);

// Fetch the flavor details
$flavor_query = $conn->prepare("SELECT * FROM flavors WHERE id = ?");
$flavor_query->bind_param("i", $flavor_id);
$flavor_query->execute();
$flavor_result = $flavor_query->get_result();

if ($flavor_result->num_rows === 0) {
    header("Location: admin_flavors.php");
    exit;
}

$flavor = $flavor_result->fetch_assoc();

// Handle form submission for updating the flavor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_flavor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Handle image upload
    $image_path = $flavor['image_path']; // Keep existing image by default
    
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
            // Delete old image if exists
            if (!empty($flavor['image_path']) && file_exists($flavor['image_path'])) {
                unlink($flavor['image_path']);
            }
            
            $image_path = $target_file;
        }
    }
    
    // Update flavor in database
    $sql = "UPDATE flavors SET name = ?, description = ?, price = ?, image_path = ?, is_available = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsii", $name, $description, $price, $image_path, $is_available, $flavor_id);
    
    if ($stmt->execute()) {
        $success_message = "Flavor updated successfully!";
        
        // Refresh flavor data
        $flavor_query->execute();
        $flavor = $flavor_query->get_result()->fetch_assoc();
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flavor - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .edit-flavor-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            max-width: 800px;
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
        
        .current-image {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        
        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            border: none;
        }
        
        .btn-primary {
            background: #d32f2f;
            color: white;
        }
        
        .btn-secondary {
            background: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
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
                <h1><i class="fas fa-edit"></i> Edit Flavor</h1>
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
                <div class="alert alert-error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="edit-flavor-form">
                    <h2>Edit Flavor: <?php echo htmlspecialchars($flavor['name']); ?></h2>
                    
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-row">
                            <label for="name">Flavor Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($flavor['name']); ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" required><?php echo htmlspecialchars($flavor['description']); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <label for="price">Price ($)</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $flavor['price']; ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <label for="image">Flavor Image</label>
                            
                            <?php if (!empty($flavor['image_path'])): ?>
                            <div class="current-image">
                                <p>Current image:</p>
                                <img src="<?php echo $flavor['image_path']; ?>" alt="<?php echo htmlspecialchars($flavor['name']); ?>">
                            </div>
                            <?php endif; ?>
                            
                            <input type="file" id="image" name="image" accept="image/*">
                            <small>Leave empty to keep the current image</small>
                        </div>
                        
                        <div class="form-row">
                            <label>
                                <input type="checkbox" name="is_available" <?php echo $flavor['is_available'] ? 'checked' : ''; ?>>
                                Available for Purchase
                            </label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="update_flavor" class="btn btn-primary">Update Flavor</button>
                            <a href="admin_flavors.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
