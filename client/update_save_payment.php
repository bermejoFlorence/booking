<?php
session_start();
include("../connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $booking_id = $_POST['booking_id'] ?? null;
    $transac_num = $_POST['transac_num'] ?? null;
    $amt_payment = $_POST['amt_payment'] ?? 0; // Default 0 if empty
    $payment_method = $_POST['payment_status'] ?? null;
    $payment_type = $_POST['payment_type'] ?? null;
    $reference_no = $_POST['reference_no'] ?? null;

    // Generate a random 6-digit receipt number
    $receipt_no = mt_rand(100000, 999999);

    // Validate required fields
    if (empty($booking_id) || empty($transac_num) || empty($payment_method)) {
        echo "<script>
                alert('Error: Missing required fields.');
                window.history.back();
              </script>";
        exit();
    }

    // Payment handling logic
    if ($payment_method === "walk-in") {
        $payment_status = "No Payment"; // Default status for walk-in
        $reference_no = "walkinpayment";
        $amt_payment = 0; // Ensure amount is 0 for Walk-in
    } elseif ($payment_method === "through gcash") {
        if (empty($reference_no) || empty($payment_type)) {
            echo "<script>
                    alert('Error: Reference number and payment type are required for GCash payments.');
                    window.history.back();
                  </script>";
            exit();
        }
        $payment_status = ucfirst($payment_type) . " Paid"; // "Partial Paid" or "Full Paid"
    }

    // Start transaction
    $database->begin_transaction();

    try {
        // Insert into payment table
        $query = "INSERT INTO payment (booking_id, transac_num, amt_payment, payment_status, reference_no, receipt_no) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $database->prepare($query);

        if ($stmt) {
            $stmt->bind_param("isissi", $booking_id, $transac_num, $amt_payment, $payment_status, $reference_no, $receipt_no);
            
            if (!$stmt->execute()) {
                throw new Exception("Error inserting payment: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Error preparing payment query: " . $database->error);
        }

        // Update booking status
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

        // Save to sales table if amt_payment > 0
        if ($amt_payment > 0) {
            date_default_timezone_set('Asia/Manila'); // Set timezone to Philippine Time
            $emp_id = 1; // Fixed Employee ID
            $date = date('Y-m-d'); // Get current date
            
            $sales_query = "INSERT INTO sales (emp_id, date, total_sales) VALUES (?, ?, ?)";
            $sales_stmt = $database->prepare($sales_query);
        
            if ($sales_stmt) {
                $sales_stmt->bind_param("isd", $emp_id, $date, $amt_payment);
        
                if (!$sales_stmt->execute()) {
                    throw new Exception("Error inserting sales: " . $sales_stmt->error);
                }
                $sales_stmt->close();
            } else {
                throw new Exception("Error preparing sales query: " . $database->error);
            }
        }

        // Commit transaction
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
                    text: 'Your payment has been recorded successfully. Receipt No: $receipt_no',
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
