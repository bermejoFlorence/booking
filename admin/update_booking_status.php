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

        // Kung `approved` ang status, mag-generate ng receipt number at i-update ang `payment` table
        if ($status === "approved") {
            // Generate random 6-digit receipt number
            $receipt_no = random_int(100000, 999999);

            // Update the receipt_no in the payment table
            $paymentQuery = $database->prepare("UPDATE payment SET receipt_no = ? WHERE booking_id = ?");
            $paymentQuery->bind_param("ii", $receipt_no, $booking_id);
            $paymentQuery->execute();
        }

        // Commit the transaction
        $database->commit();

        // Magpakita ng tamang message depende sa status
        if ($status === "approved") {
            echo "Booking Approved.";
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
