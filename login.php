<?php
session_start();
include("connection.php");
include("functions.php");

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $user_name = $_POST['Name'];
    $password = $_POST['Password'];
    

    if(!empty($user_name) && !empty($password) && !is_numeric($user_name))
    {
        
        $query = "select * from users where user_name = '$user_name' limit 1";          
        $result = mysqli_query($con,$query);
        if($result)
        {
            if($result && mysqli_num_rows($result)>0)
            {
                $user_data = mysqli_fetch_assoc($result);
                if($user_data['password'] === $password)
                {
                    $_SESSION['user_id'] = $user_data['user_id'];
                    header("Location:index.php");
                    die;

                }
            }
        }
        echo "Wrong User Credentials";
    }
    else
    {
        echo "Please enter some valid information!";
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
                    <input type="text" placeholder="User Name" name = "Name"><br><br>
                </div>

                <div class="input-field">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" placeholder="Password" name = "Password"><br><br>
                </div>
                
                <input class="button" type="submit" value="Log in"><br><br>

                <a class="button" href="signup.php" id="button">Click to signup</a><br><br>
                
                
            </div>
        </form>
    </div>

</div>
    
</body>
</html>