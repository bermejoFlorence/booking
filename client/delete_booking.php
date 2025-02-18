<?php
session_start();
include("../connection.php");

if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // I-update ang `is_deleted` sa database (1 = deleted sa user side)
    $updateQuery = "UPDATE booking SET is_deleted = 1 WHERE booking_id = '$booking_id'";
    
    if ($database->query($updateQuery)) {
        echo "success"; // Success response sa AJAX
    } else {
        echo "error";
    }
}
?>
