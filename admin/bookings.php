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
    <!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales-all.min.js"></script>

        
    <title>Dashboard</title>
</head>
<body>
<?php
// Simula ng PHP code
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION["usertype"] != "a") {
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

// Import database connection
include("../connection.php");

$user_id = $_SESSION["user"];
$query = $database->prepare("SELECT emp_email FROM employee WHERE emp_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_email = $row["emp_email"];
} else {
    $user_email = "Unknown Email";
}

// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

$records_per_page = 10; // Number of records per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page, default to 1
$offset = ($current_page - 1) * $records_per_page; // Calculate the offset

// Query to count total records
$total_records_query = "SELECT COUNT(*) AS total FROM booking WHERE stat != 'pending'";
$total_records_result = $database->query($total_records_query);
$total_records = $total_records_result->fetch_assoc()['total'];

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);

// Query to fetch data with LIMIT and OFFSET
$sql = "SELECT 
            b.booking_id, 
            c.c_fullname AS full_name, 
            b.date_event, 
            b.event, 
            b.address_event, 
            b.package, 
            b.price,
            b.stat,  
            p.receipt_no,
            p.transac_num,
            p.amt_payment,
            p.payment_status as payment_status,
            p.reference_no as reference_no
        FROM 
            booking b 
        LEFT JOIN 
            client c ON b.client_id = c.client_id 
        LEFT JOIN 
            payment p ON b.booking_id = p.booking_id 
        WHERE 
            b.stat != 'pending' 
        ORDER BY 
            b.date_event DESC
        LIMIT $records_per_page OFFSET $offset";

$result = $database->query($sql);?>

   <style>
           .dash-body {
    margin: 30px auto; /* Adds top margin and centers horizontally */
    padding: 0 20px; /* Adds inner padding */
    width: 90%; /* Set width to allow centering with auto margin */
    margin-top: 80px; /* I-adjust ayon sa taas ng header */
    margin-left: 250px; /* I-adjust ayon sa lapad ng sidebar */
}

    .header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px; /* Space sa pagitan ng header at table */
    flex-wrap: wrap;
    margin-top: 7%; /* Space sa taas ng header */
    
}

    .heading-main12 {
        font-size: 20px;
        color: rgb(49, 49, 49);
        margin: 0;
    }

    .search-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    justify-content: flex-end;
    width: 100%;
    margin-top: 10px; /* Space sa taas ng search at reset button */
}

.header-searchbar {
    flex: 1;
    min-width: 200px;
    max-width: 400px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-top: 10px; /* Space sa taas ng search input */
}


.login-btn {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    margin-top: 10px; /* Space sa taas ng mga button */
}

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        text-decoration: none;
    }

    .table-container {
        margin-top: 20px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .sub-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sub-table th,
    .sub-table td {
        text-align: left;
        padding: 10px;
        border: 1px solid #ddd;
    }

    .sub-table th {
        background-color: #f2f2f2;
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
    

    @media screen and (max-width: 1024px) {
        .header-section {
            flex-direction: column;
            align-items: center;
        }

        .search-container {
            justify-content: center;
        }

        .heading-main12 {
            font-size: 18px;
            margin-bottom: 10px;
        }
    }

    @media screen and (max-width: 768px) {
    .container {
        flex-direction: column; /* Gawing vertical ang layout sa maliit na screen */
        height: auto; /* Hayaan itong mag-adjust */
    }

    .menu {
        width: 100%; /* Buong lapad sa maliit na screen */
        height: auto; /* Hayaan itong mag-adjust */
        border-right: none;
        box-shadow: none;
    }

    .dash-body {
        width: 100%; /* Buong lapad sa maliit na screen */
        height: auto;
        margin: 10px auto;
        padding: 0 10px; /* Binawasan ang padding */
    }

    .header-section {
        flex-direction: column;
        align-items: center;
        margin-bottom: 10px; /* Mas maliit na agwat */
    }

    .heading-main12 {
        font-size: 18px;
        margin-bottom: 5px;
    }

    .search-container {
        flex-direction: row; /* Isang linya lang */
        justify-content: center;
        gap: 5px;
        width: 100%;
    }

    .header-searchbar {
        width: auto;
        flex: 1;
        max-width: 60%;
    }

    .login-btn {
        padding: 8px 12px;
        font-size: 14px;
    }
    .header {
        font-size: 5px;
        padding: 10px;
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
@media screen and (max-width: 480px) {
    .dash-body {
        margin: 5px auto;
        padding: 0 5px;
    }

    .search-container {
        flex-direction: row;
        justify-content: center;
        gap: 3px;
        width: 100%;
    }

    .header-searchbar {
        width: auto;
        flex: 1;
        max-width: 65%;
    }

    .login-btn {
        font-size: 12px;
        padding: 6px 8px;
    }
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
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="User Image">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle"><?php echo $user_email; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button onclick="showLogoutModal()" class="logout-btn btn-primary-soft btn">Log out</button>
                                </td>
                            </tr>   
                        </table>
                        <div id="logoutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000; transition: opacity 0.3s;">
                                <div id="logoutModalContent" style="background: white; padding: 30px; border-radius: 12px; text-align: center; width: 400px; transform: scale(0); transition: transform 0.3s ease-in-out;">
                                    <p id="logoutModalMessage" style="font-size: 18px; margin-bottom: 20px;">Are you sure you want to log out?</p>
                                    <button id="logoutConfirmBtn" onclick="logoutUser()" style="background-color:rgb(39, 134, 211); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 10px; font-size: 16px;">Confirm</button>
                                    <button onclick="closeLogoutModal()" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">Cancel</button>
                                </div>
                            </div> 
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu">
                            <div><p class="menu-text">Home</p></div>
                        </a>
                    </td>
                </tr>
                
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appointment-active">
                        <a href="bookings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Bookings</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-message">
                        <a href="report.php" class="non-style-link-menu"><div><p class="menu-text">Reports</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-feedback">
                        <a href="feedback.php" class="non-style-link-menu"><div><p class="menu-text">Feedback</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-sales">
                        <a href="moving_average.php" class="non-style-link-menu"><div><p class="menu-text">Sales</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu">
                            <div><p class="menu-text">Settings</p></div>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="dash-body" style="margin-top: 15px;">
        
    <div class="header-section">
        <p class="heading-main12" style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px;">BOOKING DETAILS</p>
        <form action="" method="post" class="header-search">
            <div class="search-container">
                <input type="search" name="search" class="input-text header-searchbar" 
                    placeholder="Type Client Name or Event" 
                    value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>" />
                <button type="submit" class="login-btn btn-primary btn">Search</button>
                <a href="bookings.php" class="login-btn btn-secondary btn">Reset</a>
            </div>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
        $search = $database->real_escape_string($_POST['search']);
        $sql = "SELECT 
                    b.booking_id, 
                    c.c_fullname AS full_name, 
                    b.date_event, 
                    b.event, 
                    b.address_event, 
                    p.payment_status AS mode_of_payment, 
                    p.amt_payment
                FROM 
                    booking b 
                LEFT JOIN 
                    client c ON b.client_id = c.client_id 
                LEFT JOIN 
                    payment p ON b.booking_id = p.booking_id 
                WHERE 
                    (c.c_fullname LIKE '%$search%' OR 
                    b.event LIKE '%$search%') AND 
                    b.stat != 'pending'
                ORDER BY 
                    b.date_event DESC";

        $result = $database->query($sql);

        if ($result->num_rows > 0) {
            if (!empty($search)) {
                echo "<p>Search results for '<strong>$search</strong>':</p>";
            }
        } else {
            if (!empty($search)) {
                echo "<p>No results found for '<strong>$search</strong>'.</p>";
            } else {
                echo "<p>No records available.</p>";
            }
        }
    }
    ?>

    <div class="table-container">
        <div class="table-responsive">
        <table class="sub-table" style="width: 100%; border-collapse: collapse; text-align: center;">

            <thead>
                <tr>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">#</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Full Name</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Date of Event</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Event</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Payment Status</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Reference No.</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Amount</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Action</th>
                </tr>
            </thead>

            <tbody>
    <?php
    if ($result->num_rows > 0) {
        $counter = $offset + 1; // Start the numbering from the correct offset
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$counter}</td>";
            echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['full_name']}</td>";
            echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['date_event']}</td>";
            echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['event']}</td>";
            echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['payment_status']}</td>";
            echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$row['reference_no']}</td>";
            echo "<td style='padding: 10px; border-bottom: 1px solid #ddd;'>₱{$row['amt_payment']}.00</td>";

            // **Condition para sa Status ng Booking**
            if ($row['stat'] == 'approved') {
                echo "<td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>
                        <button style='background-color: rgb(77, 224, 126); color: white; border: none; padding: 5px 10px; border-radius: 4px;' disabled>Approved</button>
                        <button style='background-color: #007bff; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;' 
                            onclick=\"printBooking(
                                '{$row['booking_id']}', 
                                '{$row['receipt_no']}', 
                                '{$row['transac_num']}', 
                                '{$row['amt_payment']}',  
                                '{$row['reference_no']}', 
                                '{$row['package']}', 
                                '{$row['price']}', 
                                '{$row['event']}', 
                                '{$row['date_event']}', 
                                '{$row['address_event']}'
                            )\">
                            View Receipt
                        </button>
                    </td>";
            } elseif ($row['stat'] == 'rejected') {
                echo "<td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>
                        <button style='background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px;' disabled>Rejected</button>
                    </td>";
            } else {
                echo "<td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>
                        <button style='background-color: rgb(98, 54, 246); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;' 
                            onclick=\"openModal('accept', {$row['booking_id']});\">Accept</button>
                        <button style='background-color: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;' 
                            onclick=\"openModal('reject', {$row['booking_id']});\">Reject</button>
                    </td>";
            }

            echo "</tr>";
            $counter++;
        }
    } else {
        echo "<tr><td colspan='8' style='padding: 10px; text-align: center;'>No bookings available.</td></tr>";
    }
    ?>
</tbody>


                <div id="confirmationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000; transition: opacity 0.3s;">
                <div id="modalContent" style="background: white; padding: 30px; border-radius: 12px; text-align: center; width: 400px; transform: scale(0); transition: transform 0.3s ease-in-out;">
                    <p id="modalMessage" style="font-size: 18px; margin-bottom: 20px;"></p>
                    <button id="confirmBtn" style="background-color:rgb(39, 134, 211); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 10px; font-size: 16px;">Confirm</button>
                    <button onclick="closeConfirmationModal()" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">Cancel</button>
                </div>
                </div>

                <div id="viewReceiptModal" class="overlay" style="display: none;">
                    <div class="popup medium">
                        <span class="close" onclick="closeModal();">&times;</span>
                        <div class="modal-header">
                            <h2>Sale Invoice Details</h2>
                        </div>
                        <div class="modal-content">
                            <div class="section">
                                <h3>Payment Information</h3>
                                <div class="info-row">
                                    <span>Receipt No.:</span> 
                                    <span id="modal-receipt-num"></span>
                                </div>
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
                            </div>
                        </div>
                    </div>
                </div>
        </table>

        <div style="text-align: center; margin-top: 20px;">
    <?php if ($current_page > 1): ?>
        <a href="?page=<?php echo $current_page - 1; ?>" style="margin-right: 10px;">&laquo; Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" style="margin: 0 5px; <?php echo ($i == $current_page) ? 'font-weight: bold; color: blue;' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?php echo $current_page + 1; ?>" style="margin-left: 10px;">Next &raquo;</a>
    <?php endif; ?>
        </div>


        </div>
    </div>
   
</div>

    </div>
    <script>
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('open');
        }

       function openModal(action, bookingId) {
    const modal = document.getElementById('confirmationModal');
    const modalContent = document.getElementById('modalContent');
    const modalMessage = document.getElementById('modalMessage');
    const confirmBtn = document.getElementById('confirmBtn');

    // Set the modal message and button style based on action
    if (action === 'accept') {
        modalMessage.textContent = 'Are you sure you want to accept this booking?';
        confirmBtn.style.backgroundColor = '#007bff';
        confirmBtn.onclick = function () {
            // Call the function to update the booking status
            updateBookingStatus(bookingId, 'approved');
            closeModal();
        };
    } else if (action === 'reject') {
        modalMessage.textContent = 'Are you sure you want to reject this booking?';
        confirmBtn.style.backgroundColor = '#007bff';
        confirmBtn.onclick = function () {
            updateBookingStatus(bookingId, 'rejected');
            closeModal();
        };
    }

    // Show the modal with animation
    modal.style.display = 'flex';
    setTimeout(() => {
        modalContent.style.transform = 'scale(1)';
    }, 10); // Trigger the animation after displaying the modal
}

function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    const modalContent = document.getElementById('modalContent');

    // Hide the modal with animation
    modalContent.style.transform = 'scale(0)';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}


function updateBookingStatus(bookingId, status) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_booking_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            const responseMessage = xhr.responseText.trim();

            Swal.fire({
                title: 'Success!',
                text: responseMessage, // Gumamit ng response mula sa PHP
                icon: status === 'approved' ? 'success' : 'warning', // Icon depende sa status
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // Mag-reload ng page pagkatapos mag-click ng "OK"
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred. Please try again.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    };
    xhr.send(`booking_id=${bookingId}&status=${status}`);
}

function printBooking(bookingId, receiptNo, transacNum, amtPayment, paymentStatus, referenceNo, packageName, price, event, eventDate, eventAddress) {
    var modal = document.getElementById("viewReceiptModal");

    // Ipakita ang modal
    modal.style.display = "block";

    // Update modal contents gamit ang tamang data
    document.getElementById("modal-receipt-num").innerText = receiptNo;
    document.getElementById("modal-transac-num").innerText = transacNum;
    document.getElementById("modal-amt-payment").innerText = "₱" + amtPayment + ".00";
    document.getElementById("modal-payment-status").innerText = paymentStatus;
    document.getElementById("modal-reference-no").innerText = referenceNo;
    
    document.getElementById("modal-package").innerText = packageName;
    document.getElementById("modal-price").innerText = "₱" + price + ".00";
    document.getElementById("modal-event").innerText = event;
    document.getElementById("modal-event-date").innerText = eventDate;
    document.getElementById("modal-event-address").innerText = eventAddress;
}

function closeModal() {
    document.getElementById("viewReceiptModal").style.display = "none";
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
