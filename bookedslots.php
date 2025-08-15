<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\PHPMailer.php";
require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\SMTP.php";
require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\Exception.php";
require("C:\\xampp\\htdocs\\KARE_WEBSITE2\\fpdf186\\fpdf.php"); // Include the FPDF library
session_start();
include("connection.php");
include("functions.php");
$host = 'localhost';
$username = 'root';
$password = '';
// Check if the user is logged in and get their email
$user_data = check_login($con);
$user_email = $user_data['email']; // Use the logged-in user's email
$msg='';

// List of venues (database names)
$venues = ["seminarhall", "9thseminarhall", "auditorium", "tcseminarhall", "bookingcalender", "libraryseminarhall","guesthouse"];

$venueMappings = [
    "seminarhall" => "8TH BLOCK SEMINAR HALL",
    "9thseminarhall" => "9TH BLOCK SEMINAR HALL",
    "auditorium" => "AUDITORIUM",
    "tcseminarhall" => "TIFAC CORE SEMINAR HALL",
    "bookingcalender" => "INDOOR STADIUM",
    "libraryseminarhall" => "LIBRARY SEMINAR HALL",
    "guesthouse" => "GUEST HOUSE",
];

// Initialize an array to store booking data for all venues
$allBookingData = [];
$venueNames = [];
foreach ($venues as $venue) {
    // Create a database connection for the current venue
    $mysqli = new mysqli($host, $username, $password, $venue);

    if ($mysqli->connect_error) {
        die("Connection failed for $venue: " . $mysqli->connect_error);
    }

    // Query to retrieve email, date, timeslot, and venue for the specified email
    $query = "SELECT email, date, timeslot, venue, phonenumber,purpose,requirements,register_number,status FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->bind_result($email, $date, $timeslot, $venue, $phonenumber,$purpose,$requirement,$registerNumber,$status);


        $bookingData = array(); // Initialize the array to store booking data for the current venue

        while ($stmt->fetch()) {
            $bookingData[] = [
                'venue' => $venue,
                'date' => $date,
                'timeslot' => $timeslot,
                'phonenumber' => $phonenumber,
                'status' => $status, // Add 'status' to the array
                'purpose'=>$purpose,
                'requirement'=>$requirement,
                'registerNumber'=>$registerNumber
            ];
        }
        
        // Append the booking data for the current venue to the array for all venues
        $allBookingData = array_merge($allBookingData, $bookingData);

        // Close the statement for the current venue
        $stmt->close();

        // Close the database connection for the current venue
        $mysqli->close();
    } else {
        echo "Error in preparing the SQL statement for $venue: " . mysqli_error($mysqli);
    }
}

// Handle the cancellation of a booking
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['date']) && isset($_GET['timeslot'])) {
    $date = $_GET['date'];
    $timeslot = $_GET['timeslot'];

    $cancellationSuccessful = false;
    $correctVenue = '';
    $correctPhoneNumber = '';
  

    foreach ($venues as $venue) {
        // Create a database connection for the current venue
        $mysqli = new mysqli($host, $username, $password, $venue);

        if ($mysqli->connect_error) {
            die("Connection failed for $venue: " . $mysqli->connect_error);
        }

        // Delete the booking for the logged-in user with the specified timeslot and date in the current venue
        $query = "DELETE FROM users WHERE email = ? AND date = ? AND timeslot = ?";
        $stmt = $mysqli->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sss", $user_email, $date, $timeslot);
            if ($stmt->execute()) {
                // Booking successfully canceled for the current venue
                $cancellationSuccessful = true;
                
                // Retrieve the venue and phone number for the canceled booking
                $query = "SELECT venue, phonenumber FROM users WHERE email = ? AND date = ? AND timeslot = ?";
                $stmt2 = $mysqli->prepare($query);
                
                if ($stmt2) {
                    $stmt2->bind_param("sss", $user_email, $date, $timeslot);
                    $stmt2->execute();
                    $stmt2->bind_result($venueName, $phonenumber);
                
                    while ($stmt2->fetch()) {
                        $correctVenue = $venueName; // This correctly assigns the venue name from the current booking
                    }
                
                    // Close the second statement
                    $stmt2->close();
                }
                
                
            } else {
                // Error occurred while canceling the booking for the current venue
                echo '<script>alert("An error occurred while canceling the booking for ' . $venue . '.");</script>';
            }
            // Close the first statement for the current venue
            $stmt->close();
        } else {
            // Error in preparing the delete statement for the current venue
            echo '<script>alert("An error occurred while canceling the booking for ' . $venue . '.");</script>';
        }

        // Close the database connection for the current venue
        $mysqli->close();
    }

    if ($cancellationSuccessful) {
        // Booking cancellation was successful, send confirmation email and WhatsApp message
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
        $mail->SMTPAuth = true;
        $mail->Username = 'arunkumar97462@gmail.com'; // Replace with your SMTP username
        $mail->Password = 'endk xtrk xvci cuuv'; // Replace with your SMTP password
        $mail->SMTPSecure = 'ssl'; // Use 'tls' or 'ssl' based on your SMTP server
        $mail->Port = 465; // The SMTP port

        $mail->setFrom('arunkumar97462@gmail.com', 'KareSlots'); // Replace with your email and name
        $mail->addAddress($user_email, $user_data['user_name']); // Email and name of the recipient

        $mail->isHTML(true);
        $mail->Subject = 'Booking Cancellation Confirmation';
        $date = date('d-m-Y', strtotime($date));
        $venueMessage = isset($venueNames[$correctVenue]) ? $venueNames[$correctVenue] : $correctVenue;
    
        $mail->Body = "Hello " . $name. ",<br>Your booking for timeslot $timeslot on date $date has been canceled at $venueMessage.<br>Thank you for using our service!";
        $mail->send();
        $msg .= "<div class='alert alert-success'>Confirmation email sent for booking cancellation</div>";
        try {
           
            // WhatsApp message sending code
            
            require_once('ultramsg.class.php'); // if you download ultramsg.class.php
            $ultramsg_token = "bspd1apg7j8zz8ph"; // Ultramsg.com token
            $instance_id = "instance62986"; // Ultramsg.com instance id
            $client = new UltraMsg\WhatsAppApi($ultramsg_token, $instance_id);

            $body = "Hello " . $user_data['user_name'] . ", your booking for timeslot $timeslot on date $date has been canceled at $venueMessage . Thank you for choosing our service!";
$api = $client->sendChatMessage($phonenumber, $body);
            echo '<script>alert("Booking successfully canceled for ' .$venueMessage  . '.");</script>';
            echo '<script>window.location.href = "bookedslots.php";</script>';
            // Log success or handle any errors if necessary
        } 
        catch (Exception $e) {
            // Handle the email sending error if needed
            echo '<script>window.location.href = "bookedslots.php";</script>';
        }

    } else {
        // Error in preparing the delete statement for the current venue
        echo '<script>alert("An error occurred while canceling the booking for ' . $venue . '.");</script>';
        echo '<script>window.location.href = "bookedslots.php";</script>';
    }
}
// Sort the booking data array by date
usort($allBookingData, function ($a, $b) {
    return strtotime($a['date']) - strtotime($b['date']);
});
$currentTimestamp = time();




    






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
<div class="marquee-container">
    <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> YOUR BOOKED SLOTS</strong></marquee>
</div>

<div class="container">
<div class="button-container">
    <a class="btn btn-primary" href="index.php" id="button">Home</a><br><br>
    <a class="btn btn-primary" href="ticket available.php" id="button">EVENTS BOOKED</a><br><br>
    </div><br><br>
    <h1>Booked Slots</h1>

    <?php
    // Separate events by date
    $eventsByDate = [];

    foreach ($allBookingData as $booking) {
        $eventDate = $booking['date'];

        // Group events by date
        if (!isset($eventsByDate[$eventDate])) {
            $eventsByDate[$eventDate] = [];
        }

        $eventsByDate[$eventDate][] = $booking;
    }

    // Display events by date
    foreach ($eventsByDate as $date => $events):
        ?>
        <h2><?= date('d-m-Y', strtotime($date)) ?></h2>
        <table class="booked-slots">
            <thead>
            <tr>
                <th>Venue</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Status</th>
                <th>Invoice</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($events as $booking): ?>
    <tr>
        <td><?= isset($venueMappings[$booking['venue']]) ? $venueMappings[$booking['venue']] : $booking['venue'] ?></td>
        <td><?= date('d-m-y', strtotime($booking['date'])) ?></td> <!-- Change the date format here -->
        <td><?= $booking['timeslot'] ?></td>
        <td><?= isset($booking['status']) ? $booking['status'] : 'N/A' ?></td> <!-- Check if 'status' exists -->
        <td><form method="post" action="">
        <button class="btn btn-primary" name="invoice" id="invoice">Generate Invoice</button>
    </form></td>
        <td><a href="?date=<?= $booking['date'] ?>&timeslot=<?= $booking['timeslot'] ?>"
               type="button" class="btn btn-danger">Cancel</a></td>
    </tr>
<?php endforeach; ?>

            </tbody>
        </table>

    <?php endforeach; ?>
</div>

<script>
    function confirmCancellation(date, timeslot, venue) {
        var confirmation = confirm("Are you sure you want to cancel the booking for " + venue + " on " + date + " at " + timeslot + "?");
        
        if (confirmation) {
            // Redirect to the cancellation URL
            window.location.href = "bookedslots.php?date=" + date + "&timeslot=" + timeslot;
        }
    }
</script>



</body>
</html>
