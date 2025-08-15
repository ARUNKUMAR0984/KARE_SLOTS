<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <title>Available venues</title>
    <LINK  rel="icon" href="kare_logo-removebg-preview.png" type="image/x-icon">
    <style>
        /* Style the marquee container */
        .marquee-container {
            background-color: #333; /* Background color */
            color: #fff; /* Text color */
            padding: 10px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }
    </style>
    <style>
        /* Define styles for the list */
        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 10px;
            background-color: #f0f0f0;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        /* Define styles for the hover effect */
        li:hover {
            background-color: #3498db;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="marquee-container">
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> CHOOSE YOUR SEMINAR HALL VENUE</strong></marquee>
    </div><br><br>
    <CENTER><a class="btn btn-primary" href="index.php" id="button">Home</a></CENTER>
    <center><h3><strong> PICK YOUR SLOT</strong></h3></center><br><br>
    <ul>
    <a href="SLOT1SUB.php" ><li><strong>1.SLOT - 1</strong></li></a>
    <a href="SLOT2SUB.php" ><li><strong>2.SLOT - 2</strong></li></a>
    <a href="TCseminarhall.php" ><li><strong>3.SLOT - 3</strong></li></a>
    <a href="libraryseminarhall.php" ><li><strong>4.SLOT - 4</strong></li></a>
    <a href="libraryseminarhall.php" ><li><strong>4.SLOT - 5</strong></li></a>
    </ul>
    
</body>
</html>