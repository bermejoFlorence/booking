<?php
include("connection.php");

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check kung valid ang token sa webuser table
    $stmt = $database->prepare("SELECT * FROM webuser WHERE reset_token = ? AND reset_expiry > NOW()");
    if (!$stmt) {
        die("Prepare failed: " . $database->error);
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $email = $user['email']; 
        $usertype = $user['usertype']; // Kunin ang usertype (p = client, a = admin)
    } else {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid or Expired Token!',
                    text: 'Please request a new password reset link.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'forgot_password.php';
                });
            });
        </script>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email']; 
    $usertype = $_POST['usertype']; // Ipadala ang usertype sa form

    if ($usertype === 'p') {
        // Update password sa client table
        $update_client = $database->prepare("UPDATE client SET c_password = ? WHERE c_email = ?");
        if (!$update_client) {
            die("Prepare failed (client table): " . $database->error);
        }
        $update_client->bind_param("ss", $new_password, $email);
        $update_client->execute();
    } elseif ($usertype === 'a') {
        // Update password sa employee table para sa admin
        $update_admin = $database->prepare("UPDATE employee SET emp_password = ? WHERE emp_email = ?");
        if (!$update_admin) {
            die("Prepare failed (employee table): " . $database->error);
        }
        $update_admin->bind_param("ss", $new_password, $email);
        $update_admin->execute();
    }

    // I-clear ang reset token sa webuser table
    $clear_token = $database->prepare("UPDATE webuser SET reset_token = NULL, reset_expiry = NULL WHERE email = ?");
    if (!$clear_token) {
        die("Prepare failed (webuser table): " . $database->error);
    }
    $clear_token->bind_param("s", $email);
    $clear_token->execute();

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Password Reset Successful!',
                text: 'You can now log in with your new password.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'login.php';
            });
        });
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

        <button type="submit" class="btn">Change Password</button>
    </form>

    <a href="login.php" class="back-btn">← Back to Login</a>
</div>

</body>
</html>

