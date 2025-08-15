<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);


if (isset($_GET['name'])) {
    $name = $_GET['name'];
} else {
    $name = "Name not provided";
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    $email = "Email not provided";
}

if (isset($_GET['timeslots'])) {
    $timeslots = $_GET['timeslots'];
} else {
    $timeslots = "Timeslot not provided";
}

if (isset($_GET['date'])) {
    $date = $_GET['date'];
} else {
    $date = "Date not provided";
}
$eventSummary = 'Auditorium';
$eventDescription = 'Details about the booking';
$eventLocation = 'auditorium';
$eventStart = date('Ymd\THis\Z', strtotime($date)); // Convert to Google Calendar date format
$eventEnd = date('Ymd\THis\Z', strtotime($date));   // Convert to Google Calendar date format

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <LINK rel="icon" href="kare_logo-removebg-preview.png" type="image/X-icon">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Booking Confirmation</h3>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-success">
                            <strong>Booking Successful! Email Sent Successfully</strong>
                        </div>
                        
                        <h4>Your Booking Details:</h4>
                    
                        <ul>
                            <li><strong>Name:</strong> <?php echo $name; ?></li>
                            <li><strong>Email:</strong> <?php echo $email; ?></li>
                            <li><strong>Timeslot:</strong> <?php echo $timeslots; ?></li>
                            <li><strong>Date:</strong> <?php echo date('d-m-Y', strtotime($date)); ?></li>
                            <li><strong>Venue:</strong> AUDITORIUM</li>
                        </ul>
                        <center><a class="btn btn-primary" href="index.php" id="button">Home</a><br><br></center>
                        <center><a class="btn btn-primary" href="auditoriumcalender.php" id="button">Back to calendar</a><br><br></center>
                        <center><a class="btn btn-primary" href="<?php echo generateGoogleCalendarLink(); ?>" target="_blank">Add to Google Calendar</a><br><br></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Function to generate Google Calendar link
function generateGoogleCalendarLink() {
    global $eventSummary, $eventDescription, $eventLocation, $eventStart, $eventEnd;

    $googleCalendarUrl = 'https://www.google.com/calendar/render?action=TEMPLATE';

    // Append event details as query parameters
    $googleCalendarUrl .= '&text=' . urlencode($eventSummary);
    $googleCalendarUrl .= '&details=' . urlencode($eventDescription);
    $googleCalendarUrl .= '&location=' . urlencode($eventLocation);
    $googleCalendarUrl .= '&dates=' . urlencode($eventStart . '/' . $eventEnd);

    return $googleCalendarUrl;
}
?>
