<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_start();
include("connection.php");
include("functions.php");
$host = 'localhost';
$username = 'root';
$password = '';

// Check if the user is logged in and get their email
$user_data = check_login($con);

$venues = ["slot1_it", "slot1_java", "slot1_iots","slot1_dpsd","slot1_pa","slot1_ds","slot1_dm","slot2_dpsd","slot2_iots","slot2_ds","slot2_dm","slot2_pa","slot2_it","slot2_java"];

// Initialize an array to store the results from all databases
$allResults = [];

foreach ($venues as $venue) {
    $mysqli = new mysqli($host, $username, $password, $venue);

    if ($mysqli->connect_error) {
        die("Connection failed for $venue: " . $mysqli->connect_error);
    }

    if ($user_data) {
        $registerNumber = $user_data['register_number'];
        $query = "SELECT * FROM users WHERE Register_number = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $registerNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        // Store the result in the allResults array
        $allResults[] = $result;

        // Close the statement and database connection for the current venue
        $stmt->close();
        $mysqli->close();
    }
}


if (isset($_POST['remove_booking'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $mysqli->prepare("SELECT Facultyname FROM users WHERE booking_id = ?");
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($facultyName);
        $stmt->fetch();

        $stmt = $mysqli->prepare("DELETE FROM users WHERE booking_id = ?");
        $stmt->bind_param('i', $booking_id);

        if ($stmt->execute()) {
            // Increment the available seats count when a booking is removed
            $updateSeatsQuery = "UPDATE faculties SET Available_Seats = Available_Seats + 1 WHERE Faculty_Name = ?";
            $updateSeatsStmt = $mysqli->prepare($updateSeatsQuery);
            $updateSeatsStmt->bind_param('s', $facultyName);
            $updateSeatsStmt->execute();

            header("Location: Slot1-IOT.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booked Slots</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="icon" href="kare_logo-removebg-preview.png" type="image/X-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #333;
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
        .button-container {
            text-align: center; /* Center align the buttons horizontally */
        }

        .button-container a {
            margin: 0 10px; /* Add some horizontal spacing between the buttons */
            display: inline-block; /* Display buttons inline */
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
<div class="container">
    <h2>My Courses</h2>
    <a class="btn btn-primary" href="index.php" id="button">Home</a><br><br>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>S.no</th>
            <th>Faculty Name</th>
            <th>Course Name</th>
            <th>Course Code</th>
            <th>Slot</th>
            <th>Venue</th>
            
        </tr>
        </thead>
        <tbody>
        <?php
        $sno = 1; // Initialize a counter for S.No
        foreach ($allResults as $result) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $sno . "</td>"; // Display the S.No
                echo "<td>" . $row['Facultyname'] . "</td>";
                echo "<td>" . $row['coursename'] . "</td>";
                echo "<td>" . $row['course_code'] . "</td>";
                echo "<td>" . $row['slot'] . "</td>";
                echo "<td>" . $row['venue'] . "</td>";
                
                echo "</tr>";
                $sno++; // Increment the counter
            }
        }
        if ($sno === 1) {
            echo "<tr><td colspan='5'>No bookings found.</td></tr>";
        }
        ?>
        
        </tbody>
    </table>
</div>
</body>
</html>