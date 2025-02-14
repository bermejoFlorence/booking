<?php
include("../connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    // Start a transaction to ensure consistency
    $database->begin_transaction();

    try {
        // Update the booking status
        $query = $database->prepare("UPDATE booking SET stat = ? WHERE booking_id = ?");
        $query->bind_param("si", $status, $booking_id);
        $query->execute();

        // Kung `approved` ang status, mag-generate ng receipt number, i-update ang `payment`, at mag-save sa `sales` table
        if ($status === "approved") {
            // Generate random 6-digit receipt number
            $receipt_no = random_int(100000, 999999);

            // Update the receipt_no in the payment table
            $paymentQuery = $database->prepare("UPDATE payment SET receipt_no = ? WHERE booking_id = ?");
            $paymentQuery->bind_param("ii", $receipt_no, $booking_id);
            $paymentQuery->execute();

            // Kunin ang payment amount at current date
            $paymentAmountQuery = $database->prepare("SELECT amt_payment FROM payment WHERE booking_id = ?");
            $paymentAmountQuery->bind_param("i", $booking_id);
            $paymentAmountQuery->execute();
            $paymentAmountQuery->bind_result($amt_payment);
            $paymentAmountQuery->fetch();
            $paymentAmountQuery->close();

            $currentDate = date("Y-m-d");
            $emp_id = 1; // Example employee ID; palitan ito kung may dynamic value

            // I-check kung may existing record na sa sales table para sa parehong date at emp_id
            $salesCheckQuery = $database->prepare("SELECT id FROM sales WHERE date = ? AND emp_id = ?");
            $salesCheckQuery->bind_param("si", $currentDate, $emp_id);
            $salesCheckQuery->execute();
            $salesCheckQuery->store_result();

            if ($salesCheckQuery->num_rows > 0) {
                // I-update ang existing sales record
                $salesUpdateQuery = $database->prepare("UPDATE sales SET total_sales = total_sales + ? WHERE date = ? AND emp_id = ?");
                $salesUpdateQuery->bind_param("dsi", $amt_payment, $currentDate, $emp_id);
                $salesUpdateQuery->execute();
                $salesUpdateQuery->close();
            } else {
                // Mag-insert ng bagong record sa sales table
                $salesInsertQuery = $database->prepare("INSERT INTO sales (emp_id, date, total_sales) VALUES (?, ?, ?)");
                $salesInsertQuery->bind_param("isd", $emp_id, $currentDate, $amt_payment);
                $salesInsertQuery->execute();
                $salesInsertQuery->close();
            }

            $salesCheckQuery->close();
        }

        // Commit the transaction
        $database->commit();

        // Magpakita ng tamang message depende sa status
        if ($status === "approved") {
            echo "Booking Approved and sales record updated.";
        } else if ($status === "rejected") {
            echo "Booking Rejected.";
        }
    } catch (Exception $e) {
        // Rollback if any error occurs
        $database->rollback();
        echo "Failed to update booking status: " . $e->getMessage();
    }

    $query->close();
    $database->close();
}
?>
