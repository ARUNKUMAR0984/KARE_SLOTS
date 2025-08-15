<?php

function build_calender($month,$year)
{
    $mysqli = new mysqli('localhost','root','','9thseminarhall');
    $stmt = $mysqli->prepare("select*from users where MONTH(date) =? AND YEAR(date)=?");
    $stmt-> bind_param('ss',$month,$year);
    $users = array();
    if($stmt->execute())
    {
        $result = $stmt->get_result();
        if($result->num_rows>0)
        {
            while($row = $result->fetch_assoc())
            {
                $users[] = $row['date'];
            }
            $stmt->close();
        }
    }



    $daysOfWeek = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t',$firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    $dateToday = date("Y-m-d");
    $calender = "<table class = 'table table-bordered'>";
$calender .= "<center><h2>$monthName $year</h2>";
$calender .="<a class='btn btn-xs btn-primary' href='?month=" .date('m',mktime(0,0,0,$month-1, 1, $year)) ."&year=".date('Y',mktime(0,0,0, $month-1, 1, $year)) ."'>Previous Month</a>";
$calender.= " <a class='btn btn-primary btn-xs'href='?month=".date('m')."&year=".date('Y')."'>CurrentMonth</a>";
$calender .= " <a class='btn btn-xs btn-primary' href='?month=" .date('m',mktime(0, 0, 0,$month+1, 1, $year)) ."&year=" .date('Y', mktime(0, 0, 0, $month+1, 1, $year)) ."'>Next Month </a></center><br>";
$calender .= "<tr>";

    foreach($daysOfWeek as $day)
    {
        $calender .= "<th class = 'header'>$day</th>";
    }
    $calender .= "</tr><tr>";

    if($dayOfWeek > 0)
    {
        for($k=0;$k<$dayOfWeek;$k++)
        {
            $calender .= "<td></td>";
        }
    }

    $currentDay = 1;
    $month = str_pad($month,2,"0",STR_PAD_LEFT);

    while($currentDay<=$numberDays)
    {
        if($dayOfWeek == 7)
        {
            $dayOfWeek=0;
            $calender .= "</tr><tr>";
        }
        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayname = strtolower(date("l",strtotime($date)));
        $evenNum = 0;
        $today = $date== date('Y-m-d')?"today":"";
        
        if ($dayname == 'saturday' || $dayname == 'sunday') {
            $calender .= "<td class='$today'><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>Holiday</button>";
        } elseif ($date < date('Y-m-d')) {
            $calender .= "<td class='$today'><h4>$currentDay</h4><button class='btn btn-danger btn-xs'>N/A</button>";
        } else {
            $totalbookings = checkSlots($mysqli,$date);
            if($totalbookings==9)
            {
                $calender .= "<td class='$today'><h4>$currentDay</h4><a href='9thseminarhallbooking.php?date=" . $date . "' class='btn btn-danger btn-xs'>All Slots Booked</a>";
            }
            else{$availableslots = 9-$totalbookings;
                $calender .= "<td class='$today'><h4>$currentDay</h4><a href='9thseminarhallbooking.php?date=" . $date . "' class='btn btn-success btn-xs'>View Slots</a><small><strong>  $availableslots slots Remaining</strong></small>";
            }
            
            
        }
        
        $calender .= "</td>";
        $currentDay++;
        $dayOfWeek++;
    }
    if($dayOfWeek !=7)
    {
        $remainingDays = 7-$dayOfWeek;
        for($i=0;$i<$remainingDays;$i++)
        {
            $calender .= "<td></td>";
        }
    }
    $calender .= "</tr>";
    $calender.="</table>";
    echo $calender;
}

function checkSlots($mysqli, $date)
{
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE date = ?");
    $stmt->bind_param('s', $date);
    $totalbookings = 0; // Initialize the count of bookings

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $totalbookings = $result->num_rows; // Get the count of bookings
        $stmt->close();
    }

    return $totalbookings;

}

$month = $_GET['month'] ?? date('m'); // Default to current month if not provided
$year = $_GET['year'] ?? date('Y');
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
    <title>Available slots</title>
    <LINK  rel="icon" href="kare_logo-removebg-preview.png" type="image/x-icon">
    <style>
        table{
            table-layout: fixed;
        }

        td{
            width: 33%;
        }
        .today{
            background: yellow;
        }
    </style>
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
</head>
<body>
<div class="marquee-container">
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> BOOK YOUR SLOTS AT 9TH BLOCK SEMINAR HALL</strong></marquee>
    </div>
    <br><br>
    
    <div class="container">
    <a class="btn btn-primary" href="index.php" id="button">Home</a>
    <a class="btn btn-primary" href="seminarhallvenues.php" id="button">Seminar Hall Venues</a>
    <center><h2><strong> 9TH BLOCK SEMINAR HALL</strong></h2></center>
        <div class="row">
            <div class="col-md-12">
            <?php
			   $dateComponents = getdate();
			   if (isset($_GET['month']) && isset($_GET['year'])) {
			   	$month = $_GET['month'];
			   	$year = $_GET['year'];
			   }else{
			   	$month = $dateComponents['mon'];
			   	$year = $dateComponents['year'];

			   }

			   echo build_calender($month, $year);
			?>

            </div>
        </div>
    </div>


    
</body>
</html>