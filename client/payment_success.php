<?php
// You can also log source ID from URL
$source_id = $_GET['source'] ?? 'unknown';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Successful</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #f4f6f9;
      padding: 50px;
    }
    .success-box {
      background: #ffffff;
      border-radius: 10px;
      padding: 40px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
      display: inline-block;
    }
    .success-box h1 {
      color: #2ecc71;
      margin-bottom: 10px;
    }
    .success-box p {
      color: #555;
    }
  </style>
</head>
<body>
  <div class="success-box">
    <h1>🎉 Payment Successful!</h1>
    <p>Thank you! We’ve received your payment.</p>
    <p><small>Transaction Ref: <?php echo htmlspecialchars($source_id); ?></small></p>
    <a href="index.php">← Go back to homepage</a>
  </div>
</body>
</html>
