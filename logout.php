<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include("connection.php");
include("functions.php");

if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
    echo "User logged out successfully."; // Add this line for debugging
} else {
    echo "User not logged in."; // Add this line for debugging
}

header("Location: login2.php");
exit;
?>
