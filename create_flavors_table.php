<?php
// Include database connection
require_once 'db_connect.php';

// Create flavors table
$sql = "CREATE TABLE IF NOT EXISTS flavors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255),
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Flavors table created successfully.<br>";
    
    // Insert sample flavors if table is empty
    $check = $conn->query("SELECT * FROM flavors LIMIT 1");
    
    if ($check->num_rows === 0) {
        // Array of sample flavors
        $sample_flavors = [
            [
                'name' => 'Chocolate Bliss',
                'description' => 'Rich and creamy chocolate ice cream made with premium cocoa.',
                'price' => 4.99,
                'image_path' => 'images/choco.jpg',
                'is_available' => 1
            ],
            [
                'name' => 'Strawberry Delight',
                'description' => 'Sweet and fruity strawberry ice cream with real strawberry chunks.',
                'price' => 4.99,
                'image_path' => 'images/strawberry.jpg',
                'is_available' => 1
            ],
            [
                'name' => 'Classic Vanilla',
                'description' => 'Smooth and creamy vanilla ice cream made with Madagascar vanilla beans.',
                'price' => 4.99,
                'image_path' => 'images/vanilla.jpg',
                'is_available' => 1
            ],
            [
                'name' => 'Mint Chocolate Chip',
                'description' => 'Refreshing mint ice cream filled with chocolate chunks.',
                'price' => 5.49,
                'image_path' => 'images/mint.jpg',
                'is_available' => 1
            ],
            [
                'name' => 'Caramel Swirl',
                'description' => 'Vanilla ice cream with rich caramel swirls throughout.',
                'price' => 5.49,
                'image_path' => 'images/caramel.jpg',
                'is_available' => 1
            ],
            [
                'name' => 'Cookie Dough',
                'description' => 'Vanilla ice cream packed with chunks of chocolate chip cookie dough.',
                'price' => 5.99,
                'image_path' => 'images/cookie.jpg',
                'is_available' => 1
            ]
        ];
        
        // Prepare and execute insert statements
        $stmt = $conn->prepare("INSERT INTO flavors (name, description, price, image_path, is_available) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssdsi", $name, $description, $price, $image_path, $is_available);
            
            foreach ($sample_flavors as $flavor) {
                $name = $flavor['name'];
                $description = $flavor['description'];
                $price = $flavor['price'];
                $image_path = $flavor['image_path'];
                $is_available = $flavor['is_available'];
                
                $stmt->execute();
            }
            
            $stmt->close();
            echo "Sample flavors have been added to the database.<br>";
        } else {
            echo "Error preparing statement: " . $conn->error . "<br>";
        }
    } else {
        echo "Flavors table already has data.<br>";
    }
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

$conn->close();
?> 