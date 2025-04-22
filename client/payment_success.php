<?php
include("../connection.php");

$source_id = $_GET['source'] ?? null;
$booking_id = $_GET['booking_id'] ?? null;

if ($source_id && $booking_id) {
    $stmt = $database->prepare("UPDATE payment SET source_id = ?, payment_status = 'Full Payment' WHERE booking_id = ?");
    $stmt->bind_param("si", $source_id, $booking_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Success</title>
  <style>
    body { font-family: Arial; text-align: center; background: #f4f6f9; padding: 50px; }
    .box { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: inline-block; }
    h1 { color: #27ae60; }
    p { color: #333; }
  </style>
</head>
<body>
  <div class="box">
    <h1>✅ Payment Recorded!</h1>
    <p>Thank you! Your payment for <strong>Booking #<?php echo htmlspecialchars($booking_id); ?></strong> is now marked as <strong>Full Payment</strong>.</p>
    <p><small>GCash Reference: <strong><?php echo htmlspecialchars($source_id); ?></strong></small></p>
    <a href="my_bookings.php">View Bookings</a>
  </div>
</body>
</html>
