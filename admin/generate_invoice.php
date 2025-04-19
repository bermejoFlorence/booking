<?php
include("../connection.php");

if (isset($_GET['booking_id']) && !empty($_GET['booking_id'])) {
    $bookingId = $_GET['booking_id'];

    // Booking and client info
    $stmt1 = $database->prepare("
        SELECT 
            b.booking_id,
            b.package,
            b.price,
            b.event,
            b.date_event,
            b.address_event,
            b.client_id,
            c.c_fullname,
            c.c_contactnum,
            c.c_address,
            p.receipt_no
        FROM booking b
        JOIN client c ON b.client_id = c.client_id
        LEFT JOIN payment p ON p.booking_id = b.booking_id
        WHERE b.booking_id = ?
        ORDER BY p.date_created DESC
        LIMIT 1
    ");
    $stmt1->bind_param("s", $bookingId);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $info = $result1->fetch_assoc();

    // Payment history
    $stmt2 = $database->prepare("
        SELECT amt_payment, reference_no, payment_status, date_created 
        FROM payment 
        WHERE booking_id = ?
        ORDER BY date_created ASC
    ");
    $stmt2->bind_param("s", $bookingId);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $payments = [];
    while ($row = $result2->fetch_assoc()) {
        $payments[] = $row;
    }

    $info["payment_history"] = $payments;

    echo json_encode($info);
} else {
    echo json_encode(["error" => "Missing booking ID"]);
}
?>
