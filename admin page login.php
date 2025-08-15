<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("admin connect.php");


$msg = ""; // Initialize the message variable

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['email']) && isset($_POST['Password'])) {
        $email = $_POST['email'];
        $password = $_POST['Password'];

        if (!empty($email) && !empty($password) && !is_numeric($email)) {

            $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
            $result = mysqli_query($con, $query);

            if ($result) {
                if ($result && mysqli_num_rows($result) > 0) {
                    $user_data = mysqli_fetch_assoc($result);
                    if ($user_data['password'] === $password) {
                        $_SESSION['id'] = $user_data['id'];
                        header("Location: admin.php");
                        die;
                    } else {
                        // Incorrect password
                        $msg = "<div class='alert alert-success' style='color: white;'>Wrong Password</div>";
                    }
                } else {
                    // Email doesn't exist in the database
                    $msg = "<div class='alert alert-success' style='color: white;'>Email ID does not exist. Please sign up.</div>";
                }
            } else {
                // Error in database query
                $msg = "<div class='alert alert-success' style='color: white;'>Database error. Please try again.</div>";
            }
        } else {
            // Empty or invalid input
            $msg = "Please enter valid information.";
        }
    } else {
        // The 'email' or 'Password' keys are not set in $_POST
        $msg = "Please enter email and password.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KARE SLOTS</title>
     <LINK  rel="icon" href="kare_logo-removebg-preview.png" type="image/X - icon">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/14c0387f46.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
<marquee>KALASALINGAM UNIVERSITY,KRISHNAN KOIL,SRIVILLIPUTHUR,VIRUDHUNAGAR,626126</marquee>
    <div class="form-box">
        <h1>Login</h1>
        <form method = "post">
            <div class="input-group">
                <div class="input-field">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" placeholder="Enter mail id" name = "email"><br><br>
                </div>

                <div class="input-field">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" placeholder="Password" name = "Password"><br><br>
                </div>
                
                <input class="button" type="submit" value="Log in"><br><br>
                <a class="button" href="index.php" id="button">Click Here to Home Page</a><br><br>
                
                
                
            </div>
            <div id="popup" style="display: none;">
        <p><?php echo $msg; ?></p>
    </div>
        </form>
        
    </div>
    


</div>

<script>
        window.addEventListener("DOMContentLoaded", function() {
            var popup = document.getElementById("popup");
            
            // Show the message div if it contains any content
            if (popup.innerText.trim() !== "") {
                popup.style.display = "block";
            }
        });
    </script>
    
</body>
</html>