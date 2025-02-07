<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION["usertype"] != "a") {
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_id = $_POST['service_id'];
    $service_name = $_POST['service_name'];
    $price = $_POST['price'];

    // Concatenate service details
    $service_details_array = $_POST['service_details'];
    $service_details = implode('|', array_map('trim', $service_details_array));

    // Update query
    $stmt = $database->prepare("UPDATE services 
                                SET name = ?, details = ?, price = ?, date_created = NOW()
                                WHERE service_id = ?");
    $stmt->bind_param("ssdi", $service_name, $service_details, $price, $service_id);

    if ($stmt->execute()) {
        // Return success response
        echo json_encode(["status" => "success", "message" => "Service updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update service."]);
    }

    $stmt->close();
}
$database->close();
?>
