<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/signup.css">
    <title>Create Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            animation: transitionIn-X 0.5s;
        }

        .header-text {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group label {
            width: 100%;
            font-size: 14px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #007BFF;
            outline: none;
        }

        .form-group-half {
            flex: 1;
            min-width: 48%;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            width: 48%;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #007BFF;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ccc;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .sub-text {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .sub-text a {
            color: #007BFF;
            text-decoration: none;
        }

        .sub-text a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: rgb(255, 62, 62);
            text-align: center;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .form-group-half {
                min-width: 100%;
            }
        }

        .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-wrapper .form-control {
        flex: 1;
        padding-right: 35px;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        cursor: pointer;
        color: #888;
    }

    .password-toggle:hover {
        color: #007BFF;
    }

      
    </style>
</head>
<body>
<?php
session_start();
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

date_default_timezone_set('Asia/Manila');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

include("connection.php");

$error = '';
// saving to database
if ($_POST) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $fullname = $fname . " " . $lname;
    $email = $_POST['newemail'];
    $tele = $_POST['tele'];
    $address = $_POST['address'];
    $newpassword = $_POST['newpassword'];
    $cpassword = $_POST['cpassword'];

    // Check if passwords match
    if ($newpassword == $cpassword) {
        // Check if emp_id 1 exists in employee table
        $empCheckResult = $database->query("SELECT emp_id FROM employee WHERE emp_id = 1;");
        if ($empCheckResult && $empCheckResult->num_rows == 0) {
            $error = '<div class="error-message">Error: emp_id 1 does not exist in the employee table.</div>';
        } else {
            $result = $database->query("SELECT * FROM client WHERE c_email='$email';");

            if (!$result) {
                $error = '<div class="error-message">Error checking email in client: ' . $database->error . '</div>';
            } else if ($result->num_rows == 1) {
                $error = '<div class="error-message">Already have an account for this Email address in client table.</div>';
            } else {
                $webuserResult = $database->query("SELECT * FROM webuser WHERE email='$email';");

                if (!$webuserResult) {
                    $error = '<div class="error-message">Error checking email in webuser: ' . $database->error . '</div>';
                } else if ($webuserResult->num_rows == 1) {
                    $error = '<div class="error-message">Already have an account for this Email address in webuser table.</div>';
                } else {
                    // Encrypt the password before saving it to the database
                    $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);

                    // Insert into client table with emp_id = 1
                    $queryClient = "INSERT INTO client (c_fullname, c_email, c_contactnum, c_address, c_password, emp_id, date_created) 
                                    VALUES ('$fullname', '$email', '$tele', '$address', '$hashedPassword', 1, NOW());";

                    $queryWebuser = "INSERT INTO webuser (email, usertype) VALUES ('$email', 'p');";

                    if ($database->query($queryClient) && $database->query($queryWebuser)) {
                        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
                        echo '<script>
                            Swal.fire({
                                title: "Success!",
                                text: "Account created successfully. Please login.",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "login.php";
                            });
                        </script>';
                        exit();
                    }
                     else {
                        $error = '<div class="error-message">Error saving data: ' . $database->error . '</div>';
                    }
                }
            }
        }
    } else {
        $error = '<div class="error-message">Password Confirmation Error! Reconfirm Password.</div>';
    }
}
?>

<!-- form for creating account -->
<div class="container">
    <p class="header-text">Let’s Get Started</p>

    <form action="" method="POST">
        <div class="form-group">
            <div class="form-group-half">
                <label for="fname">First Name:</label>
                <input type="text" name="fname" class="form-control" placeholder="First Name" required>
            </div>
            <div class="form-group-half">
                <label for="lname">Last Name:</label>
                <input type="text" name="lname" class="form-control" placeholder="Last Name" required>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group-half">
                <label for="newemail">Email:</label>
                <input type="email" name="newemail" class="form-control" placeholder="Email Address" required>
            </div>
            <div class="form-group-half">
                <label for="tele">Mobile Number:</label>
                <input type="tel" name="tele" class="form-control" placeholder="ex: 09123456789" 
                    pattern="^09[0-9]{9}$" maxlength="11" required
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" name="address" class="form-control" placeholder="Address" required>
        </div>

        
        <div class="form-group">
            <label for="newpassword">Create New Password:</label>
                <input type="password" name="newpassword" id="newpassword" class="form-control" placeholder="New Password" 
                    minlength="8" pattern=".{8,}" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('newpassword', this)"></i>
        </div>
        <div class="form-group">
            <label for="cpassword">Confirm Password:</label>
            
                <input type="password" name="cpassword" id="cpassword" class="form-control" placeholder="Confirm Password" 
                    minlength="8" pattern=".{8,}" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('cpassword', this)"></i>
        </div>

        <?php echo $error; ?>

        <div class="btn-container">
            <input type="reset" value="Reset" class="btn btn-secondary">
            <input type="submit" value="Sign Up" class="btn btn-primary">
        </div>

        <p class="sub-text">Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>

<script>
    function togglePassword(fieldId, icon) {
        let field = document.getElementById(fieldId);
        if (field.type === "password") {
            field.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            field.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
</body>

</html>
