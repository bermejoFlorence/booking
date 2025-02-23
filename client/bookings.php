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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    <title>Settings</title>

    <?php
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

$bookingData = $database->query("
    SELECT 
        b.*,
        p.receipt_no,  
        p.transac_num, 
        p.amt_payment, 
        p.payment_status, 
        p.reference_no
    FROM booking AS b
    LEFT JOIN payment AS p ON b.booking_id = p.booking_id
    WHERE b.client_id = '$userid' AND b.is_deleted = 0
    ORDER BY b.booking_id DESC
");


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
        }

        .table-container {
            width: 100%;
            display: flex;
            justify-content: center;
            overflow-x: auto;
        }

        .table {
            border: 1px solid #ccc;
            margin-top: 20px;
            margin-right: 20px;
            width: 100%;
            max-width: 1200px;
            min-width: 320px;
            border-collapse: collapse;
            display: flex;
            justify-content: center; /* Center-align table horizontally */
            align-items: center; /* Center-align vertically kung may fixed height */
        }

        .table th, .table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            word-wrap: break-word;
            border-bottom: 1px solid #ddd;
            vertical-align: middle; /* Center-align text vertically */
            text-align: center; /* Centers text horizontally */
        }

        .table th {
            background-color: #f4f4f4;
        }

        
/* Modal Overlay */
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Modal Popup */
.popup {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    width: 600px; /* Medium size */
    max-width: 90%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s ease-in-out;
    position: relative;
}

/* Modal Header */
.modal-header h2 {
    margin: 0;
    font-size: 24px;
    color: #333;
    text-align: center;
    border-bottom: 2px solid #f4f4f4;
    padding-bottom: 10px;
}

/* Close Button */
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    color: #888;
}
.close:hover {
    color: #333;
}

/* Modal Content */
.modal-content {
    margin-top: 20px;
}

.section h3 {
    font-size: 18px;
    color: #007BFF;
    margin-bottom: 15px;
    text-align: center;
}

/* Info Row for Key-Value Pair */
.info-row {
    display: flex;
    justify-content: space-between;
    margin: 8px 0;
}

.info-row span:first-child {
    font-weight: bold;
    color: #555;
    flex: 1; /* Key column width */
    text-align: right;
    margin-right: 10px;
}

.info-row span:last-child {
    flex: 2; /* Value column width */
    text-align: left;
    color: #333;
}

/* Divider */
hr {
    border: 0;
    border-top: 1px solid #f4f4f4;
    margin: 15px 0;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}



       /* Fix column sizes */
    .table .col-number {
        width: 50px; /* # column */
    }

    .table .col-name {
        width: 200px; /* Full Name */
    }

    .table .col-event {
        width: 200px; /* Event */
    }

    .table .col-address {
        width: 300px; /* Address */
    }

    .table .col-status {
        width: 150px; /* Status */
    }

    /* Status colors */
    .status-pending {
        color: white;
        background-color: orange;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .print-btn {
        display: inline-block;
        margin-left: 10px;
        cursor: pointer;
    }

    .checkout-btn {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.checkout-btn:hover {
    background-color: #218838;
}

.print-btn {
    cursor: pointer;
}

.print-btn img {
    transition: transform 0.2s ease-in-out;
}

.print-btn:hover img {
    transform: scale(1.2);
}
.heading-main12 {
        font-size: 20px;
        color: rgb(49, 49, 49);
        margin: 0;
    }


        /* Responsive Design */
        @media (max-width: 1024px) {
            .dashboard-items {
                width: 80%;
                flex-direction: row;
                justify-content: center;
            }

            .settings-title {
                font-size: 1.8rem;
            }

            .table {
            font-size: 14px;
            
        }
        

        .btn-primary-soft {
            font-size: 12px;
        }
        }

        @media (max-width: 768px) {
            .settings-title {
                font-size: 1.5rem;
            }

            .table {
                font-size: 12px;
            }

            .table img {
                width: 40px;
            }

            .dashboard-items {
                width: 90%;
            }

            .btn-primary-soft {
            font-size: 10px;
            }
            .table th, .table td {
                font-size: 8px;
                padding: 4px;
            }
        }

        @media (max-width: 480px) {
            .settings-title {
                font-size: 1.2rem;
            }
                .table-container {
                display: block;
                overflow-x: auto;
            }
            table {
                display: block;
                width: 100%;
                overflow-x: auto;
            }


            .table thead {
        display: none; /* Hide table headers */
    }

    .table tr {
        display: block;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
    }

    .table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px;
        border: none;
        font-size: 12px;
    }

    .table td:before {
        content: attr(data-label);
        flex-basis: 50%;
        font-weight: bold;
        text-align: left;
        padding-right: 10px;
    }

            .btn-primary-soft {
                font-size: 12px;
                padding: 5px 8px;
            }
        }
        .center-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Depende sa gusto mong taas */
    width: 100%;
    position: relative; /* Siguraduhin na may relative positioning ito */
}

@media screen and (max-width: 768px) {
    .header {
        font-size: 5px;
        padding: 10px;
    }
}

@media screen and (max-width: 480px) {
    .header {
        font-size: 3px;
        padding: 8px;
    }
}
.btn {
    padding: 10px 20px;
    margin-top: 20px;
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
    
    <div class="center-container">

        <div class="menu">
            <div class="close-btn" onclick="toggleMenu()">✖</div>
            
            <table class="menu-container" border="0">
                 <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="User Image">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo $username; ?></p>
                                    <p class="profile-subtitle"><?php echo $useremail; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button onclick="showLogoutModal()" class="logout-btn btn-primary-soft btn">Log out</button>
                                </td>
                            </tr>

                    </table>
                     <!-- Logout Confirmation Modal -->
                        <div id="logoutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000; transition: opacity 0.3s;">
                            <div id="logoutModalContent" style="background: white; padding: 30px; border-radius: 12px; text-align: center; width: 400px; transform: scale(0); transition: transform 0.3s ease-in-out;">
                                <p id="logoutModalMessage" style="font-size: 18px; margin-bottom: 20px;">Are you sure you want to log out?</p>
                                <button id="logoutConfirmBtn" onclick="logoutUser()" style="background-color:rgb(39, 134, 211); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 10px; font-size: 16px;">Confirm</button>
                                <button onclick="closeLogoutModal()" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">Cancel</button>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
             
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appointment-active">
                        <a href="bookings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>
            
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-feedback">
                        <a href="feedback.php" class="non-style-link-menu"><div><p class="menu-text">Feedback</p></a></div>
                    </td>
                </tr>



                <!-- <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr> -->
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
        <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
         
                <tr >
                <tr>
                    <td colspan="4" style="padding-top:30px; display: flex; justify-content: space-between; align-items: center;">
                    <p class="heading-main12" style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px;">&nbsp;&nbsp;BOOKING DETAILS</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top: 15px; text-align: left; font-size: 14px; color:grey;">
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong>Note:</strong> If the status is <strong>"pending"</strong>, you can still cancel your booking or proceed to checkout. 
                        If the status is <strong>"processing"</strong>, please wait for 5 minutes to confirm your booking. 
                        If the status is <strong>"approved"</strong>, you can now print the receipt.
                    </td>
                </tr>


                </tr>
               
                <tr>
                    
                   <td colspan="4">
                   &nbsp;&nbsp;
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                            <tr>
                                <th class="table-headin" >#</th>
                                <th class="table-headin">Full Name</th>
                                <th class="table-headin">Date of Event</th>
                                <th class="table-headin">Event</th>
                                <th class="table-headin">Package</th>
                                <th class="table-headin">Address of Event</th>
                                <th class="table-headin">Status</th>
                                <th class="table-headin"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($bookingData && $bookingData->num_rows > 0) {
                                $counter = 1;
                                while ($row = $bookingData->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='col-number' style='text-align: center; vertical-align: middle;'>" . $counter++ . "</td>";
                                    echo "<td class='col-name' style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($username) . "</td>";
                                    echo "<td class='col-event' style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['date_event']) . "</td>";
                                    echo "<td class='col-event' style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['event']) . "</td>";
                                    echo "<td class='col-package' style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['package']) . "</td>";
                                    echo "<td class='col-address' style='text-align: center; vertical-align: middle;'>" . htmlspecialchars($row['address_event']) . "</td>";

                                    // Set status with background color
                                    $statusClass = '';
                                    $statusText = htmlspecialchars($row['stat']);
                                    if ($row['stat'] === 'pending') {
                                        $statusClass = 'background-color: orange; color: white;';
                                    } elseif ($row['stat'] === 'processing') {
                                        $statusClass = 'background-color: blue; color: white;';
                                    } elseif ($row['stat'] === 'approved') {
                                        $statusClass = 'background-color: green; color: white;';
                                    }
                                    elseif ($row['stat'] === 'rejected') {
                                        $statusClass = 'background-color: red; color: white;';
                                    }
                                    elseif ($row['stat'] === 'cancelled') {
                                        $statusClass = 'background-color: #8B0000; color: white;';
                                        
                                    }

                                    // Display the status with background color
                                    echo "<td class='col-status' style='text-align: center; vertical-align: middle; font-size: 0.9em; $statusClass'>";
                                    echo $statusText;
                                    echo "</td>";

                                    // Add Checkout, View Details, or Printer icon based on the status
                                    echo "<td class='col-action' style='text-align: center; vertical-align: middle;'>";
                                    if ($row['stat'] === 'pending') {
                                        // Checkout button
                                        echo "<button class='checkout-btn' style='padding: 5px 10px; border: none; background-color: #007bff; color: white; border-radius: 3px; cursor: pointer; margin-right: 5px;' onclick=\"showConfirmationModal('checkout', '" . htmlspecialchars($row['booking_id']) . "')\">Checkout</button>";
                                    
                                        // Cancel button
                                        echo "<button class='cancel-btn' style='padding: 5px 10px; border: none; background-color: red; color: white; border-radius: 3px; cursor: pointer;' onclick=\"showConfirmationModal('cancel', '" . htmlspecialchars($row['booking_id']) . "')\">Cancel</button>";

                                    }
                                     elseif ($row['stat'] === 'processing') {
                                        echo "<button class='details-btn' style='padding: 5px 10px; border: none; background-color: #ffc107; color: #fff; border-radius: 3px; cursor: pointer;' 
                                                onclick=\"viewDetails(
                                                    '" . htmlspecialchars($row['booking_id']) . "', 
                                                    '" . htmlspecialchars($row['package']) . "',
                                                    '" . htmlspecialchars($row['price']) . "',
                                                    '" . htmlspecialchars($row['event']) . "',
                                                    '" . htmlspecialchars($row['date_event']) . "',
                                                    '" . htmlspecialchars($row['address_event']) . "',
                                                    '" . htmlspecialchars($row['transac_num']) . "',
                                                    '" . htmlspecialchars($row['amt_payment']) . "',
                                                    '" . htmlspecialchars($row['payment_status']) . "',
                                                    '" . htmlspecialchars($row['reference_no']) . "'
                                                )\">View Details</button>";

                                    
                                    } elseif ($row['stat'] === 'approved') {
                                        echo "<button class='details-btn' style='padding: 5px 10px; border: none; background-color: #ffc107; color: #fff; border-radius: 3px; cursor: pointer; margin-right: 5px;' 
                                        onclick=\"viewDetails(
                                            '" . htmlspecialchars($row['booking_id']) . "', 
                                            '" . htmlspecialchars($row['package']) . "',
                                            '" . htmlspecialchars($row['price']) . "',
                                            '" . htmlspecialchars($row['event']) . "',
                                            '" . htmlspecialchars($row['date_event']) . "',
                                            '" . htmlspecialchars($row['address_event']) . "',
                                            '" . htmlspecialchars($row['transac_num']) . "',
                                            '" . htmlspecialchars($row['amt_payment']) . "',
                                            '" . htmlspecialchars($row['payment_status']) . "',
                                            '" . htmlspecialchars($row['reference_no']) . "'
                                        )\">View Details</button>";
                                        
                                        echo "<span class='print-btn' onclick=\"printInvoice(
                                            '" . htmlspecialchars($row['receipt_no']) . "',
                                            '" . htmlspecialchars($row['transac_num']) . "',
                                            '" . htmlspecialchars($row['amt_payment']) . "',
                                            '" . htmlspecialchars($row['payment_status']) . "',
                                            '" . htmlspecialchars($row['package']) . "',
                                            '" . htmlspecialchars($row['price']) . "',
                                            '" . htmlspecialchars($row['event']) . "'
                                        )\">";
                                        echo "<img src='../img/icons/printer.png' alt='Print Icon' width='20' style='cursor: pointer;'>";
                                        echo "</span>";
                                    } elseif ($row['stat'] === 'cancelled') {
                                        // Delete button
                                        echo "<button class='delete-btn' style='padding: 5px 10px; border: none; background-color: red; color: white; border-radius: 3px; cursor: pointer;' onclick=\"showConfirmationModal('delete', '" . htmlspecialchars($row['booking_id']) . "')\">Delete</button>";
                                    }
                                    echo "</td>";

                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align: center; vertical-align: middle;'>No bookings found.</td></tr>";
                            }
                            ?>
                        </tbody>
                        <div id="viewDetailsModal" class="overlay" style="display: none;">
                            <div class="popup medium">
                                <span class="close" onclick="closeModal();">&times;</span>
                                <div class="modal-header">
                                    <h2>Booking Details</h2>
                                </div>
                                <div class="modal-content">
                                    <div class="section">
                                        <h3>Payment Information</h3>
                                        <div class="info-row">
                                            <span>Transaction No.:</span> 
                                            <span id="modal-transac-num"></span>
                                        </div>
                                        <div class="info-row">
                                            <span>Amount Paid:</span> 
                                            <span id="modal-amt-payment"></span>
                                        </div>
                                        <div class="info-row">
                                            <span>Payment Status:</span> 
                                            <span id="modal-payment-status"></span>
                                        </div>
                                        <div class="info-row">
                                            <span>Reference Number:</span> 
                                            <span id="modal-reference-no"></span>
                                        </div>
                                        <div class="info-row" id="balance-row" style="display: none;">
                                            <span>Balance:</span> 
                                            <span id="modal-balance"></span>
                                        </div>

                                    </div>
                                    <hr>
                                    <div class="section">
                                        <h3>Booking Information</h3>
                                        <div class="info-row">
                                            <span>Package:</span> 
                                            <span id="modal-package"></span>
                                        </div>
                                        <div class="info-row">
                                            <span>Price:</span> 
                                            <span id="modal-price"></span>
                                        </div>
                                        <div class="info-row">
                                            <span>Event:</span> 
                                            <span id="modal-event"></span>
                                        </div>
                                        <div class="info-row">
                                            <span>Event Date:</span> 
                                            <span id="modal-event-date"></span>
                                        </div>
                                        <div class="info-row">
                                            <span>Event Address:</span> 
                                            <span id="modal-event-address"></span>
                                        </div>
                                        <!-- Update Payment Button (Initially Hidden) -->
                                            <button id="update-payment-btn" style="display: none; margin-top: 10px;" onclick="updatePayment()">
                                                Update Payment
                                            </button>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="confirmationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000; transition: opacity 0.3s;">
                            <div id="modalContent" style="background: white; padding: 30px; border-radius: 12px; text-align: center; width: 400px; transform: scale(0); transition: transform 0.3s ease-in-out;">
                                <p id="modalMessage" style="font-size: 18px; margin-bottom: 20px;"></p>
                                <button id="confirmBtn" style="background-color:rgb(39, 134, 211); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 10px; font-size: 16px;">Confirm</button>
                                <button onclick="closeConfirmationModal()" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">Cancel</button>
                            </div>
                        </div>


        </table> 


                        </div>
                        </center>
                   </td> 
                </tr>
        </table>
        </div>
    </div>

    <script>
             function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('open');
        }

        updatePaymentBtn.onclick = function() {
    console.log("Update Payment button clicked!"); // Debugging step
    console.log("Booking ID before redirect:", bookingId); // Siguraduhin may value
    
    if (bookingId) {
        let redirectURL = `update_payment.php?booking_id=${bookingId}`;
        console.log("Redirecting to:", redirectURL); // I-check ang final URL
        window.location.href = redirectURL;
    } else {
        alert("Error: No Booking ID found!");
    }
};


function closeModal() {
    // Hide the modal
    document.getElementById('viewDetailsModal').style.display = 'none';
}


function showConfirmationModal(action, bookingId) {
    let modal = document.getElementById("confirmationModal");
    let modalContent = document.getElementById("modalContent");
    let modalMessage = document.getElementById("modalMessage");
    let confirmBtn = document.getElementById("confirmBtn");

    modal.style.display = "flex"; // Ipakita ang modal
    setTimeout(() => {
        modalContent.style.transform = "scale(1)"; // Zoom-in effect
    }, 50);

    if (action === 'checkout') {
        modalMessage.textContent = 'Are you sure you want to proceed with checkout?';
        confirmBtn.style.backgroundColor = '#007bff';
        confirmBtn.onclick = function () {
            window.location.href = 'payment.php?booking_id=' + bookingId;
        };
    } else if (action === 'cancel') {
        modalMessage.textContent = 'Are you sure you want to cancel this booking?';
        confirmBtn.style.backgroundColor = '#007bff';
        confirmBtn.onclick = function () {
            fetch('cancel_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'booking_id=' + bookingId
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Cancelled',
                        text: 'The booking has been successfully cancelled!',
                        confirmButtonColor: '#007bff',
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error cancelling booking. Please try again.',
                        confirmButtonColor: '#dc3545',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonColor: '#dc3545',
                });
            });
        };
    } else if (action === 'delete') {
        modalMessage.textContent = 'Are you sure you want to delete this booking?';
        confirmBtn.style.backgroundColor = '#007bff';
        confirmBtn.onclick = function () {
            fetch('delete_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'booking_id=' + bookingId
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Deleted',
                        text: 'Your booking has been removed!',
                        confirmButtonColor: '#007bff',
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error removing booking. Please try again.',
                        confirmButtonColor: '#dc3545',
                    });
                }
            });

            closeConfirmationModal();
        };
    }
}

function printInvoice(receiptNo, transactionNum, amountPayment, paymentStatus, packageName, price, eventName) {
    // Gumawa ng hidden div para sa printing
    let printArea = document.createElement('div');
    printArea.innerHTML = `
        <style>
            body {
                font-family: Arial, sans-serif;
                width: 8.5in;
                height: 11in;
                padding: 20px;
            }
            .invoice-container {
                width: 100%;
                border: 1px solid black;
                padding: 20px;
                box-sizing: border-box;
                text-align: center;
            }
            h1 {
                text-align: center;
                margin-bottom: 30px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            table, th, td {
                border: 1px solid black;
            }
            th, td {
                padding: 10px;
                text-align: left;
            }
            .no-border {
                border: none !important;
            }
        </style>

        <div class="invoice-container">
            
             <h1>EXZPHOTOGRAPHY STUDIO</h1>
            <h3>SALES INVOICE</h3>
            <table>
                <tr>
                    <th>Receipt No:</th>
                    <td>${receiptNo}</td>
                </tr>
                <tr>
                    <th>Transaction No:</th>
                    <td>${transactionNum}</td>
                </tr>
                <tr>
                    <th>Event:</th>
                    <td>${eventName}</td>
                </tr>
                <tr>
                    <th>Package:</th>
                    <td>${packageName}</td>
                </tr>
                <tr>
                    <th>Price:</th>
                    <td>₱${parseFloat(price).toLocaleString()}</td>
                </tr>
                <tr>
                    <th>Amount Paid:</th>
                    <td>₱${parseFloat(amountPayment).toLocaleString()}</td>
                </tr>
                <tr>
                    <th>Payment Status:</th>
                    <td>${paymentStatus}</td>
                </tr>
            </table>
        </div>
    `;

    // Itago ang lahat ng ibang content sa page
    let originalBody = document.body.innerHTML;
    document.body.innerHTML = printArea.innerHTML;

    // Mag-trigger ng print dialog
    window.print();

    // Ibalik ang original na content pagkatapos ng printing
    document.body.innerHTML = originalBody;

    function updateBookingStatus(bookingId, status) {
    // Use AJAX to send a request to update the booking status in the database
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_booking_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            alert(xhr.responseText); // Show success message
            location.reload(); // Reload the page to update the table
        } else {
            alert('An error occurred. Please try again.');
        }
    };
    xhr.send(`booking_id=${bookingId}&status=${status}`);
}

}
function viewDetails(bookingId, package, price, event, eventDate, eventAddress, transacNum, amtPayment, paymentStatus, referenceNo) {
    // Set booking details in modal
    document.getElementById('modal-package').textContent = package;
    document.getElementById('modal-price').textContent = price;
    document.getElementById('modal-event').textContent = event;
    document.getElementById('modal-event-date').textContent = eventDate;
    document.getElementById('modal-event-address').textContent = eventAddress;

    // Set transaction details
    document.getElementById('modal-transac-num').textContent = transacNum || 'N/A';
    document.getElementById('modal-amt-payment').textContent = `₱${amtPayment}`;

    // Compute balance
    let balanceElement = document.getElementById('modal-balance');
    let updateButton = document.getElementById('update-payment-btn'); // Get button element

    let cleanedPrice = parseFloat(price.replace(/,/g, '')) || 0;
    let cleanedAmtPayment = parseFloat(amtPayment) || 0;
    let balance = cleanedPrice - cleanedAmtPayment;

    if (paymentStatus.trim() === 'Partial Paid') {
        balanceElement.textContent = `₱${balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        balanceElement.parentElement.style.display = 'flex'; 
        updateButton.style.display = 'block'; 
    } else {
        balanceElement.parentElement.style.display = 'none'; 
        updateButton.style.display = 'none'; 
    }

    // Modify payment status display
    if (paymentStatus.trim() === 'Fully Paid') {
        document.getElementById('modal-payment-status').textContent = 'Gcash';
        document.getElementById('modal-reference-no').textContent = referenceNo || 'N/A';
    } else if (paymentStatus.trim() === 'No Payment') {
        document.getElementById('modal-payment-status').textContent = 'Walk-In';
        document.getElementById('modal-reference-no').textContent = 'N/A';
    } else {
        document.getElementById('modal-payment-status').textContent = paymentStatus || 'N/A';
        document.getElementById('modal-reference-no').textContent = referenceNo || 'N/A';
    }

    // Update button function with bookingId, transactionNumber, package, and balance
    updateButton.onclick = function () {
        updatePayment(bookingId, transacNum, package, balance);
    };

    // Show the modal
    document.getElementById('viewDetailsModal').style.display = 'block';
}

// Function to redirect to update_payment.php with parameters
function updatePayment(bookingId, transacNum, package, balance) {
    window.location.href = `update_payment.php?booking_id=${bookingId}&transac_num=${transacNum}&package=${encodeURIComponent(package)}&balance=${balance}`;
}




function closeConfirmationModal() {
    let modal = document.getElementById("confirmationModal");
    let modalContent = document.getElementById("modalContent");

    modalContent.style.transform = "scale(0)"; // Zoom-out effect
    setTimeout(() => {
        modal.style.display = "none"; // Itago ang modal pagkatapos ng 300ms
    }, 300);
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

</script>

</body>
</html>