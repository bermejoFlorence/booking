<?php
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $receipt_no = $_POST['receipt_no'];
    $emp_id = 1; // Static for now. Replace with session/admin ID if available.
    $today = date("Y-m-d");
    $now = date("Y-m-d H:i:s");

    $database->begin_transaction();

    try {
        // 1. Hanapin ang pinakabagong payment record ng booking_id na walang resibo pa
        $query = $database->prepare("
            SELECT payment_id, amt_payment 
            FROM payment 
            WHERE booking_id = ? 
              AND payment_status = 'processing payment' 
              AND (receipt_no IS NULL OR receipt_no = '') 
            ORDER BY date_created DESC 
            LIMIT 1
        ");
        $query->bind_param("i", $booking_id);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Walang pending payment record na puwedeng i-update.");
        }

        $payment = $result->fetch_assoc();
        $payment_id = $payment['payment_id'];
        $amt_payment = floatval($payment['amt_payment']);
        $query->close();

        // 2. Update payment row para i-set ang receipt_no
        $update = $database->prepare("
            UPDATE payment 
            SET receipt_no = ?, date_created = ? 
            WHERE payment_id = ?
        ");
        $update->bind_param("ssi", $receipt_no, $now, $payment_id);
        $update->execute();
        $update->close();

        // 3. Add amt_payment to sales table
        $check_sales = $database->prepare("SELECT id FROM sales WHERE date = ? AND emp_id = ?");
        $check_sales->bind_param("si", $today, $emp_id);
        $check_sales->execute();
        $check_sales->store_result();

        if ($check_sales->num_rows > 0) {
            $update_sales = $database->prepare("UPDATE sales SET total_sales = total_sales + ? WHERE date = ? AND emp_id = ?");
            $update_sales->bind_param("dsi", $amt_payment, $today, $emp_id);
            $update_sales->execute();
            $update_sales->close();
        } else {
            $insert_sales = $database->prepare("INSERT INTO sales (emp_id, date, total_sales) VALUES (?, ?, ?)");
            $insert_sales->bind_param("isd", $emp_id, $today, $amt_payment);
            $insert_sales->execute();
            $insert_sales->close();
        }
        $check_sales->close();

        $database->commit();
        echo "✅ Payment receipt updated and amount added to sales successfully.";
    } catch (Exception $e) {
        $database->rollback();
        http_response_code(500);
        echo "❌ Error: " . $e->getMessage();
    }
}
?>
