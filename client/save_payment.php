<?php
session_start();
include("../connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['booking_id'] ?? null;
    $amt_payment = $_POST['amt_payment'] ?? null;
    $reference_no = $_POST['reference_no'] ?? null;

    // Validate required fields
    if (empty($booking_id) || empty($amt_payment) || empty($reference_no)) {
        echo "<script>
                alert('Error: All fields are required.');
                window.history.back();
              </script>";
        exit();
    }

    date_default_timezone_set('Asia/Manila');
    $current_datetime = date('Y-m-d H:i:s');

    $database->begin_transaction();

    try {
        // ✅ 1. INSERT into payment table
        $query = "INSERT INTO payment (booking_id, amt_payment, reference_no, payment_status, receipt_no, date_created) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $database->prepare($query);

        if ($stmt) {
            $payment_status = "processing payment";
            $receipt_no = "processing payment";

            $stmt->bind_param("isssss", $booking_id, $amt_payment, $reference_no, $payment_status, $receipt_no, $current_datetime);

            if (!$stmt->execute()) {
                throw new Exception("Error inserting payment: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Error preparing payment query: " . $database->error);
        }

        // ✅ 2. UPDATE booking.stat = 'processing'
        $updateBooking = $database->prepare("UPDATE booking SET stat = ? WHERE booking_id = ?");
        $processingStat = "processing";
        $updateBooking->bind_param("si", $processingStat, $booking_id);
        if (!$updateBooking->execute()) {
            throw new Exception("Error updating booking status: " . $updateBooking->error);
        }
        $updateBooking->close();

        // ✅ 3. Commit changes
        $database->commit();

        // ✅ 4. SweetAlert success
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: 'Payment Successful!',
                    text: 'Your payment has been recorded successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'bookings.php';
                });
            </script>
        </body>
        </html>";
        exit();

    } catch (Exception $e) {
        $database->rollback();
        error_log("Transaction Failed: " . $e->getMessage());

        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                title: 'Transaction Failed!',
                text: '" . addslashes($e->getMessage()) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }

    $database->close();
} else {
    echo "<script>
            alert('Invalid request method.');
            window.history.back();
          </script>";
}
?>
