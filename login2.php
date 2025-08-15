<?php
session_start();
include("connection.php");


$msg = ""; // Initialize the message variable

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['Password'];

    if (!empty($email) && !empty($password) && !is_numeric($email)) {

        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result) {
            if ($result && mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
                if ($user_data['password'] === $password) {
                    $_SESSION['user_id'] = $user_data['user_id'];
                    header("Location: index.php");
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
                <label for="forgotpassword"><a class="button"  href="forgot.php">Forgot password?</a></label><br><br>

                <a class="button" href="signup.php" id="button">Click to signup</a><br><br>
                
                
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