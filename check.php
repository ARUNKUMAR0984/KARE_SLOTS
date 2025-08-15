<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "PHPMailer-master/src/PHPMailer.php";
require "PHPMailer-master/src/SMTP.php";
require "PHPMailer-master/src/Exception.php";


require_once('ultramsg.class.php'); 

$mysqli = new mysqli('sql112.infinityfree.com','if0_34976893','8ZJEaPYBbUUYAo','if0_34976893_kare_9thseminar_hall');
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

    $stmt = $mysqli->prepare("INSERT INTO users(name,timeslot,email,date,phonenumber) VALUES(?,?,?,?,?)");
    $stmt->bind_param('sssss', $name, $timeslots, $email, $date,$phonenumber);
    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>Booking Successful</div>";

        // Send confirmation email using PHPMailer
        $mail = new PHPMailer(true);

$mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
        $mail->SMTPAuth = true;
        $mail->Username = 'arunkumar97462@gmail.com'; // Replace with your SMTP username
        $mail->Password = 'endk xtrk xvci cuuv'; // Replace with your SMTP password
        $mail->SMTPSecure = 'ssl'; // Use 'tls' or 'ssl' based on your SMTP server
        $mail->Port = 465; // The SMTP port

        $mail->setFrom('arunkumar97462@gmail.com', 'KareSlots'); // Replace with your email and name
        $mail->addAddress($email, $name);  // Replace with your email and name
        $mail->addAddress($email, $name); // Email and name of the recipient

        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation';
        $dates = date('d-m-Y', strtotime($date));
        $mail->Body = "Hello <strong>$name</strong>,<br>Your booking for timeslot $timeslots on date $dates has been confirmed at 9TH BLOCK SEMINAR HALL.<br>Thank you for choosing our service!";

        try {
            $mail->send();
            $msg .= "<div class='alert alert-success'>Confirmation email sent to $email</div>";
            // WhatsApp message sending code
            require_once('ultramsg.class.php'); // if you download ultramsg.class.php
            $ultramsg_token = "scvh0ulygatley30"; // Ultramsg.com token
            $instance_id = "instance64406"; // Ultramsg.com instance id
            $client = new UltraMsg\WhatsAppApi($ultramsg_token, $instance_id);
            $body = "Hello $name, your booking for timeslot $timeslots on date $dates has been confirmed at 9TH BLOCK SEMINAR HALL. Thank you for choosing our service!";
            $api = $client->sendChatMessage($phonenumber, $body);

            $fields = array(
    "message" => "Hello $name, your booking for timeslot $timeslots on date $dates has been confirmed at 9TH COMPLEX SEMINAR HALL-(9002).",
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
            header("Location: summary9th.php?name=$name&email=$email&timeslots=$timeslots&date=$date");
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
} else {
    // Handle the case where user data is not found (e.g., display an error message)
    $user_name = '';
    $user_email = '';
    $phonenumber='';
    
}
?>




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
        <marquee class="marquee-content"><strong>HELLO, <?php echo $user_data['user_name']; ?> BOOK YOUR SLOTS AT 9TH BLOCK SEMINAR HALL</strong></marquee>
    </div>
<center><h2><STRONG>9TH BLOCK SEMINAR HALL</STRONG></h2></center>
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
                                <input required type="text" readonly name="venue" id="venue" class="form-control" value="9TH BLOCK SEMINAR HALL">
                            </div>
                          <div class="form-group">
    <label for="">PHONE NUMBER</label>
    <input type="phonenumber" id="phonenumber" placeholder="Enter your phone number" class="form-control"name="phonenumber" value="<?php echo $phonenumber; ?>" required><br><br>
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
                    <a class="btn btn-primary" href="9thseminarhall.php" id="button">Back to calendar</a><br><br>
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