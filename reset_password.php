<?php
require 'connection.php'; // Ensure this file connects to the database session_start();

if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: forgot_password.php");
    exit();
}

$token = $_GET['token'];

$stmt = $database->prepare("SELECT email, usertype FROM webuser WHERE reset_token = ? AND reset_expiry > NOW() LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid or Expired Token!',
            text: 'The reset link has expired or is invalid.',
            confirmButtonText: 'OK'
        }).then(() => { window.location.href = 'forgot_password.php'; });
    </script>";
    exit();
}

$row = $result->fetch_assoc();
$email = $row['email'];
$usertype = $row['usertype'];
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Passwords do not match!',
                text: 'Please enter the same password in both fields.',
                confirmButtonText: 'OK'
            });
        </script>";
        exit();
    }

    if (strlen($password) < 8) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Password Too Short!',
                text: 'Your password must be at least 8 characters long.',
                confirmButtonText: 'OK'
            });
        </script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    if ($usertype === 'client') {
        $update_stmt = $database->prepare("UPDATE client SET password = ? WHERE email = ?");
    } else {
        $update_stmt = $database->prepare("UPDATE admin SET password = ? WHERE email = ?");
    }
    $update_stmt->bind_param("ss", $hashed_password, $email);
    $update_stmt->execute();
    $update_stmt->close();

    // Clear the reset token
    $clear_token_stmt = $database->prepare("UPDATE webuser SET reset_token = NULL, reset_expiry = NULL WHERE email = ?");
    $clear_token_stmt->bind_param("s", $email);
    $clear_token_stmt->execute();
    $clear_token_stmt->close();
    $database->close();

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Password Reset Successfully!',
            text: 'You can now log in with your new password.',
            confirmButtonText: 'OK'
        }).then(() => { window.location.href = 'login.php'; });
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* === Base Styles === */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
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

        label {
            display: block;
            text-align: left;
            font-size: 1rem;
            margin: 1rem 0 0.5rem;
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
            margin-top: 1.5rem;
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

            label {
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

            label {
                font-size: 0.85rem;
            }

            .btn {
                font-size: 0.85rem;
                padding: 0.7rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <p class="header-text">Reset Password</p>
    <p class="sub-text">Enter your new password below</p>

    <form action="" method="POST">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="usertype" value="<?= htmlspecialchars($usertype) ?>">

        <label>Enter New Password:</label>
        <input type="password" name="password" class="input-text" required>

        <label>Confirm New Password:</label>
        <input type="password" name="confirm_password" class="input-text" required>

        <button type="submit" class="btn">Change Password</button>
    </form>

    <a href="login.php" class="back-btn">← Back to Login</a>
</div>

</body>
</html>

