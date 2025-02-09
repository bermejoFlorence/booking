<?php
session_start();
include("../connection.php");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $booking_id = $_POST['booking_id'];
    $transac_num = $_POST['transac_num'];
    $amt_payment = $_POST['amt_payment'];
    $payment_status = $_POST['payment_status'];
    $reference_no = isset($_POST['reference_no']) ? $_POST['reference_no'] : null;

    // Validate required fields
    if (empty($booking_id) || empty($transac_num) || empty($amt_payment) || empty($payment_status)) {
        echo "<script>
                alert('Error: Missing required fields.');
                window.history.back();
              </script>";
        exit();
    }

    // Determine payment status and reference number
    if ($payment_status === "walk-in") {
        $payment_status = "No Payment";
        $reference_no = "walkinpayment";
    } elseif ($payment_status === "through gcash") {
        $payment_status = "Fully Paid";
        if (empty($reference_no)) {
            echo "<script>
                    alert('Error: Reference number is required for GCash payments.');
                    window.history.back();
                  </script>";
            exit();
        }
    }

    // Start transaction
    $database->begin_transaction();

    try {
        // Insert data into the payment table
        $query = "INSERT INTO payment (booking_id, transac_num, amt_payment, payment_status, reference_no) VALUES (?, ?, ?, ?, ?)";
        $stmt = $database->prepare($query);

        if ($stmt) {
            $stmt->bind_param("isiss", $booking_id, $transac_num, $amt_payment, $payment_status, $reference_no);

            if (!$stmt->execute()) {
                throw new Exception("Error inserting payment: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Error preparing payment query: " . $database->error);
        }

        // Update the stat column in the booking table
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

        // Commit the transaction
        $database->commit();

        // ✅ Show SweetAlert and redirect using JavaScript
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: 'Payment Successful!',
                    text: 'Your payment has been recorded successfully. Please wait the confirmation to approve.',
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
        // Rollback transaction on error
        $database->rollback();
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: 'Transaction Failed!',
                    text: '" . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.history.back();
                });
            </script>
        </body>
        </html>";
    }

    $database->close();
} else {
    echo "<script>
            alert('Invalid request method.');
            window.history.back();
          </script>";
}
?>
