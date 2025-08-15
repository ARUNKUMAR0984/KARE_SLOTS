<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\PHPMailer.php";
require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\SMTP.php";
require "C:\\xampp\\htdocs\\KARE_WEBSITE2\\PHPMailer-master\\src\\Exception.php";

$mysqli = new mysqli('localhost','root','','auditorium');
if(isset($_GET['date']))
{
    $date = $_GET['date'];

    $stmt = $mysqli->prepare("select * from users where date = ?");
    $stmt->bind_param('s', $date);
    $users = array();
    if($stmt->execute())
    {
        $result = $stmt->get_result();
        if($result->num_rows>0)
        {
            while($row = $result->fetch_assoc())
            {
                $users[] = $row['timeslot'];
            }
            $stmt->close();
        }
    }
}
$user_phone = '';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $timeslots = $_POST["timeslot"];
    $phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber'] : '';
    $registerNumber = isset($_POST['registernumber']) ? $_POST['registernumber'] : '';
    $purpose = $_POST['purpose'];
    $requirement = $_POST['requirement'];

    $stmt = $mysqli->prepare("INSERT INTO users(name, timeslot, email, date, phonenumber,register_number,purpose,requirements,status) VALUES(?,?,?, ?, ?, ?, ?, ?, 'PENDING')");
    $stmt->bind_param('ssssssss', $name, $timeslots, $email, $date, $phonenumber, $registerNumber,$purpose,$requirement);
    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>Booking Successful</div";
    
        // Send confirmation email using PHPMailer for the user
        $userMail = new PHPMailer(true);
    
        $userMail->isSMTP();
        $userMail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
        $userMail->SMTPAuth = true;
        $userMail->Username = 'arunkumar97462@gmail.com'; // Replace with your SMTP username
        $userMail->Password = 'endk xtrk xvci cuuv'; // Replace with your SMTP password
        $userMail->SMTPSecure = 'ssl'; // Use 'tls' or 'ssl' based on your SMTP server
        $userMail->Port = 465; // The SMTP port
    
        $userMail->setFrom('arunkumar97462@gmail.com', 'KareSlots'); // Replace with your email and name
        $userMail->addAddress($email, $name); // Add the user's email and name
    
        $userMail->isHTML(true);
        $userMail->Subject = 'Booking Confirmation';
        $dates = date('d-m-Y', strtotime($date));
        $userMail->Body = "Hello <strong>$name</strong>,<br>Your booking for timeslot $timeslots on date $dates at KRISHNAN AUDITORIUM to conduct <strong>$purpose</strong> with <strong>$requirements</strong> has been submitted for approval.<br>Please stay tuned.<br> Thank you for choosing our service!";
    
        // Send confirmation email using PHPMailer for the administrator
        $adminMail = new PHPMailer(true);
    
        $adminMail->isSMTP();
        $adminMail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
        $adminMail->SMTPAuth = true;
        $adminMail->Username = 'arunkumar97462@gmail.com'; // Replace with your SMTP username
        $adminMail->Password = 'endk xtrk xvci cuuv'; // Replace with your SMTP password
        $adminMail->SMTPSecure = 'ssl'; // Use 'tls' or 'ssl' based on your SMTP server
        $adminMail->Port = 465; // The SMTP port
    
        $adminMail->setFrom('arunkumar97462@gmail.com', 'KareSlots'); // Replace with your email and name
        $adminMail->addAddress('arunkumars97462@gmail.com', 'KareSlots'); // Add the administrator's email and name
    
        $adminMail->isHTML(true);
        $adminMail->Subject = 'Booking Confirmation Approval';
        $dates = date('d-m-Y', strtotime($date));
        $adminMail->Body = "Hello Sir,<br>There is a booking application for timeslot $timeslots on date $dates at KRISHNAN AUDITORIUM to conduct <strong>$purpose</strong> with <strong>$requirement</strong> that has been submitted for approval.<br><br> Click below to approve the booking:<br>";
        $adminMail->Body .= "<p><a href=\"http://localhost/KARE_WEBSITE2/admin.php?secret=" . base64_encode($email) . "\"><button class=\"btn btn-primary\">ADMIN PAGE</button></a></p>";
        $adminMail->Body .= "Thank you for choosing our service!";
    
        try {
            $userMail->send(); // Send the user email
            $adminMail->send(); // Send the administrator email
            $msg .= "<div class='alert alert-success'>Confirmation emails sent to $email and administrator</div>";
    
            // WhatsApp message sending code to the user
            require_once('ultramsg.class.php');
            $ultramsg_token = "bspd1apg7j8zz8ph"; // Ultramsg.com token
            $instance_id = "instance62986"; // Ultramsg.com instance id
            $client = new UltraMsg\WhatsAppApi($ultramsg_token, $instance_id);
            $body = "Hello $name, your booking for timeslot $timeslots on date $dates at KRISHNAN AUDITORIUM has been submitted for approval . Thank you for choosing our service!";
            $api = $client->sendChatMessage($phonenumber, $body);
    
            // WhatsApp message sending code to the administrator
            $admin_body = "New booking by $name for timeslot $timeslots on date $dates at KRISHNAN AUDITORIUM. Please review and approve.";
            $admin_api = $client->sendChatMessage('admin_phone_number', $admin_body);
             
            // SMS Gateway Code
$fields = array(
    "message" => "New booking by $name for timeslot $timeslots on date $dates at KRISHNAN AUDITORIUM. Please review and approve.",
    "language" => "english",
    "route" => "q",
    "numbers" => $phonenumber, // Use the recipient's phone number

    // Set the sender name (KARESLOTS)
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
            // End of WhatsApp message sending code
            header("Location: summaryaudi.php?name=$name&email=$email&timeslots=$timeslots&date=$date");
            exit();
        } catch (Exception $e) {
            $msg .= "<div class='alert alert-warning'>Failed to send confirmation email: " . $mail->ErrorInfo . "</div>";
        }

        $users[] = $timeslots; // Add the booked timeslot to the array
        $stmt->close();
    } else {
        $msg = "<div class='alert alert-danger'>Booking Failed</div>";
    }
}


$duration = 60;
$cleanup = 0;
$start = "09:00";
$end = "18:00";

function timeslots($duration, $cleanup, $start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT" . $duration . "M");
    $cleanupInterval = new DateInterval("PT" . $cleanup . "M");

    $slots = array();

    while ($start < $end) {
        $endPeriod = clone $start;
        $endPeriod->add($interval);

        if ($endPeriod > $end) {
            break;
        }

        // Format time in 12-hour format (h:i A)
        $slots[] = $start->format("h:i A") . " - " . $endPeriod->format("h:i A");
        $start->add($interval)->add($cleanupInterval);
    }

    return $slots;
}
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);

$email = $user_data['email'];
$query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user_details = mysqli_fetch_assoc($result);
    $user_name = $user_details['user_name'];
    $user_email = $user_details['email'];
    $phonenumber = $user_details['phonenumber'];
    $registerNumber=$user_details['register_number'];
} else {
    // Handle the case where user data is not found (e.g., display an error message)
    $user_name = '';
    $user_email = '';
    $phonenumber='';
    $registerNumber='';
    
}
?>
<!-- Rest of your HTML code -->




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book slot</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <LINK  rel="icon" href="kare_logo-removebg-preview.png" type="image/X - icon">
    <style>
        .forms-group button {
    margin-bottom: 10px; /* Adjust the margin as needed */
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
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> BOOK YOUR SLOTS AUDITORIUM</strong></marquee>
    </div>
    <br><br>
<center><h2>AUDITORIUM</h2></center>
    <div class="container">
    <h1 class="text-center">Book for Date: <?php echo date('d-m-Y', strtotime($date)); ?></h1>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <?php echo isset($msg)?$msg:"";?>
            </div>
            <?php
            $timeslots = timeslots($duration, $cleanup, $start, $end);
            $columnsPerRow = 4;
            $columnWidth = 12 / $columnsPerRow; // Calculate column width based on the number of columns

            foreach ($timeslots as $ts) {
                ?>
                <div class="col-md-<?php echo $columnWidth; ?>">
                    <div class="forms-group">
                        <?php if (in_array($ts, $users)) { ?>
                            <button class="btn btn-danger"><?php echo $ts; ?></button>
                        <?php } else { ?>
                            <button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">BOOKING: <span id="slot"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post"> 
                                <div class="form-group">
                                    <label for="">TIMESLOT</label>
                                    <input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">NAME</label>
                                    <input required type="text" readonly name="name" id="name" class="form-control" value="<?php echo $user_name; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input required type="email" readonly name="email" id="email" class="form-control" value="<?php echo $user_email; ?>" pattern="[0-9]+@klu.ac.in" title="Enter a valid email address with the domain 'klu.ac.in'">
                                </div>
                                
                                <div class="form-group">
                                <label for="">VENUE</label>
                                <input required type="text" readonly name="venue" id="venue" class="form-control" value="AUDITORIUM">
                            </div>
                            <div class="form-group">
    <label for="">PHONE NUMBER</label>
    <input type="phonenumber" id="phonenumber" placeholder="Enter your phone number" class="form-control" readonly name="phonenumber" value="<?php echo $phonenumber; ?>" required>
</div>
<div class="form-group">
    <label for="">Register Number</label>
    <input type="phonenumber" id="registernumber" placeholder="Enter your register number" class="form-control" readonly name="registernumber" value="<?php echo $registerNumber; ?>" required>
</div>
<div class="form-group">
    <label for="">Purpose of booking:</label>
    <textarea type="text" id="purpose" placeholder="Enter the purpose of the event" class="form-control" name="purpose"  rows="4" cols="50" required></textarea>
</div>
<div class="form-group">
    <label for="">Requirements</label>
    <textarea type="text" id="requirement" placeholder="Enter your event Requirements like sound systems,sanitization,wifi etc.." class="form-control" name="requirement"  rows="4" cols="50" required></textarea><br><br>
</div>
                            <div class="form-group pull-right">
                                    <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>

        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

    <script>
        $(".book").click(function (){
            var timeslot = $(this).attr('data-timeslot');
            $("#slot").html(timeslot);
            $("#timeslot").val(timeslot);
            $("#myModal").modal("show");
        });
    </script>
            <center></div>
                    
                    <a class="btn btn-primary" href="index.php" id="button">Home</a><br><br>
                    <a class="btn btn-primary" href="auditoriumcalender.php" id="button">Back to calendar</a><br><br>
            </div></center>
            <script>
    $(document).ready(function () {
        // Function to check and disable slots for the current date and time
        function checkSlotsForCurrentTime() {
            var now = new Date();
            var currentTime = now.getHours() * 60 + now.getMinutes(); // Convert current time to minutes
            var currentDate = now.toISOString().slice(0, 10); // Get the current date in YYYY-MM-DD format

            $(".book").each(function () {
                var timeslot = $(this).attr('data-timeslot');
                var slotParts = timeslot.split('-');
                var startSlot = slotParts[0].trim();

                // Convert slot start time to minutes
                var startTimeParts = startSlot.split(':');
                var startMinutes = parseInt(startTimeParts[0]) * 60 + parseInt(startTimeParts[1]);

                // Check if the slot belongs to the current date and has already started
                if (currentDate === '<?php echo $date; ?>' && currentTime >= startMinutes) {
                    // Disable the slot
                    $(this).addClass('expired');
                    $(this).prop('disabled', true);
                }
            });
        }

        // Call the function on page load
        checkSlotsForCurrentTime();
    });
</script>
        
</body>


</html>