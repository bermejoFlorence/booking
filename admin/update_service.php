<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION["usertype"] != "a") {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
    $service_name = isset($_POST['service_name']) ? trim($_POST['service_name']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;

    // Check if service_details is set and not empty
    $service_details = "";
    if (isset($_POST['service_details']) && is_array($_POST['service_details'])) {
        $service_details = implode('|', array_map('trim', $_POST['service_details']));
    }

    if ($service_id > 0 && !empty($service_name)) {
        // Prepare update query
        $stmt = $database->prepare("UPDATE services 
                                    SET name = ?, details = ?, price = ?, date_created = NOW() 
                                    WHERE service_id = ?");
        $stmt->bind_param("ssdi", $service_name, $service_details, $price, $service_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Service updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update service."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid service ID or service name."]);
    }
}
$database->close();
?>
