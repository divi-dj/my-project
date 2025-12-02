<?php
$conn = new mysqli("localhost", "root", "", "ice_cream_shop");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM icecreams";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Description</th><th>Image</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['price']}</td>";
        echo "<td>{$row['description']}</td>";
        echo "<td><img src='{$row['image']}' alt='{$row['name']}' width='50'></td>";
        echo "<td>
                <a href='edit_flavor.php?id={$row['id']}'>Edit</a> |
                <a href='delete_flavor.php?id={$row['id']}'>Delete</a>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No flavors found.";
}

$conn->close();
?>
