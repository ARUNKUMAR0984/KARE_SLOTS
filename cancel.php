<?php
session_start();
include("connection.php");
include("functions.php");

// Check if the user is logged in and get their email
$user_data = check_login($con);
$user_email = $user_data['email'];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['timeslot']) && isset($_GET['date'])) {
    $timeslot = $_GET['timeslot'];
    $date = $_GET['date'];

    // Delete the booking for the logged-in user with the specified timeslot and date
    $query = "DELETE FROM users WHERE user_email = ? AND date = ? AND timeslot = ?";
    $stmt = $con->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $user_email, $date, $timeslot);
        if ($stmt->execute()) {
            // Booking successfully canceled
            echo '<script>alert("Booking successfully canceled.");</script>';
            echo '<script>window.location.href = "booked_slots.php";</script>';
        } else {
            // Error occurred while canceling the booking
            echo '<script>alert("An error occurred while canceling the booking.");</script>';
            echo '<script>window.location.href = "booked_slots.php";</script>';
        }
        $stmt->close();
    } else {
        // Error in preparing the delete statement
        echo '<script>alert("An error occurred while canceling the booking.");</script>';
        echo '<script>window.location.href = "booked_slots.php";</script>';
    }

} else {
    // Invalid parameters
    echo '<script>alert("Invalid parameters.");</script>';
    echo '<script>window.location.href = "booked_slots.php";</script>';
}
?>
