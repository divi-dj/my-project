<?php
// Include the admin session check
require_once 'admin_session_check.php';
require 'db_connect.php';

// Create limited_offers table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS limited_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flavor_id INT,
    discount INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flavor_id) REFERENCES flavors(id) ON DELETE CASCADE
)";

if (!$conn->query($create_table_sql)) {
    $error_message = "Error creating offers table: " . $conn->error;
}

// Initialize messages
$success_message = '';
$error_message = '';

// Handle form submission for adding new offer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_offer'])) {
    $flavor_id = intval($_POST['flavor_id']);
    $discount = intval($_POST['discount']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Validation
    if ($discount <= 0 || $discount > 100) {
        $error_message = "Discount must be between 1 and 100%";
    } else if (strtotime($end_date) < strtotime($start_date)) {
        $error_message = "End date cannot be before start date";
    } else {
        // Insert new offer
        $insert_sql = "INSERT INTO limited_offers (flavor_id, discount, start_date, end_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iiss", $flavor_id, $discount, $start_date, $end_date);
        
        if ($stmt->execute()) {
            $success_message = "New offer added successfully!";
        } else {
            $error_message = "Error adding offer: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Handle delete offer
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    $delete_sql = "DELETE FROM limited_offers WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success_message = "Offer deleted successfully!";
    } else {
        $error_message = "Error deleting offer: " . $stmt->error;
    }
    
    $stmt->close();
}

// Fetch all flavors for the dropdown
$flavors_query = "SELECT id, name FROM flavors WHERE is_available = 1 ORDER BY name ASC";
$flavors_result = $conn->query($flavors_query);
$flavors = [];

if ($flavors_result && $flavors_result->num_rows > 0) {
    while ($row = $flavors_result->fetch_assoc()) {
        $flavors[$row['id']] = $row['name'];
    }
}

// Fetch all offers with flavor names
$offers_sql = "SELECT o.*, f.name as flavor_name FROM limited_offers o 
               LEFT JOIN flavors f ON o.flavor_id = f.id 
               ORDER BY o.start_date DESC";
$offers_result = $conn->query($offers_sql);
$offers = [];

if ($offers_result && $offers_result->num_rows > 0) {
    while ($row = $offers_result->fetch_assoc()) {
        $offers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limited-Time Offers - Ice Cream Wonderland</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .offers-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .offers-form h2 {
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
        
        .form-row input, 
        .form-row select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .date-container {
            display: flex;
            gap: 15px;
        }
        
        .date-container .form-row {
            flex: 1;
        }
        
        .offers-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .offers-table th {
            background-color: #f5f5f5;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
            color: #333;
        }
        
        .offers-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .offers-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .discount-badge {
            background-color: #ffebee;
            color: #d32f2f;
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        
        .offer-actions {
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
        
        .offers-count {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-left: 10px;
        }
        
        .active-offer {
            background-color: #e8f5e9;
        }
        
        .inactive-offer {
            background-color: #f5f5f5;
            color: #999;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        
        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-upcoming {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .status-expired {
            background-color: #f5f5f5;
            color: #757575;
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
                    <li><a href="offers.php" class="active"><i class="fas fa-tag"></i> Offers</a></li>
                    <li><a href="admin_users.php"><i class="fas fa-user-shield"></i> Admin Users</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="content">
            <header>
                <h1><i class="fas fa-tag"></i> Limited-Time Offers</h1>
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
                
                <div class="offers-form">
                    <h2><i class="fas fa-plus-circle"></i> Add New Offer</h2>
                    
                    <?php if (empty($flavors)): ?>
                        <div class="alert alert-danger">
                            No flavors available. <a href="admin_flavors.php">Add flavors</a> before creating offers.
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="form-row">
                                <label for="flavor_id">Select Flavor</label>
                                <select id="flavor_id" name="flavor_id" required>
                                    <option value="">-- Select Flavor --</option>
                                    <?php foreach ($flavors as $id => $name): ?>
                                        <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <label for="discount">Discount Percentage (%)</label>
                                <input type="number" id="discount" name="discount" min="1" max="100" required>
                            </div>
                            
                            <div class="date-container">
                                <div class="form-row">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" required>
                                </div>
                                
                                <div class="form-row">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" name="end_date" required>
                                </div>
                            </div>
                            
                            <button type="submit" name="add_offer" class="btn btn-primary">Add Offer</button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="data-table">
                    <h2><i class="fas fa-list"></i> Current Offers <span class="offers-count"><?php echo count($offers); ?></span></h2>
                    
                    <?php if (!empty($offers)): ?>
                        <table class="offers-table">
                            <thead>
    <tr>
        <th>ID</th>
                                    <th>Ice Cream Flavor</th>
                                    <th>Discount</th>
        <th>Start Date</th>
        <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
    </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $today = date('Y-m-d');
                                foreach ($offers as $offer): 
                                    $status = '';
                                    $status_class = '';
                                    
                                    if ($offer['start_date'] > $today) {
                                        $status = 'Upcoming';
                                        $status_class = 'status-upcoming';
                                        $row_class = '';
                                    } else if ($offer['end_date'] < $today) {
                                        $status = 'Expired';
                                        $status_class = 'status-expired';
                                        $row_class = 'inactive-offer';
                                    } else {
                                        $status = 'Active';
                                        $status_class = 'status-active';
                                        $row_class = 'active-offer';
                                    }
                                ?>
                                    <tr class="<?php echo $row_class; ?>">
        <td><?php echo $offer['id']; ?></td>
                                        <td><?php echo htmlspecialchars($offer['flavor_name'] ?? 'Unknown Flavor'); ?></td>
                                        <td><span class="discount-badge"><?php echo $offer['discount']; ?>% OFF</span></td>
                                        <td><?php echo date('M d, Y', strtotime($offer['start_date'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($offer['end_date'])); ?></td>
                                        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                                        <td>
                                            <div class="offer-actions">
                                                <a href="offers.php?delete=<?php echo $offer['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this offer?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
        </td>
    </tr>
                                <?php endforeach; ?>
                            </tbody>
</table>
                    <?php else: ?>
                        <p>No offers found. Add your first offer above!</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        // Set default dates
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            if (startDateInput && endDateInput) {
                // Set default start date to today
                const today = new Date();
                const todayFormatted = today.toISOString().split('T')[0];
                startDateInput.value = todayFormatted;
                startDateInput.min = todayFormatted;
                
                // Set default end date to 7 days from today
                const nextWeek = new Date();
                nextWeek.setDate(today.getDate() + 7);
                endDateInput.value = nextWeek.toISOString().split('T')[0];
                endDateInput.min = todayFormatted;
            }
        });
    </script>
</body>
</html>
