<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



// Kung manual download, i-include ang mga file
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Simulan ang PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // SMTP Server ng Gmail
    $mail->SMTPAuth   = true;
    $mail->Username   = 'florencebermejo09@gmail.com'; // Gamitin ang iyong Gmail
    $mail->Password   = 'jqkc hulz qqhv mfqo'; // Gumamit ng App Password (hindi regular na password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & Recipient
    $mail->setFrom('your-email@gmail.com', 'Your Name');
    $mail->addAddress('recipient-email@gmail.com', 'Recipient Name'); 

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email using PHPMailer';
    $mail->Body    = '<h3>Hello! This is a test email sent from PHPMailer.</h3>';

    // Send Email
    $mail->send();
    echo 'Email has been sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
