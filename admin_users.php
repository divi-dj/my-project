<?php
// Include the admin session check
require_once 'admin_session_check.php';
require 'db_connect.php';

// Process form submissions
$success_message = '';
$error_message = '';

// Add New Admin
if (isset($_POST['add_admin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    
    $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $error_message = "Username or email already exists!";
    } else {
        $insert_query = "INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, 'admin')";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ssss", $username, $password, $email, $full_name);
        
        if ($insert_stmt->execute()) {
            $success_message = "New admin user added successfully!";
        } else {
            $error_message = "Error adding admin: " . $conn->error;
        }
    }
}

// Delete Admin
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Don't allow deleting yourself
    if ($_SESSION['user_id'] == $id) {
        $error_message = "You cannot delete your own admin account!";
    } else {
        $delete_query = "DELETE FROM users WHERE id = ? AND role = 'admin'";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            $success_message = "Admin user deleted successfully!";
        } else {
            $error_message = "Error deleting admin: " . $conn->error;
        }
    }
}

// Fetch all admins
$query = "SELECT id, username, email, full_name, created_at FROM users WHERE role = 'admin' ORDER BY id ASC";
$result = $conn->query($query);
$admins = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin Users - Ice Cream Wonderland</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .admin-form h2 {
            margin-bottom: 20px;
            color: #d32f2f;
        }
        
        .form-row {
            margin-bottom: 15px;
        }
        
        .form-row label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-row input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th {
            background-color: #f5f5f5;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
            color: #333;
        }
        
        .admin-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .admin-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-delete {
            color: #c62828;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            transition: color 0.2s;
        }
        
        .btn-delete:hover {
            color: #d32f2f;
        }
        
        .admin-count {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-left: 10px;
        }
        
        .current-user {
            background-color: #e3f2fd;
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
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
                    <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
                    <li><a href="offers.php"><i class="fas fa-tag"></i> Offers</a></li>
                    <li><a href="admin_users.php" class="active"><i class="fas fa-user-shield"></i> Admin Users</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="content">
            <header>
                <h1><i class="fas fa-user-shield"></i> Manage Admin Users</h1>
                <div class="user-menu">
                    <span>Admin</span>
                    <img src="images/admin-avatar.png" alt="Admin" class="avatar">
                </div>
            </header>
            
            <main>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="admin-form">
                    <h2><i class="fas fa-user-plus"></i> Add New Admin User</h2>
                    <form method="POST">
                        <div class="form-row">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        
                        <div class="form-row">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-row">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-row">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>
                        
                        <button type="submit" name="add_admin" class="btn btn-primary">Add Admin</button>
                    </form>
                </div>
                
                <div class="data-table">
                    <h2><i class="fas fa-users-cog"></i> Admin Users <span class="admin-count"><?php echo count($admins); ?></span></h2>
                    
                    <?php if (!empty($admins)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Created On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $admin): ?>
                                    <tr class="<?php echo ($_SESSION['user_id'] == $admin['id']) ? 'current-user' : ''; ?>">
                                        <td><?php echo $admin['id']; ?></td>
                                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['full_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                                        <td>
                                            <?php if ($_SESSION['user_id'] != $admin['id']): ?>
                                                <div class="admin-actions">
                                                    <a href="admin_users.php?delete=<?php echo $admin['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this admin user?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <span><i class="fas fa-user-check"></i> Current User</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No admin users found.</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
