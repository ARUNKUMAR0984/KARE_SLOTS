<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'seminarhall';

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected successfully!";


if (isset($_GET['action'])) {
    if ($_GET['action'] === 'review') {
        // Authorities view the list of pending bookings
        $pendingBookings = $mysqli->query("SELECT * FROM users WHERE status = 'pending'");
        
        // Display a list of pending bookings with options to approve or reject
        if ($pendingBookings->num_rows > 0) {
            echo "<h2>Pending Booking Requests</h2>";
            echo "<table>";
            echo "<tr><th>Name</th><th>Timeslot</th><th>Email</th><th>Date</th><th>Action</th></tr>";
            while ($row = $pendingBookings->fetch_assoc()) {
                $bookingId = $row['id'];
                $name = $row['name'];
                $timeslot = $row['timeslot'];
                $email = $row['email'];
                $date = $row['date'];
    
                // Display booking details
                echo "<tr>";
                echo "<td>$name</td>";
                echo "<td>$timeslot</td>";
                echo "<td>$email</td>";
                echo "<td>$date</td>";
                echo "<td>
                        <a href='approval.php?action=approve&id=$bookingId'>Approve</a>
                        <a href='approval.php?action=reject&id=$bookingId'>Reject</a>
                    </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No pending bookings.";
        }
    } elseif ($_GET['action'] === 'approve' || $_GET['action'] === 'reject') {
        // Authorities approve or reject a booking
        $bookingId = $_GET['id'];
        $status = ($_GET['action'] === 'approve') ? 'approved' : 'rejected';

        // Update the status of the booking in the database
        $updateStmt = $mysqli->prepare("UPDATE users SET status = ? WHERE id = ?");
        $updateStmt->bind_param('si', $status, $bookingId);

        if ($updateStmt->execute()) {
            // Send an email to the user with the approval/rejection status
            $userEmail = $mysqli->query("SELECT email, timeslot, date FROM users WHERE id = $bookingId")->fetch_assoc();
            $timeslot = $userEmail['timeslot'];
            $date = $userEmail['date'];
            
            $mail->Subject = 'Booking Status Update';
            $mail->addAddress($userEmail['email']);
            $mail->Body = "Hello, your booking for timeslot $timeslot on date $date has been $status at 8TH BLOCK SEMINAR HALL.";

            try {
                $mail->send();
            } catch (Exception $e) {
                // Handle email sending error
            }

            header("Location: approval.php?action=review");
            exit();
        } else {
            // Handle database update error
        }
    }
}
// Rest of your code
