<?php
session_start();
include("connection.php");
include("functions.php");
$user_data = check_login($con);


$servername = "localhost";
$username = "root";
$password = "";
$database = "tamilmandram";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $user_data['email'];

// Add a SQL query to retrieve the data from the database based on the email
$sql = "SELECT name, register_number,academic_year, degree, phone_number,section FROM users WHERE email = '$email'";

$result = $conn->query($sql);

// Initialize variables to store retrieved data
$name = "";
$registerNumber = "";
$department = "";
$phone = "";
$academic_year="";
$section="";

// Check if there are rows in the result
if ($result->num_rows > 0) {
    // Fetch the data from the first row (assuming one row per email)
    $row = $result->fetch_assoc();
    $name = $row["name"];
    $registerNumber = $row["register_number"];
    $department = $row["degree"];
    $phone = $row["phone_number"];
    $academic_year=$row["academic_year"];
    $section=$row["section"];
} else {
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <LINK rel="icon" href="kare_logo-removebg-preview.png" type="image/X-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 50vh;
        }

        .ticket {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            width: 375px; /* Increased width */
            padding: 50px;
            border-radius: 50px;
            text-align: center;
        }

        .ticket-header img {
            max-width: 50%;
            height: auto;
            border-radius: 5px;
        }

        .ticket-header h1 {
            font-size: 30px; /* Increased font size */
            margin-top: 10px;
        }

        
        .ticket-info,
        .seat-info {
            text-align: left;
        }

        .ticket-info p,
        .seat-info p {
            margin: 0;
            font-size: 16px; /* Increased font size */
        }

        .ticket-info span,
        .seat-info span {
            font-weight: bold;
        }

        .barcode img {
            margin-top: 20px;
            max-width: 100%;
            height: auto;
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
    <title>Your Ticket</title>
</head>
<body>

<div class="ticket">
<div class="marquee-container">
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> HERE IS YOUR EVENT TICKET CONFIRMATION</strong></marquee>
    </div><br>
    
    <div class="button-container">
    <a class="btn btn-primary" href="tamilmandramavail.php">Book More Tickets</a>
    <a class="btn btn-primary" href="index.php">HOME</a>
</div><br><br>
<?php
    if ($result->num_rows > 0) {
        // Data found for this email, display the ticket details
    ?>
    <div class="ticket-header">
        <img src="Tamil mandram.jpeg" alt="Event Poster">
        <h1><strong>MIRTH</strong></h1>
    </div>
    
    <div class="ticket-details">
        <div class="ticket-info">
        <span><p>NAME:  <span><?php echo $user_data['user_name']; ?></span></p>
            <span><p>Date: <span>SEPTEMBER 29, 2023</span></p>
            <span><p>Time: <span>09:00 AM</span></p>
            <span><p>Venue: K S AUDITORIUM</span></p>
        </div>
        <div class="seat-info">
        <span><p>Price: <span>₹150</span></p>
        <span><p>Confirmation: <span>Confirmed</span></p>
        </div>
    </div>
    <div class="barcode">
    <!-- Display the QR code image -->
    <?php
    // Generate and display the QR code
    $email = $user_data['email'];
$userData = "Name: $name\nRegister Number: $registerNumber\nDepartment: $department\nSection: $section\nAcademic Year: $academic_year\nPhone: $phone\nEvent Name: Mirth \nVenue: K S krishnan Auditorium\nDate: 29/09/2023\nBooking Confirmation: Confirmed";
echo '<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($userData) . '" alt="QR Code">';
    ?>
    </div>
    <?php
    } else {
        // No data found for this email, display the message
    ?>
    <div class="alert alert-danger">
        <strong><p>NO TICKETS BOOKED FOR THIS EMAIL</p></strong>
    </div>
    <?php
    }
    ?>
</div>
</body>
</html>
