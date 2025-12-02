<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ice_cream_shop"); // Change as per your DB settings

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all reviews
$result = $conn->query("SELECT * FROM reviews ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Review Management</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background: #ff6b6b;
            color: white;
        }
        .delete-btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            background: red;
            color: white;
            border-radius: 5px;
        }
        .logout {
            background: red;
            padding: 10px;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h2>Admin Review Management</h2>
    <div class="container">
        <table>
            <tr>
                <th>Review ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['rating']; ?> ⭐</td>
                <td><?php echo $row['review']; ?></td>
                <td>
                    <form method="POST" action="delete_review.php">
                        <input type="hidden" name="review_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_review" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>

        </table>
    </div>

    <form action="logout.php" method="POST">
        <button type="submit" class="logout">Logout</button>
    </form>

</body>
</html>

<?php
$conn->close();
?>
