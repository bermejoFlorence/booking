<?php
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $database->begin_transaction();

    try {
        // ✅ 1. Update the booking status right away
        $query = $database->prepare("UPDATE booking SET stat = ? WHERE booking_id = ?");
        $query->bind_param("si", $status, $booking_id);
        $query->execute();

        // ✅ 2. If status is approved, proceed with payment/sales updates
        if ($status === "approved") {
            // First check if there is a corresponding payment record
            $paymentCheck = $database->prepare("SELECT amt_payment FROM payment WHERE booking_id = ?");
            $paymentCheck->bind_param("i", $booking_id);
            $paymentCheck->execute();
            $paymentCheck->store_result();

            if ($paymentCheck->num_rows > 0) {
                $paymentCheck->bind_result($amt_payment);
                $paymentCheck->fetch();
                $paymentCheck->close();

                // Generate receipt number
                $receipt_no = random_int(100000, 999999);

                // Update receipt number
                $paymentQuery = $database->prepare("UPDATE payment SET receipt_no = ? WHERE booking_id = ?");
                $paymentQuery->bind_param("ii", $receipt_no, $booking_id);
                $paymentQuery->execute();
                $paymentQuery->close();

                // Insert or update sales
                $currentDate = date("Y-m-d");
                $emp_id = 1;

                $salesCheckQuery = $database->prepare("SELECT id FROM sales WHERE date = ? AND emp_id = ?");
                $salesCheckQuery->bind_param("si", $currentDate, $emp_id);
                $salesCheckQuery->execute();
                $salesCheckQuery->store_result();

                if ($salesCheckQuery->num_rows > 0) {
                    $salesUpdateQuery = $database->prepare("UPDATE sales SET total_sales = total_sales + ? WHERE date = ? AND emp_id = ?");
                    $salesUpdateQuery->bind_param("dsi", $amt_payment, $currentDate, $emp_id);
                    $salesUpdateQuery->execute();
                    $salesUpdateQuery->close();
                } else {
                    $salesInsertQuery = $database->prepare("INSERT INTO sales (emp_id, date, total_sales) VALUES (?, ?, ?)");
                    $salesInsertQuery->bind_param("isd", $emp_id, $currentDate, $amt_payment);
                    $salesInsertQuery->execute();
                    $salesInsertQuery->close();
                }

                $salesCheckQuery->close();
            } else {
                // Walang payment record yet
                $paymentCheck->close();
            }
        }

        // ✅ 3. Commit transaction
        $database->commit();

        if ($status === "approved") {
            echo "Booking Approved. Sales updated if payment exists.";
        } else if ($status === "rejected") {
            echo "Booking Rejected.";
        }
    } catch (Exception $e) {
        $database->rollback();
        echo "Failed to update booking status: " . $e->getMessage();
    }

    $query->close();
    $database->close();
}
?>
