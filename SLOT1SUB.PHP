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
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> CHOOSE YOUR SUBJECT</strong></marquee>
    </div><br><br>
    <CENTER><a class="btn btn-primary" href="index.php" id="button">Home</a></CENTER>
    <center><h3><strong> SLOT - 1</strong></h3></center>
    <center><h3><strong> CHOOSE YOUR SUBJECT</strong></h3></center><br><br>
    <ul>
    <a href="Slot1-IOT.php" ><li><strong>1. 212ECE1400 - IOT Sensor and Devices</strong></li></a>
    <a href="Slot1_dpsd.php" ><li><strong>2. 212 CSE 2302 - DIGITAL PRINCIPLE AND SYSTEM DESIGN</strong></li></a>
    <a href="Slot1-java.php" ><li><strong>3. 212 CSE 2403 - JAVA PROGRAMMING</strong></li></a>
    <a href="Slot1-pa.php" ><li><strong>4. 213 CSE 2301 - PREDICTIVE ANALYSIS</strong></li></a>
    <a href="Slot1-it_infra.php" ><li><strong>5. 212 CSE 1101 - IT INFRASTRUCTURE AND LANDSCAPE OVERVIEW</strong></li></a>
    <a href="SLOT1-DM.php" ><li><strong>6. 212 CSE 2101 - DISCRETE MATHEMATICS</strong></li></a>
    <a href="SLOT1-DS.php" ><li><strong>7. 212 CSE 2301 - DATA STRUCTURES</strong></li></a>
    </ul>
    
</body>
</html>