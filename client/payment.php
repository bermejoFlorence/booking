
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="style.css">
    <title>Payment</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=AQ1Pyn7z1m2wr7cSMsa5hcXrO1s3aA2bYdgSVEmbVgmkVx6mEBwp99A5PXBR3o33_5M_XgRJTxkDT_ja&currency=PHP"></script>
   
    
    <?php
    echo "<script>console.log('Received booking_id: " . $booking_id . "');</script>";
session_start();

if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

// Fetch client details
$userrow = $database->query("SELECT * FROM client WHERE c_email='$useremail'");

if ($userrow && $userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["client_id"];
    $username = $userfetch["c_fullname"];
} else {
    session_unset();
    session_destroy();
    header("location: ../login.php");
    exit();
}

// Check if booking_id is set in the URL
if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // Fetch booking details
    $query = "SELECT package, price FROM booking WHERE booking_id = ? AND client_id = ?";
    $stmt = $database->prepare($query);
    $stmt->bind_param("ii", $booking_id, $userid); // Bind client ID to ensure security
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        $package = $booking['package'];
        $price = $booking['price'];

        echo "<script>console.log('Package: " . $package . " | Price: " . $price . "');</script>";

    } else {
        echo "Booking not found or unauthorized access!";
        exit();
    }
} else {
    echo "No booking ID provided!";
    exit();
}
?>
</head>
<body>
<style>
          .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-X  0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }

        .settings-title {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 20px 0;
        }

        .dashboard-items {
            padding: 20px;
            margin: 10px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            text-align: center;
        }


    .form-container {
        max-width: 600px; /* Slightly larger form */
        width: 100%;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        padding: 25px 35px;
        margin-left:auto;
        margin-right: auto;
        margin-bottom: auto;
        margin-top: 80px;
    }

        .form-container h2 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #333;
        }

        .details-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }

        .details-container span {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .details-container span strong {
            font-weight: bold;
            color: #000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 15px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        .btn-primary {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #46B1C9;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            margin-top: 15px;
        }

        .btn-primary:hover {
            background-color:#4da0e0;
        }

        .hidden {
    display: none;
}

        /* Responsive styling */
        @media (max-width: 768px) {
            .form-container {
                padding: 20px 25px;
            }

            label {
                font-size: 14px;
            }

            input[type="text"], input[type="number"], select {
                font-size: 14px;
            }

            .btn-primary {
                font-size: 15px;
            }

            .details-container {
                font-size: 15px;
            } 
             .form-container {
 
        margin-left:auto;
        margin-right: auto;
        margin-bottom: auto;
        margin-top: 80px;
    }
        }


        @media screen and (max-width: 480px) {
    .header {
        font-size: 3px;
        padding: 8px;
    }
    
}

.btn {
   
   background:#46B1C9; ;
   color: #fff;
   text-decoration: none;
   border-radius: 5px;
   cursor: pointer;
   border: none;
}
.btn:hover {
   background: #4da0e0 ;
}



</style>

<div class="header">
    <h1>EXZPHOTOGRAPHY STUDIO</h1>
</div>
<div class="hamburger" onclick="toggleMenu()">
            ☰
        </div>
    <div class="container">
    <div class="menu">
    <div class="close-btn" onclick="toggleMenu()">✖</div>

    <div class="menu-container">

        <!-- Profile Section -->
        <div class="profile-container">
            <img src="../img/user.png" alt="User Image">
            <p class="profile-title"><?php echo $username; ?></p>
            <p class="profile-subtitle"><?php echo $useremail; ?></p>
            <button onclick="showLogoutModal()" class="logout-btn btn-primary-soft btn">Log out</button>
        </div>

        <!-- Sidebar Links -->
        <a href="index.php" class="menu-btn menu-icon-home">
            <p class="menu-text">Home</p>
        </a>

        <a href="bookings.php" class="menu-btn menu-icon-appoinment  menu-active">
            <p class="menu-text">My Bookings</p>
        </a>

        <a href="feedback.php" class="menu-btn menu-icon-feedback">
            <p class="menu-text">Feedback</p>
        </a>

        <a href="profile_set.php" class="menu-btn menu-icon-settings">
            <p class="menu-text">Profile Settings</p>
        </a>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000; transition: opacity 0.3s;">
        <div id="logoutModalContent" style="background: white; padding: 30px; border-radius: 12px; text-align: center; width: 400px; transform: scale(0); transition: transform 0.3s ease-in-out;">
            <p id="logoutModalMessage" style="font-size: 18px; margin-bottom: 20px;">Are you sure you want to log out?</p>
            <button id="logoutConfirmBtn" onclick="logoutUser()" style="background-color:rgb(39, 134, 211); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 10px; font-size: 16px;">Confirm</button>
            <button onclick="closeLogoutModal()" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">Cancel</button>
        </div>
    </div>

</div>
        <div class="dash-body" style="margin-top: 15px">
    <table border="0" width="100%" style="border-spacing: 0; margin: 0; padding: 0; margin-top: 25px;">
        <div class="form-container">
            <h2>Payment Details</h2>

            <!-- Transaction Details -->
            <div class="details-container">
                <span><strong>Package:</strong> <?php echo htmlspecialchars($package); ?></span>
                <span><strong>Price:</strong> <?php echo htmlspecialchars($price); ?></span>
            </div>

            <!-- GCash Form -->
            <form method="POST" action="save_payment.php" id="paymentForm">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
                <input type="hidden" name="payment_method" value="gcash">

                    <div id="gcash-fields">
                    <div class="form-group">
                            <label for="gcash_qr">Scan QR Code</label>
                            <div>
                                <img src="gcash.jpg" alt="GCash QR Code" style="width: 200px; height: auto;">
                            </div>

                            <!-- ✅ Download Button -->
                            <div style="margin-top: 10px;">
                                <a href="gcash.jpg" download="GCash_QR_Code.jpg" 
                                    style="display: inline-block; padding: 8px 16px; background-color: #224D98; color: white; text-decoration: none; border-radius: 6px; font-size: 14px;">
                                    Download QR Code
                                </a>
                            </div>

                        <div class="form-group">
                            <label for="reference_no">Reference Number</label>
                            <input type="text" name="reference_no" id="reference_no" placeholder="Enter GCash reference number"
                                maxlength="13" pattern="\d{13}" required onkeypress="return event.charCode>=48 && event.charCode<=57">
                            <small id="error-message" style="color: red; display: none;">Reference number must be exactly 13 digits.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="amt_payment">Amount to Pay</label>
                            <input type="text" name="amt_payment" id="amt_payment" required
                                onkeypress="return event.charCode>=48 && event.charCode<=57"
                                placeholder="Enter amount ex 1000, 100, 10, 1">
                        </div>
                    </div>

                <button type="submit" class="btn-primary">Submit Payment</button>
            </form>

            <hr>
            <h3 style="text-align:center;">OR</h3>

            <!-- PayPal Smart Button -->
            <div id="paypal-button-container" style="margin-top: 20px;"></div>
        </div>
    </table>
</div>

    </div>

  
    <script>
             function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('open');
        }

        function togglePaymentFields() {
    var paymentStatus = document.getElementById("payment_status").value;
    var walkinMessage = document.getElementById("walkin-message");
    var gcashFields = document.getElementById("gcash-fields");
    var referenceNo = document.getElementById("reference_no");

    if (paymentStatus === "walk-in") {
        walkinMessage.style.display = "block";
        gcashFields.style.display = "none";

        // Remove required attributes for GCash
        referenceNo.removeAttribute("required");
    } else if (paymentStatus === "through gcash") {
        walkinMessage.style.display = "none";
        gcashFields.style.display = "block";

        // Make reference number required
        referenceNo.setAttribute("required", "true");
    } else {
        walkinMessage.style.display = "none";
        gcashFields.style.display = "none";
    }
}


function showLogoutModal() {
        let modal = document.getElementById("logoutModal");
        let modalContent = document.getElementById("logoutModalContent");
        modal.style.display = "flex";
        setTimeout(() => {
            modalContent.style.transform = "scale(1)";
        }, 50);
    }

    function closeLogoutModal() {
        let modalContent = document.getElementById("logoutModalContent");
        modalContent.style.transform = "scale(0)";
        setTimeout(() => {
            document.getElementById("logoutModal").style.display = "none";
        }, 300);
    }

    function logoutUser() {
        window.location.href = "../logout.php"; // Redirect to logout page
    }

      // GCash Input Validation
      document.getElementById("reference_no").addEventListener("input", function () {
        var referenceNo = this.value;
        var errorMessage = document.getElementById("error-message");
        errorMessage.style.display = /^\d{13}$/.test(referenceNo) ? "none" : "block";
    });

    document.getElementById("paymentForm").addEventListener("submit", function (e) {
        const priceStr = "<?php echo str_replace(',', '', $price); ?>";
        const inputAmtStr = document.getElementById("amt_payment").value;
        const cleanedPrice = parseFloat(priceStr) || 0;
        const cleanedInput = parseFloat(inputAmtStr.replace(/,/g, '')) || 0;

        if (cleanedInput > cleanedPrice) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Overpayment Detected',
                text: `You entered ₱${cleanedInput.toLocaleString()} but the required amount is only ₱${cleanedPrice.toLocaleString()}.`,
                confirmButtonColor: '#dc3545',
            });
        }
    });

    // PayPal Smart Button
    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo str_replace(",", "", $price); ?>'
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                fetch('save_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        booking_id: '<?php echo $booking_id; ?>',
                        payment_method: 'paypal',
                        reference_no: details.id,
                        payer_name: details.payer.name.given_name + ' ' + details.payer.name.surname,
                        amt_payment: details.purchase_units[0].amount.value
                    })
                }).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful',
                        text: `Thank you, ${details.payer.name.given_name}! Your PayPal payment was processed.`,
                    }).then(() => {
                        window.location.href = 'bookings.php';
                    });
                });
            });
        }
    }).render('#paypal-button-container');


    </script>

</body>
</html>