<?php
session_start();
include('admin connect.php');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    // Add your database connection here

    $booking_id = $_GET['id'];
    $query = "DELETE FROM bookings WHERE booking_id = $booking_id";
    mysqli_query($conn, $query);

    header("Location: admin_panel.php");
    exit();
}
?>
