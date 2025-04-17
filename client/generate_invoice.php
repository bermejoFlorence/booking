<?php
include '../connection.php';

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    $stmt = $database->prepare("
        SELECT 
            p.receipt_no, p.transac_num, p.amt_payment, p.payment_status, p.date_created,
            b.date_event, b.event, b.package, b.price, b.address_event,
            c.c_fullname, c.c_contactnum, c.c_address
        FROM payment p
        JOIN booking b ON p.booking_id = b.booking_id
        JOIN client c ON b.client_id = c.client_id
        WHERE p.booking_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    echo json_encode($result);
}
?>
