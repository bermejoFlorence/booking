<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <!-- <link rel="stylesheet" href="css/login.css"> -->
    <title>Login</title>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<style>
        /* === Base Styles === */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 35%;
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

        .form-body {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-label {
            font-size: 1rem;
            text-align: left;
            width: 100%;
            display: block;
            margin-top: 1rem;
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

        .link-container {
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .link-container a {
            color: #007bff;
            text-decoration: none;
        }

        .link-container a:hover {
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

    </style>
<?php
session_start();
session_unset();
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

include("connection.php");

$error = '<label for="promter" class="form-label">&nbsp;</label>';
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['useremail']);
    $password = trim($_POST['userpassword']);

    $result = $database->prepare("SELECT * FROM webuser WHERE email = ?");
    $result->bind_param("s", $email);
    $result->execute();
    $userResult = $result->get_result();

    if ($userResult->num_rows === 1) {
        $user = $userResult->fetch_assoc(); 
        $utype = $user['usertype'];//comment nito kjung saan

        if ($utype === 'p') { // Client Login
            $checker = $database->prepare("SELECT * FROM client WHERE c_email = ?");
            $checker->bind_param("s", $email);
            $checker->execute();
            $clientResult = $checker->get_result();
        
            if ($clientResult->num_rows === 1) {
                $client = $clientResult->fetch_assoc();
        
                if (password_verify($password, $client['c_password'])) {
                    $_SESSION['user'] = $client['c_email'];
                    $_SESSION['usertype'] = 'p';
                    $success = "client/index.php"; // Redirect path for JS
                } else {
                    $error = '<label class="form-label" style="color:red;text-align:center;">Invalid email or password</label>';
                }
            } else {
                $error = '<label class="form-label" style="color:red;text-align:center;">Invalid email or password</label>';
            }
        } elseif ($utype === 'a') { // Admin/Employee Login
            $checker = $database->prepare("SELECT * FROM employee WHERE emp_email = ?");
            $checker->bind_param("s", $email);
            $checker->execute();
            $employeeResult = $checker->get_result();
        
            if ($employeeResult->num_rows === 1) {
                $employee = $employeeResult->fetch_assoc();
        
                // Ginamit ang password_verify para ma-check ang hashed password
                if (password_verify($password, $employee['emp_password'])) {
                    $_SESSION['user'] = $employee['emp_id'];
                    $_SESSION['usertype'] = 'a';
                    $success = "admin/index.php"; // Redirect path for JS
                } else {
                    $error = '<label class="form-label" style="color:red;text-align:center;">Invalid email or password</label>';
                }
            } else {
                $error = '<label class="form-label" style="color:red;text-align:center;">Invalid email or password</label>';
            }
        } else {
            $error = '<label class="form-label" style="color:red;text-align:center;">User type not recognized</label>';
        }
    } else {
        $error = '<label class="form-label" style="color:red;text-align:center;">Email not found</label>';
    }
}
?>

<div class="container">
    <p class="header-text">Welcome Back!</p>
    <p class="sub-text">Login with your details to continue</p>

    <form action="" method="POST" class="form-body">
        <label for="useremail" class="form-label">Email:</label>
        <input type="email" name="useremail" class="input-text" placeholder="Email Address" required>

        <label for="userpassword" class="form-label">Password:</label>
        <input type="password" name="userpassword" class="input-text" placeholder="Password" required>

        <input type="submit" value="Login" class="btn">
    </form>
    <td>
                        <?php echo $error; ?>
                    </td>
    <div class="link-container">
        <p>Don't have an account? <a href="create-account.php">Sign Up</a></p>
        <p>Forgot Password? <a href="forgot_password.php">Recover Account</a></p>
    </div>
    <a href="index.php" class="back-btn">← Back to Home</a>
</div>

<!-- JavaScript for SweetAlert -->
<script>
    <?php if (!empty($success)): ?>
        Swal.fire({
            title: "Login Successful!",
            text: "Redirecting...",
            icon: "success",
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            window.location.href = "<?php echo $success; ?>";
        });
    <?php endif; ?>
</script>

</body>
</html>
