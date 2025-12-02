<?php
// Include the admin session check
require_once 'admin_session_check.php';
require 'db_connect.php';

// Check if the "customers" table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'customers'");
if ($tableCheck->num_rows == 0) {
    die("Error: The 'customers' table does not exist. Please create it in your database.");
}

// Fetch all customers
$query = "SELECT id, fullname, username, email FROM customers ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Handle deletion
if (isset($_POST['delete'])) {
    $id = intval($_POST['customer_id']);
    mysqli_query($conn, "DELETE FROM customers WHERE id = $id");
    header("Location: customers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <main class="content">
            <h2>Customers</h2>
            <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="customer_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete" onclick="return confirm('Are you sure?')">❌ Remove</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php else: ?>
                <p>No customers found.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
