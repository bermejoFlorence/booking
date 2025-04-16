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

    // Begin transaction
    $database->begin_transaction();

    try {
        // 1. Check if already processed
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
        $stmt1 = $database->prepare("UPDATE payment SET payment_status = ?, receipt_no = ? WHERE booking_id = ?");
        $stmt1->bind_param("ssi", $payment_status, $receipt_no, $booking_id);
        $stmt1->execute();

        // 3. Insert into sales table
        $stmt2 = $database->prepare("INSERT INTO sales (emp_id, date, total_sales) VALUES (?, ?, ?)");
        $stmt2->bind_param("isd", $emp_id, $date, $amt_payment);
        $stmt2->execute();

        // ✅ 4. Update booking.stat = payment_status
        $stmt3 = $database->prepare("UPDATE booking SET stat = ? WHERE booking_id = ?");
        $stmt3->bind_param("si", $payment_status, $booking_id);
        $stmt3->execute();

        // 5. Commit all
        $database->commit();
        echo "Payment, sales, and booking status updated successfully.";
    } catch (Exception $e) {
        $database->rollback();
        http_response_code(500);
        echo $e->getMessage();
    }
}
?>
