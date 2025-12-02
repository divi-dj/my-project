<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch Flavor Details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM ice_cream WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $flavor = $result->fetch_assoc();
}

// Update Flavor
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
        $stmt = $conn->prepare("UPDATE ice_cream SET name=?, price=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("sdssi", $name, $price, $description, $image, $id);
    } else {
        $stmt = $conn->prepare("UPDATE ice_cream SET name=?, price=?, description=? WHERE id=?");
        $stmt->bind_param("sdsi", $name, $price, $description, $id);
    }

    $stmt->execute();
    header("Location: ice_cream.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Ice Cream</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="login-container">
        <h2>Edit Ice Cream Flavor</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" value="<?php echo $flavor['name']; ?>" required>
            <input type="number" name="price" step="0.01" value="<?php echo $flavor['price']; ?>" required>
            <textarea name="description" rows="3"><?php echo $flavor['description']; ?></textarea>
            <input type="file" name="image" accept="image/*">
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
