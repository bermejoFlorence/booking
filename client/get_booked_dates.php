<?php
session_start();
include("../connection.php"); // Ensure na tama ang database connection

header('Content-Type: application/json');

$booked_dates = [];
$query = $database->query("SELECT date_event FROM booking");

while ($row = $query->fetch_assoc()) {
    $booked_dates[] = $row['date_event'];
}

// Return the booked dates as JSON
echo json_encode($booked_dates);
?>
