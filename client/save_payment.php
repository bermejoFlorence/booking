<?php
session_start();
include("../connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['booking_id'] ?? null;
    $amt_payment_raw = $_POST['amt_payment'] ?? null;
    $reference_no = $_POST['reference_no'] ?? null;

    // Strip commas and convert to float
    $amt_payment = floatval(str_replace(',', '', $amt_payment_raw));
    $current_datetime = date('Y-m-d H:i:s');

    // Validate required fields
    if (empty($booking_id) || empty($amt_payment) || empty($reference_no)) {
        echo "<script>
                alert('Error: All fields are required.');
                window.history.back();
              </script>";
        exit();
    }

    $database->begin_transaction();

    try {
        // 1. Check latest approved payment for current balance
        $balanceQuery = $database->prepare("
            SELECT b.price - IFNULL(p.amt_payment, 0) as current_balance
            FROM booking b
            LEFT JOIN (
                SELECT booking_id, amt_payment
                FROM payment
                WHERE booking_id = ?
                AND LOWER(payment_status) IN ('partial payment', 'full payment')
                ORDER BY date_created DESC
                LIMIT 1
            ) p ON b.booking_id = p.booking_id
            WHERE b.booking_id = ?
        ");
        $balanceQuery->bind_param("ii", $booking_id, $booking_id);
        $balanceQuery->execute();
        $balanceResult = $balanceQuery->get_result();

        $current_balance = 0;
        if ($row = $balanceResult->fetch_assoc()) {
            $current_balance = (float)$row['current_balance'];
        }
        $balanceQuery->close();

        if ($amt_payment > $current_balance) {
            echo "<script>
                alert('Error: Payment exceeds remaining balance (₱" . number_format($current_balance, 2) . ").');
                window.history.back();
            </script>";
            exit();
        }

        // 2. Insert payment row with 'processing payment'
        $payment_status = "processing payment";
        $insertQuery = $database->prepare("
            INSERT INTO payment (booking_id, amt_payment, reference_no, payment_status, date_created)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertQuery->bind_param("idsss", $booking_id, $amt_payment, $reference_no, $payment_status, $current_datetime);
        if (!$insertQuery->execute()) {
            throw new Exception("Error inserting payment: " . $insertQuery->error);
        }
        $insertQuery->close();

        // 3. Update booking status to 'processing'
        // 3. Update booking status to 'processing' and update booking date_created
$updateBooking = $database->prepare("
UPDATE booking 
SET stat = 'processing', date_created = ? 
WHERE booking_id = ?
");
$updateBooking->bind_param("si", $current_datetime, $booking_id);
if (!$updateBooking->execute()) {
throw new Exception("Error updating booking status and date_created: " . $updateBooking->error);
}
$updateBooking->close();

        // 4. Commit and alert success
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
