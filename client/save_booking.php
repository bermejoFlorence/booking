<?php
session_start();
include("../connection.php");

header('Content-Type: application/json');

// Siguraduhing naka-login ang user
if (!isset($_SESSION["user"])) {
    echo json_encode(["status" => "error", "message" => "User not logged in!"]);
    exit();
}

// Kunin ang client_id mula sa session
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT client_id FROM client WHERE c_email='$useremail'");

if ($userrow && $userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $client_id = $userfetch["client_id"];
} else {
    echo json_encode(["status" => "error", "message" => "Client not found!"]);
    exit();
}

// Kunin ang data mula sa POST
$package = $_POST['package'] ?? '';
$price_raw = $_POST['price'] ?? '';
$event = $_POST['event'] ?? '';
$date_event = $_POST['date_event'] ?? '';
$address_event = $_POST['address_event'] ?? '';

// Sanitize price (remove ₱ and commas if present)
$price = str_replace(['₱', ','], '', $price_raw);

// Suriin kung may kulang na data
if (empty($package) || empty($price) || empty($event) || empty($date_event) || empty($address_event)) {
    echo json_encode(["status" => "error", "message" => "Please fill in all required fields!"]);
    exit();
}

// Validate date format
if (!DateTime::createFromFormat('Y-m-d', $date_event)) {
    echo json_encode(["status" => "error", "message" => "Invalid event date format."]);
    exit();
}

// Kunin ang kasalukuyang petsa at oras (Philippine Time)
date_default_timezone_set('Asia/Manila'); 
$date_created = date('Y-m-d H:i:s');

// I-save sa database
$query = "INSERT INTO booking (client_id, package, price, event, date_event, address_event, stat, date_created) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $database->prepare($query);
$stat = "pending"; 

$stmt->bind_param("isssssss", $client_id, $package, $price, $event, $date_event, $address_event, $stat, $date_created);

if ($stmt->execute()) {
    // Booking saved successfully - no payment record created here
    echo json_encode(["status" => "success", "message" => "Booking saved successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error saving booking: " . $stmt->error]);
}
?>
