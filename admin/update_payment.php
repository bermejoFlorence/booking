<?php
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $receipt_no = $_POST['receipt_no'];

    $emp_id = 1; // Default admin
    $date = date("Y-m-d H:i:s");

    $database->begin_transaction();

    try {
        // 1. Hanapin ang pinakabagong 'processing payment' entry na walang receipt_no
        $find = $database->prepare("SELECT id FROM payment WHERE booking_id = ? AND payment_status = 'processing payment' AND (receipt_no IS NULL OR receipt_no = '') ORDER BY date_created DESC LIMIT 1");
        $find->bind_param("i", $booking_id);
        $find->execute();
        $result = $find->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Walang pending payment na kailangang i-update ng resibo.");
        }

        $row = $result->fetch_assoc();
        $payment_id = $row['id'];
        $find->close();

        // 2. I-update ang receipt_no sa record na 'processing payment'
        $update = $database->prepare("UPDATE payment SET receipt_no = ?, date_created = NOW() WHERE id = ?");
        $update->bind_param("si", $receipt_no, $payment_id);
        $update->execute();
        $update->close();

        $database->commit();
        echo "Resibo na-update successfully!";
    } catch (Exception $e) {
        $database->rollback();
        http_response_code(500);
        echo $e->getMessage();
    }
}
?>
