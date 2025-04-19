<?php
session_start();
include("../connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['booking_id'] ?? null;
    $amt_payment = $_POST['amt_payment'] ?? 0;
    $reference_no = $_POST['reference_no'] ?? null;

    if (empty($booking_id) || empty($reference_no) || $amt_payment <= 0) {
        echo "<script>
                alert('Error: Missing required fields or invalid amount.');
                window.history.back();
              </script>";
        exit();
    }

    $payment_status = "processing payment";

    $database->begin_transaction();

    try {
        // ✅ Insert new payment linked to the same booking_id
        $insert = $database->prepare("
            INSERT INTO payment (booking_id, amt_payment, reference_no, payment_status)
            VALUES (?, ?, ?, ?)
        ");

        if (!$insert) {
            throw new Exception("Prepare failed: " . $database->error);
        }

        $insert->bind_param("idss", $booking_id, $amt_payment, $reference_no, $payment_status);

        if (!$insert->execute()) {
            throw new Exception("Insert failed: " . $insert->error);
        }
        $insert->close();

        // ✅ Ensure booking stat reflects in-process status
        $update = $database->prepare("UPDATE booking SET stat = 'processing' WHERE booking_id = ?");
        if (!$update) {
            throw new Exception("Booking update prepare failed: " . $database->error);
        }

        $update->bind_param("i", $booking_id);

        if (!$update->execute()) {
            throw new Exception("Booking update failed: " . $update->error);
        }
        $update->close();

        $database->commit();

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
                    text: 'Your second payment has been recorded.',
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
        error_log("Payment update error: " . $e->getMessage());

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
