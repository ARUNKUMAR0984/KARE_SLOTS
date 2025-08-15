<?php
session_start();
include("connection.php");
include("functions.php");

$msg = ""; // Initialize the message variable

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_name = $_POST['Name'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber'] : '';
    $registerNumber = isset($_POST['registernumber']) ? $_POST['registernumber'] : '';
    
   
    $check_query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $check_query);
    
    if (mysqli_num_rows($result) > 0) {
        $msg = "<div class='alert alert-danger' style='color: white;'>Email is already registered.</div>";
    } else if (!empty($user_name) && !empty($email) && !empty($password) && !is_numeric($user_name)) {
        $user_id = random_num(20);
        $query = "INSERT INTO users (user_id, user_name, email, password, phonenumber,register_number) VALUES ('$user_id', '$user_name', '$email', '$password', $phonenumber,'$registerNumber')";
        
        if (mysqli_query($con, $query)) {
            $msg = "<div class='alert alert-success' style='color: white;'>Signup Successful</div>";
            echo '<script>window.location.href = "login2.php";</script>';
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        $msg = "<div class='alert alert-danger' style='color: white;'>Please enter some valid information!</div>";
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
        <h1>Sign up</h1>
        <?php echo isset($msg) ? $msg : ''; ?>
        <form method="post" autocomplete="off" onsubmit="return validateEmail();">
        <form method = "post" autocomplete="off" onsubmit="return validateEmail();">
            <div class="input-group">
                <div class="input-field">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" placeholder="Name" name = "Name" autocomplete="off" onfocus="this.removeAttribute('autocomplete');"><br><br>
                </div>

                <div class="input-field">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="user_email" placeholder="Enter mail id" name="Email" required><br><br>

                </div>
                <div class="input-field">
    <i class="fa-solid fa-envelope"></i>
    <input type="phonenumber" id="phonenumber" placeholder="Enter your phone number" name="phonenumber" required><br><br>
</div>
<div class="input-field">
    <i class="fa-solid fa-envelope"></i>
    <input type="phonenumber" id="registernumber" placeholder="Enter your Register number" name="registernumber" required><br><br>
</div>

                <div class="input-field">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" placeholder="Password" name = "Password"autocomplete="off" onfocus="this.removeAttribute('autocomplete');" ><br><br>
                </div>
               
                <input class="button" type="submit" value="Signup"><br><br>
                

                <a class="button" href="login2.php" id="button" title = "Already Registered">Click to login</a><br><br>
                
            </div>
        </form>
        
    </div>

</div>
<div id="popup" style="display: none;">
        <p>Email is already registered.</p>
    </div>
<script>
        function validateEmail() {
            var emailInput = document.getElementById("user_email");
            var emailValue = emailInput.value.trim();

        
            // Valid email format and domain
            return true;
        }
    </script>
    
</body>
</html>