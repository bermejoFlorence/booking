<?php
session_start();
include("../connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['booking_id'] ?? null;
    $amt_payment = $_POST['amt_payment'] ?? 0;
    $reference_no = $_POST['reference_no'] ?? null;

    // Validate essential fields
    if (empty($booking_id) || empty($reference_no) || $amt_payment <= 0) {
        echo "<script>
                alert('Error: Missing required fields or invalid amount.');
                window.history.back();
              </script>";
        exit();
    }

    // Start transaction
    $database->begin_transaction();

    try {
        // Insert payment record
        $query = "INSERT INTO payment (booking_id, amt_payment, reference_no) VALUES (?, ?, ?)";
        $stmt = $database->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ids", $booking_id, $amt_payment, $reference_no);

            if (!$stmt->execute()) {
                throw new Exception("Error inserting payment: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Error preparing payment query: " . $database->error);
        }

        // Update booking status to 'processing'
        $update_query = "UPDATE booking SET stat = 'processing' WHERE booking_id = ?";
        $update_stmt = $database->prepare($update_query);

        if ($update_stmt) {
            $update_stmt->bind_param("i", $booking_id);

            if (!$update_stmt->execute()) {
                throw new Exception("Error updating booking status: " . $update_stmt->error);
            }
            $update_stmt->close();
        } else {
            throw new Exception("Error preparing booking update query: " . $database->error);
        }

        // Commit all
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
