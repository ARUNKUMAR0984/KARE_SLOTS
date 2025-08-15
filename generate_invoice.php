<?php
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
    }}


if (isset($_GET['date']) && isset($_GET['timeslot']) && isset($_GET['venue'])) {
    // Retrieve booking details from query parameters
    $date = $_GET['date'];
    $timeslot = $_GET['timeslot'];
    $venue = $_GET['venue'];

    // Fetch additional details for the selected booking from the database
    $mysqli = new mysqli($host, $username, $password, $venue);

    if ($mysqli->connect_error) {
        die("Connection failed for $venue: " . $mysqli->connect_error);
    }

    $query = "SELECT user_name, email, register_number, purpose, requirements FROM users WHERE email = ? AND date = ? AND timeslot = ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $user_email, $date, $timeslot);
        $stmt->execute();
        $stmt->bind_result($user_name, $email, $register_number, $purpose, $requirements);

        if ($stmt->fetch()) {
            // Create a new PDF document
            $booking = [
                'user_name' => $user_data['user_name'],
                'email' => $user_data['email'],
                'register_number' => $registerNumber,
                'venue' => $venue,
                'date' => $date,
                'timeslot' => $timeslot,
                'purpose' => $purpose,
                'requirements' => $requirement,
            ];
        
            // Create a new PDF document
            $pdf = new FPDF();
            $pdf->AddPage();
        
            $pdf->Image('kalasalingam.png', 10, 10, 175);
        
            // Set font
            $pdf->SetFont('Arial', 'B', 16);
        
            // Title
            $pdf->Cell(0, 80, 'Booking Confirmation Invoice', 0, 1, 'C');
            $pdf->Cell(0, 10, 'Applicant Details', 0, 1);
            // User information
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(30, 10, 'User Name: ', 1);
            $pdf->Cell(100, 10, $booking['user_name'], 1);
            $pdf->Ln();
            $pdf->Cell(30, 10, 'Email: ', 1);
            $pdf->Cell(100, 10, $booking['email'], 1);
            $pdf->Ln();
            $pdf->Cell(30, 10, 'Register No: ', 1);
            $pdf->Cell(100, 10, $booking['register_number'], 1);
            $pdf->Ln();
        
            // Booking details table
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Booking Details', 0, 1);
        
            // Create a table for booking details
            $pdf->Cell(30, 10, 'Venue', 1);
            $pdf->Cell(100, 10, $booking['venue'], 1);
            $pdf->Ln();
            $pdf->Cell(30, 10, 'Date', 1);
            $pdf->Cell(100, 10, $booking['date'], 1);
            $pdf->Ln();
            $pdf->Cell(30, 10, 'Time Slot', 1);
            $pdf->Cell(100, 10, $booking['timeslot'], 1);
            $pdf->Ln();
            $pdf->Cell(30, 10, 'Purpose', 1);
            $pdf->Cell(100, 10, $booking['purpose'], 1);
            $pdf->Ln();
            $pdf->Cell(30, 10, 'Requirements', 1);
            $pdf->Cell(100, 10, $booking['requirements'], 1);
            $pdf->Ln();
        
            // Add the digital signature
            $pdf->Cell(290, 65, 'Digital Signature ', 0, 1, 'C');
            $pdf->Image('green tick.png', 150, 200, 15);
        
            // Output the PDF to the browser
            $pdf->Output('invoice.pdf', 'I');
            exit; // Terminate the script after generating and outputting the PDF
        }   }

        // Close the statement
        $stmt->close();

        // Close the database connection
        $mysqli->close();
    }
else {
    // Handle missing parameters or invalid data
    echo "Invalid request. Missing or invalid parameters.";
}
?>