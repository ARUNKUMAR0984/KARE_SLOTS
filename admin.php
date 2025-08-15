<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\PHPMailer.php";
require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\SMTP.php";
require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\Exception.php";
session_start();
include("admin connect.php");
include("functions2.php");
if (isset($_SESSION['id'])) {
    $user_data = check_login($con);
} else {
    // If the user is not logged in, redirect to the login page
    header("Location: admin page login.php");
    exit(); // Make sure to stop script execution after the redirect
}

// Check if the logout button is clicked
if (isset($_POST['logout'])) {
    // Unset the session variable
    unset($_SESSION['id']);
    // Redirect to the login page after logout
    header("Location: admin page login.php");
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';

// Check if the user is logged in and get their email
$user_data = check_login($con);
$user_email = $user_data['email']; // Use the logged-in user's email
$msg = '';

// List of venues (database names)
$venues = ["seminarhall", "9thseminarhall", "auditorium", "tcseminarhall", "bookingcalender", "libraryseminarhall", "guesthouse"];

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

    // Query to retrieve all booking data for all users in the current venue
    $query = "SELECT email, register_number, date, timeslot, venue, phonenumber, name, purpose, requirements, status FROM users";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($email, $register_number, $date, $timeslot, $venue, $phonenumber, $user_name, $purpose, $requirement, $status);

        $bookingData = array(); // Initialize the array to store booking data for the current venue

        while ($stmt->fetch()) {
            $bookingData[] = [
                'user_name' => $user_name,
                'email' => $email,
                'register_number' => $register_number,
                'venue' => isset($venueMappings[$venue]) ? $venueMappings[$venue] : $venue,
                'date' => date('d-m-Y', strtotime($date)),
                'timeslot' => $timeslot,
                'phonenumber' => $phonenumber,
                'purpose' => $purpose,
                'requirements' => $requirement,
                'status' => $status,
            ];
        }

        // Append the booking data for the current venue to the array for all venues
        $allBookingData[$venue] = $bookingData;

        // Close the statement for the current venue
        $stmt->close();

        // Close the database connection for the current venue
        $mysqli->close();
    } else {
        echo "Error in preparing the SQL statement for $venue: " . mysqli_error($mysqli);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve_booking'])) {
        $venueReferenceName = $_POST['venue'];
        $venueDatabaseName = array_search($venueReferenceName, $venueMappings);

        if ($venueDatabaseName !== false) {
            $mysqli = new mysqli($host, $username, $password, $venueDatabaseName);

            if ($mysqli->connect_error) {
                die("Connection failed for $venueDatabaseName: " . $mysqli->connect_error);
            }

            $query = "UPDATE users SET status = 'APPROVED' WHERE timeslot = ?";
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                // Get the timeslot and email from the form
                $timeslot = $_POST['timeslot'];
                $email = $_POST['email'];
                $date = $_POST['date']; // Set the date from the form
                $purpose = $_POST['purpose']; // Set the purpose from the form
                $requirement = $_POST['requirements'];

                // Bind the timeslot parameter and execute the statement
                $stmt->bind_param("s", $timeslot);

            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>Booking Confirmation</div>";

                // Send a notification email to authorities for approval
                // Modify the email subject and body as needed
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
                $mail->SMTPAuth = true;
                $mail->Username = 'arunkumar97462@gmail.com'; // Replace with your SMTP username
                $mail->Password = 'endk xtrk xvci cuuv'; // Replace with your SMTP password
                $mail->SMTPSecure = 'ssl'; // Use 'tls' or 'ssl' based on your SMTP server
                $mail->Port = 465; // The SMTP port

                $mail->setFrom('arunkumar97462@gmail.com', 'KareSlots');
                $mail->addAddress($email, 'Confirmation Authorities'); // Replace with authorities' email
                $mail->isHTML(true);
                $mail->Subject = 'Booking Approval';
                $dates = date('d-m-Y', strtotime($date));
                $mail->Body = "Hello <strong>$user_name</strong>,<br>Your booking for timeslot $timeslot on date $dates to conduct <strong>" . $purpose . "</strong> with <strong>" . $requirement . "</strong> as the requirement has been <strong>APPROVED</strong> at $venueReferenceName.<br>Thank you for choosing our service!";

                try {
                    $mail->send();
                    $msg .= "<div class='alert alert-success'>Confirmation email sent to $email</div>";
                    

$fields = array(
    "message" => "Hello $user_name,Your booking for timeslot $timeslot on date $dates to conduct $purpose with $requirement as the requirement has been 'APPROVED' at $venueReferenceName.",
    "language" => "english",
    "route" => "q",
    "numbers" => $phonenumber, // Use
    "sender_id" => "KARESLOTS"
);
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($fields),
    CURLOPT_HTTPHEADER => array(
        "authorization: qI7QQxqDnIrBudGFKwwK64MH4cjDy4YGCLa91FM53ViSGOEhzwsYfMKz7aAG" ,// Replace with your Fast2SMS API key

        "accept: */*",
        "cache-control: no-cache",
        "content-type: application/json"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}
                    
                    header("Location: admin.php");
            exit();
                } catch (Exception $e) {
                    // Handle email sending errors
                }
            } else {
                // Update failed
                echo "Failed to update 'status': " . $stmt->error;
            }

            // Close the database connection
            $mysqli->close();
        } else {
            echo "Invalid venue selected: $venueReferenceName";
        }
    }
}
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reject_booking'])) {
        $venueReferenceName = $_POST['venue'];
        $venueDatabaseName = array_search($venueReferenceName, $venueMappings);

        if ($venueDatabaseName !== false) {
            $mysqli = new mysqli($host, $username, $password, $venueDatabaseName);

            if ($mysqli->connect_error) {
                die("Connection failed for $venueDatabaseName: " . $mysqli->connect_error);
            }

            $query = "UPDATE users SET status = 'REJECTED' WHERE timeslot = ?";
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                // Get the timeslot and email from the form
                $timeslot = $_POST['timeslot'];
                $email = $_POST['email'];
                $date = $_POST['date']; // Set the date from the form
                $purpose = $_POST['purpose']; // Set the purpose from the form
                $requirement = $_POST['requirements'];

                // Bind the timeslot parameter and execute the statement
                $stmt->bind_param("s", $timeslot);

            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>Booking Confirmation</div>";

                // Send a notification email to authorities for approval
                // Modify the email subject and body as needed
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
                $mail->SMTPAuth = true;
                $mail->Username = 'arunkumar97462@gmail.com'; // Replace with your SMTP username
                $mail->Password = 'endk xtrk xvci cuuv'; // Replace with your SMTP password
                $mail->SMTPSecure = 'ssl'; // Use 'tls' or 'ssl' based on your SMTP server
                $mail->Port = 465; // The SMTP port

                $mail->setFrom('arunkumar97462@gmail.com', 'KareSlots');
                $mail->addAddress($email, 'Confirmation Authorities'); // Replace with authorities' email
                $mail->isHTML(true);
                $mail->Subject = 'Booking Rejection';
                $dates = date('d-m-Y', strtotime($date));
                $mail->Body = "Hello <strong>$user_name</strong>,<br>Your booking for timeslot $timeslot on date $dates to conduct <strong>{$purpose}</strong> with <strong>{$requirement}</strong> as the requirement has been <strong>REJECTED</strong> at $venueReferenceName.<br>Thank you for choosing our service!";

                try {
                    $mail->send();
                    $msg .= "<div class='alert alert-success'>Confirmation email sent to $email</div>";
                    $message = "Hello $user_name, Your booking for timeslot $timeslot on date $dates to conduct $purpose with $requirement as the requirement has been APPROVED at $venueReferenceName.";

        $smsResponse = sendSMS($phonenumber, $message);

        if ($smsResponse) {
            // SMS sent successfully
            echo "SMS sent successfully!";
        } else {
            // Failed to send SMS
            echo "Failed to send SMS!";
        }
        
        // Redirect back to admin.php
        header("Location: admin.php");
        exit();
                } catch (Exception $e) {
                    // Handle email sending errors
                }
            } else {
                // Update failed
                echo "Failed to update 'status': " . $stmt->error;
            }

            // Close the database connection
            $mysqli->close();
        } else {
            echo "Invalid venue selected: $venueReferenceName";
        }
    }
}
}
function sendSMS($recipient, $message) {
    $apiKey =  "qI7QQxqDnIrBudGFKwwK64MH4cjDy4YGCLa91FM53ViSGOEhzwsYfMKz7aAG"; // Replace with your Fast2SMS API key

    $fields = array(
        "message" => $message,
        "language" => "english",
        "route" => "q",
        "numbers" => $recipient, // Use the recipient's phone number
        "sender_id" => "KARESLOTS",
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($fields),
        CURLOPT_HTTPHEADER => array(
            "authorization: qI7QQxqDnIrBudGFKwwK64MH4cjDy4YGCLa91FM53ViSGOEhzwsYfMKz7aAG", // Set the API key in the header
            "accept: */*",
            "cache-control: no-cache",
            "content-type: application/json",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        // Error occurred while sending the SMS
        return false;
    } else {
        // SMS sent successfully
        return true;
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
    <title>Admin Panel - All Booked Slots</title>
    <style>
        /* Add CSS for the alert message */
        .alert {
            padding: 15px;
            background-color: #d4edda; /* Green background color */
            color: #155724; /* Dark green text color */
            border: 1px solid #c3e6cb; /* Green border */
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
    
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

        .cancel-button {
            background-color: #f00;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .msg-container {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: #f00;
        }
    </style>
</head>
<body>
    <h1>Admin Panel - All Booked Slots</h1>
    <form method="POST">
    <input type="submit" name="logout" value="Logout" class="btn btn-primary">
</form>
    <!-- Display a message if the booking cancellation was successful or failed -->
    <?php if (!empty($msg)): ?>
        <div class="msg-container">
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <?php
    // Display booking data for all venues
    foreach ($allBookingData as $venue => $bookings):
    ?>
    <center><h2><?= isset($venueMappings[$venue]) ? $venueMappings[$venue] : $venue ?></h2></center>
    <table class="booked-slots">
    <thead>
    <tr>
        <th>S.No</th> <!-- Add a new column for serial numbers -->
        <th>User Name</th>
        <th>Register Number</th>
        
        <th>Email</th>
        <th>Venue</th>
        <th>Date</th>
        <th>Time Slot</th>
        <th>purpose</th>
        <th>Requirement</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $serialNumber = 1; // Initialize the serial number
    foreach ($bookings as $booking):
    ?>
        <tr>
            <td><?= $serialNumber ?></td> <!-- Display the serial number -->
            <td><?= $booking['user_name'] ?></td>
            <td><?= $booking['register_number'] ?></td>
            <td><?= $booking['email'] ?></td>
            <td><?= $booking['venue'] ?></td>
            <td><?= $booking['date'] ?></td>
            <td><?= $booking['timeslot'] ?></td>
            <td><?= $booking['purpose'] ?></td>
            <td><?= $booking['requirements'] ?></td>
            
            <td>
    <?php if ($booking['status'] == 'PENDING'): ?>
        <form method="POST">
            <input type="hidden" name="date" value="<?= $booking['date'] ?>">
            <input type="hidden" name="timeslot" value="<?= $booking['timeslot'] ?>">
            <input type="hidden" name="venue" value="<?= $booking['venue'] ?>">
            <input type="hidden" name="email" value="<?= $booking['email'] ?>">
            <input type="hidden" name="purpose" value="<?= $booking['purpose'] ?>"> <!-- Add hidden input for purpose -->
            <input type="hidden" name="requirements" value="<?= $booking['requirements'] ?>"> <!-- Add hidden input for requirement -->
            <button type="submit" name="approve_booking" class="btn btn-success">Approve</button>
            <button type="submit" name="reject_booking" class="btn btn-danger">Reject</button>
        </form>
    <?php elseif ($booking['status'] == 'APPROVED'): ?>
        <span class="label label-success">Approved</span>
    <?php elseif ($booking['status'] == 'REJECTED'): ?>
        <span class="label label-danger">Rejected</span>
    <?php endif; ?>
</td>


        </tr>
    <?php
    $serialNumber++; // Increment the serial number for the next row
    endforeach;
    ?>
    </tbody>
</table>
    <?php endforeach; ?>

    
</body>
</html>
