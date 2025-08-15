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
        .booked-slots {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .booked-slots th,
        .booked-slots td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .booked-slots th {
            background-color: #333;
            color: #fff;
        }

        .booked-slots tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="marquee-container">
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?>  CHOOSE YOUR BOOKED TICKET</strong></marquee>
    </div><br><br>
    <CENTER><a class="btn btn-primary" href="index.php" id="button">Home</a></CENTER>
    <center><h3><strong> AVAILABLE EVENTS</strong></h3></center><br><br>
    
    <table class="booked-slots">
            <thead>
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Venue</th>
                <th>Action</th>
               
            </tr>
            </thead>
            <tbody>
            <tr>
                    <td><strong>TAMIZHI</strong></td>
                    <td><strong>30/09/2023</strong></td>
                    <td><strong>K.S.KRISHNAN AUDITORIUM</strong></td>
                    <td><a class="btn btn-primary" href="ticket.php
                    " id="button">view</a></td>
                    
                </tr>
    
    
            </tbody>
    </table>
    
</body>
</html>