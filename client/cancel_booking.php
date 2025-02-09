<?php
session_start();
include("../connection.php");

if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // I-update ang status ng booking sa database
    $updateQuery = "UPDATE booking SET stat = 'cancelled' WHERE booking_id = '$booking_id'";
    
    if ($database->query($updateQuery)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
