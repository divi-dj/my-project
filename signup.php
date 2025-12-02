<?php
$success_alert = false;
$error_message = false;

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['pass'];

    $conn = mysqli_connect('localhost', 'root', '', 'ice_cream_shop');
    if ($conn->connect_error) {
        die('Connection Failed : ' . $conn->connect_error);
    } else {
        $check_query = "SELECT * FROM registration WHERE Username = '$username'";
        $result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($result) > 0) {
            $error_message = true;
        } 
        else {
            $sql = "INSERT INTO `registration` (`Fullname`, `Username`, `Password`) VALUES ('$fullname', '$username', '$password')";
            if ($conn->query($sql) === TRUE) {
                $success_alert = true;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/bg1.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .registercart {
            background: rgba(255, 255, 255, 0.9);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #ff6b6b;
            margin-bottom: 1.5rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.9rem;
            margin: 0.8rem 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            background: #f9f9f9;
        }

        button {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            background-color: #ff6b6b;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        button:hover {
            background-color: #ee5253;
            transform: scale(1.05);
        }

        h5 {
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        h5 a {
            color: #4267B2;
            text-decoration: underline;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: left;
            font-size: 1rem;
        }

        .alert.success {
            background-color: #c5ecb6;
            color: #307031;
            border: 1px solid #d6e9c6;
        }

        .alert.error {
            background-color: rgb(248, 212, 212);
            color: rgb(198, 26, 26);
            border: 1px solid #d6e9c6;
        }

        @media (max-width: 600px) {
            .registercart {
                padding: 1.5rem;
            }

            button {
                font-size: 1rem;
            }

            input {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body> 
    <?php
    if($success_alert){
        echo '<div class="alert success">Account created successfully! You can login now <a href="login.php" style="float: right;">Login</a></div>';
    }

    if($error_message){
        echo '<div class="alert error">Username already exists! Try another username</div>';
    }
    ?>

<div class="registercart">
    <h1>Sign Up</h1>
    <form method="POST">
        <input type="text" placeholder="Enter Your Full Name" name="fullname" required>
        <input type="text" placeholder="Set Your Username" name="username" required>
        <input type="password" placeholder="Set Your Password" name="pass" required>
        
        <div class="terms-container">
            <input type="checkbox" id="termsCheckbox" required>
            <label for="termsCheckbox">I agree to the <a href="#">Terms and Conditions</a></label>
        </div>

        <button>Submit</button>
    </form>
    <a href="login.php" class="login-button">Login</a> 
</div>

</body>
</html>
