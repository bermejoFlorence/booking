<?php
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $payment_status = $_POST['payment_status']; // 'Partial Payment' or 'Full Payment'
    $receipt_no = $_POST['receipt_no'];
    $raw_amt = str_replace(',', '', $_POST['amt_payment']);
    $amt_payment = intval($raw_amt);

    $emp_id = 1; // Default employee ID
    $date = date("Y-m-d H:i:s");

    $database->begin_transaction();

    try {
        // 1. Check if already finalized
        $check_stmt = $database->prepare("SELECT payment_status FROM payment WHERE booking_id = ?");
        $check_stmt->bind_param("i", $booking_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            $current_status = strtolower(trim($row['payment_status']));

            if (in_array($current_status, ['partial payment', 'full payment'])) {
                throw new Exception("This payment has already been finalized.");
            }
        }

        // 2. Update payment table
        $stmt1 = $database->prepare("UPDATE payment SET payment_status = ?, receipt_no = ?, amt_payment = ?, date_created = NOW() WHERE booking_id = ?");
        $stmt1->bind_param("ssii", $payment_status, $receipt_no, $amt_payment, $booking_id);
        $stmt1->execute();

        // 3. Insert or update sales table
        $date_only = date("Y-m-d"); // YYYY-MM-DD format
        $check_sales = $database->prepare("SELECT id FROM sales WHERE date = ? AND emp_id = ?");
        $check_sales->bind_param("si", $date_only, $emp_id);
        $check_sales->execute();
        $check_sales->store_result();

        if ($check_sales->num_rows > 0) {
            $update_sales = $database->prepare("UPDATE sales SET total_sales = total_sales + ? WHERE date = ? AND emp_id = ?");
            $update_sales->bind_param("dsi", $amt_payment, $date_only, $emp_id);
            $update_sales->execute();
            $update_sales->close();
        } else {
            $stmt2 = $database->prepare("INSERT INTO sales (emp_id, date, total_sales) VALUES (?, ?, ?)");
            $stmt2->bind_param("isd", $emp_id, $date_only, $amt_payment);
            $stmt2->execute();
            $stmt2->close();
        }
        $check_sales->close();

        // 4. Update booking status
        $booking_stat = strtolower($payment_status) === 'full payment' ? 'completed' : 'processing';
        $stmt3 = $database->prepare("UPDATE booking SET stat = ? WHERE booking_id = ?");
        $stmt3->bind_param("si", $booking_stat, $booking_id);
        $stmt3->execute();
        $stmt3->close();

        // 5. Commit all
        $database->commit();
        echo "Payment processed successfully. Booking updated to '{$booking_stat}'.";
    } catch (Exception $e) {
        $database->rollback();
        http_response_code(500);
        echo $e->getMessage();
    }
}
?>
