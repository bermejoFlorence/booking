<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
include("connection.php");
require __DIR__ . '/vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check kung may existing email
    $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $token = bin2hex(random_bytes(50)); // Generate token
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Expiry 1 hour

        // Save token sa database
        $update = $database->prepare("UPDATE webuser SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // Setup PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'florencebermejo09@gmail.com';
            $mail->Password = 'jqkc hulz qqhv mfqo'; // Gumamit ng App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'Book Management');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click <a href='http://localhost/book/reset_password.php?token=$token'>here</a> to reset your password.";

            if ($mail->send()) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Email Sent!',
                            text: 'Check your email for the reset link.',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'login.php';
                            }
                        });
                    });
                </script>";
                exit; // Para hindi na magpatuloy ang PHP script
            }
        } catch (Exception $e) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Email sending failed: " . addslashes($mail->ErrorInfo) . "',
                        confirmButtonText: 'Try Again'
                    });
                });
            </script>";
            exit;
        }
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Email Not Found!',
                text: 'Please enter a registered email.',
                confirmButtonText: 'Try Again'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        });
    </script>";
    exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <!-- <link rel="stylesheet" href="css/login.css"> -->

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f7f7f7;
        }

        .container {
            width: 40%;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header-text {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .sub-text {
            font-size: 1rem;
            color: gray;
            margin-bottom: 1.5rem;
        }

        .input-text {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn {
            width: 100%;
            padding: 0.8rem;
            border: none;
            background: #007bff;
            color: white;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 1rem;
        }

        .btn:hover {
            background: #0056b3;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #007bff;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .back-btn:hover {
            text-decoration: underline;
        }

        /* === Responsive Styles === */
        @media screen and (max-width: 1024px) {
            .container {
                width: 50%;
            }
        }

        @media screen and (max-width: 768px) {
            .container {
                width: 70%;
                padding: 1.5rem;
            }

            .header-text {
                font-size: 1.5rem;
            }

            .sub-text {
                font-size: 0.9rem;
            }

            .btn {
                font-size: 0.9rem;
            }
        }

        @media screen and (max-width: 480px) {
            .container {
                width: 90%;
                padding: 1.2rem;
            }

            .header-text {
                font-size: 1.3rem;
            }

            .sub-text {
                font-size: 0.85rem;
            }

            .btn {
                font-size: 0.85rem;
                padding: 0.7rem;
            }
        }
    </style>
<div class="container">
    <p class="header-text">Forgot Password?</p>
    <p class="sub-text">Enter your email to reset your password</p>

    <form action="" method="POST">
        <input type="email" name="email" class="input-text" placeholder="Email Address" required>
        <button type="submit" class="btn">Reset Password</button>
    </form>

    <a href="login.php" class="back-btn">← Back to Login</a>
</div>

</body>
</html>
