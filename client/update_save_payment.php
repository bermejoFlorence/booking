<?php
session_start();
include("../connection.php");
date_default_timezone_set('Asia/Manila'); // Set Philippine Timezone
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['booking_id'] ?? null;
    $transac_num = $_POST['transac_num'] ?? null;
    $new_amt_payment = $_POST['amt_payment'] ?? null;
    $payment_method = $_POST['payment_status'] ?? null;
    $payment_type = $_POST['payment_type'] ?? null; 
    $new_reference_no = $_POST['reference_no'] ?? null;

    if (empty($booking_id) || empty($transac_num) || empty($payment_method) || empty($new_amt_payment)) {
        echo "<script>
            alert('Error: Missing required fields.');
            window.history.back();
        </script>";
        exit();
    }

    // Get existing amt_payment, reference_no, and payment_status
    $query = "SELECT amt_payment, reference_no, payment_status FROM payment WHERE booking_id = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->bind_result($old_amt_payment, $old_reference_no, $old_payment_status);
    $stmt->fetch();
    $stmt->close();

    // Concatenate old and new values
    $updated_amt_payment = empty($old_amt_payment) ? $new_amt_payment : $old_amt_payment . ", " . $new_amt_payment;
    $updated_reference_no = empty($old_reference_no) ? $new_reference_no : $old_reference_no . ", " . $new_reference_no;
    $updated_payment_status = empty($old_payment_status) ? ucfirst($payment_type) . " Paid" : $old_payment_status . ", " . ucfirst($payment_type) . " Paid";

    // Update payment table
    $update_query = "UPDATE payment SET amt_payment = ?, reference_no = ?, payment_status = ? WHERE booking_id = ?";
    $stmt = $database->prepare($update_query);
    $stmt->bind_param("sssi", $updated_amt_payment, $updated_reference_no, $updated_payment_status, $booking_id);

    if ($stmt->execute()) {
        // Insert new sales record
        $emp_id = 1;
        $date = date("Y-m-d"); // Current Philippine Date
        $sales_query = "INSERT INTO sales (emp_id, date, total_sales) VALUES (?, ?, ?)";
        $sales_stmt = $database->prepare($sales_query);
        $sales_stmt->bind_param("isd", $emp_id, $date, $new_amt_payment);
        $sales_stmt->execute();
        $sales_stmt->close();

        echo "<!DOCTYPE html>
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
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Error processing payment! Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }

    $stmt->close();
    $database->close();
}
?>
