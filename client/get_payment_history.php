<?php
include("../connection.php");

// Ensure booking_id is passed
if (isset($_GET['booking_id']) && !empty($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];

    // Prepare secure SQL query
    $stmt = $database->prepare("SELECT amt_payment, reference_no, transac_num, payment_status, date_created 
                                FROM payment 
                                WHERE booking_id = ? 
                                ORDER BY date_created ASC");
    $stmt->bind_param("s", $bookingId);
    $stmt->execute();

    // Fetch and return results as JSON
    $result = $stmt->get_result();
    $history = [];

    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }

    echo json_encode($history);
} else {
    echo json_encode([]); // Return empty array if no booking_id
}
?>
