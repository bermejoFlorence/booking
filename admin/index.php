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
    <!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core/locales-all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



        
    <title>Dashboard</title>
</head>
<body>
<?php
session_start();

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"] == "" || $_SESSION["usertype"] != "a") {
        header("location: ../login.php");
    }
} else {
    header("location: ../login.php");
}

// Import database
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

// Get total bookings for the current day
$currentDate = date('Y-m-d');
$queryBookings = $database->prepare("SELECT COUNT(*) AS total_bookings FROM booking WHERE DATE(date_created) = ?");
$queryBookings->bind_param("s", $currentDate);
$queryBookings->execute();
$resultBookings = $queryBookings->get_result();
$rowBookings = $resultBookings->fetch_assoc();
$totalBookings = $rowBookings['total_bookings'];


// Get total messages received today
$queryMessages = $database->prepare("SELECT COUNT(*) AS total_messages FROM contact_info WHERE DATE(date_created) = ?");
$queryMessages->bind_param("s", $currentDate);
$queryMessages->execute();
$resultMessages = $queryMessages->get_result();
$rowMessages = $resultMessages->fetch_assoc();
$totalMessages = $rowMessages['total_messages'];


// Fetch booked dates and their events from the database
// Fetch approved booked dates and their events from the database
$queryEvents = $database->prepare("SELECT date_event, event FROM booking WHERE stat = 'approved'");
$queryEvents->execute();
$resultEvents = $queryEvents->get_result();

$bookedDates = [];
while ($rowEvent = $resultEvents->fetch_assoc()) {
    $bookedDates[] = [
        'title' => $rowEvent['event'], // Display the event name from the database
        'start' => $rowEvent['date_event'],
        'color' => '#4da0e0' // Make the date green
    ];
}

// Convert approved booked dates to JSON for FullCalendar
$bookedDatesJSON = json_encode($bookedDates);
$queryPending = $database->prepare("SELECT COUNT(*) AS pending_count FROM booking WHERE stat = 'pending'");
$queryPending->execute();
$resultPending = $queryPending->get_result();
$rowPending = $resultPending->fetch_assoc();

// Store the count of pending bookings in a variable
$pendingCount = $rowPending['pending_count'];

$queryPending = $database->prepare("SELECT COUNT(*) AS total_pending FROM booking WHERE DATE(date_created) = ? AND stat = 'pending'");
$queryPending->bind_param("s", $currentDate);
$queryPending->execute();
$resultPending = $queryPending->get_result();
$rowPending = $resultPending->fetch_assoc();
$pendingCount = $rowPending['total_pending'];

// Get total in-process bookings for the current day
$queryInProcess = $database->prepare("SELECT COUNT(*) AS total_in_process FROM booking WHERE stat = 'processing'");
$queryInProcess->execute();
$resultInProcess = $queryInProcess->get_result();
$rowInProcess = $resultInProcess->fetch_assoc();
$inProcessCount = $rowInProcess['total_in_process'];

$queryFeedback = $database->prepare("SELECT rating FROM feedback");
$queryFeedback->execute();
$resultFeedback = $queryFeedback->get_result();

// Initialize counters
$goodCount = 0;
$neutralCount = 0;
$badCount = 0;

while ($rowFeedback = $resultFeedback->fetch_assoc()) {
    $rating = $rowFeedback['rating'];
    
    if ($rating === 'Very Satisfied' || $rating === 'Satisfied') {
        $goodCount++;
    } elseif ($rating === 'Neutral') {
        $neutralCount++;
    } elseif ($rating === 'Dissatisfied' || $rating === 'Very Dissatisfied') {
        $badCount++;
    }
}

?>
    <style>

.profile-subtitle {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    max-width: 125px; /* Adjust to fit within the sidebar */
}
        .dash-body {
            margin: 30px auto; /* Adds top margin and centers horizontally */
    padding: 0 20px; /* Adds inner padding */
    width: 90%; /* Set width to allow centering with auto margin */
    margin-top: 80px; /* I-adjust ayon sa taas ng header */
    margin-left: 250px; /* I-adjust ayon sa lapad ng sidebar */
}
        .dashboard-container {
    display: flex;
    justify-content: space-between; /* Distribute items evenly */
    gap: 20px;
    margin: 20px auto;
    padding: 20px;
    max-width: 1200px;
    flex-wrap: nowrap; /* Prevent stacking */
    margin-top: 4%;
}

.dashboard-items {
    text-decoration: none; /* Remove underline */
    color: inherit; /* Maintain text color */
    display: flex;
    align-items: center; /* Center-align vertically */
    justify-content: space-between; /* Space between icon and content */
    padding: 20px;
    width: 30%; /* Ensure each item takes up 30% of the row */
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.dashboard-items:hover {
    transform: scale(1.05);
    box-shadow: 0px 8px 12px rgba(0, 0, 0, 0.2);
}

.dashboard-items .content {
    display: flex;
    flex-direction: column; /* Align number and label vertically */
    justify-content: center; /* Vertically center-align content */
    text-align: left; /* Align text to the left */
}

.h1-dashboard {
    font-size: 36px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.h3-dashboard {
    font-size: 18px;
    font-weight: bold;
    color: #555;
    margin: 5px 0 0 0;
}

.dashboard-icons {
    width: 60px;
    height: 60px;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    margin-left: 20px; /* Add spacing between icon and content */
}


/* Responsive Design */
@media screen and (max-width: 768px) {
    .dashboard-container {
        flex-wrap: wrap; /* Stack items on smaller screens */
        gap: 10px;
    }

    .dashboard-items {
        width: 100%; /* Full width on small screens */
        padding: 15px;
    }

    .h1-dashboard {
        font-size: 24px; /* Adjust font size for smaller screens */
    }

    .h3-dashboard {
        font-size: 14px; /* Adjust font size for smaller screens */
    }

}

.calendar-container {
    flex: 1;
    max-width: 40%; /* Bawasan ang lapad ng calendar */
    height: 100px;
    
}

#calendar {
    border: 20pxrgb(50, 52, 60);/* Itim na border *
    border-radius: 10px; /* Para medyo rounded ang kanto */
    padding: 20px;

}
.fc-button {
    background-color:#46B1C9 !important; /* Blue background */
    color: white !important; /* White text */
    border: none !important;
    border-radius: 5px !important;
    padding: 5px 10px !important;
}

.fc-button:hover {
    background-color: #0056b3 !important; /* Darker blue on hover */
}

.fc-prev-button, .fc-next-button {
    background-color:#46B1C9 !important; /* Dark gray background */
    color: white !important; /* White text */
}

.fc-prev-button:hover, .fc-next-button:hover {
    background-color:#46B1C9 !important; /* Darker gray on hover */
}


@media screen and (max-width: 768px) {
    .calendar-container {
        max-width: 90%;
    }

    #calendar {
        height: calc(100vh - 150px); /* Adjust height for smaller screens */
    }
      .header {
        font-size: 5px;
        padding: 10px;
    }
}

        /* Responsive Design */
        @media screen and (max-width: 768px) {
    .container {
        flex-direction: column; /* Stack the menu and dashboard content vertically */
        height: auto; /* Remove fixed height */
    }

    .menu {
        width: 100%; /* Full width for the sidebar on small screens */
        height: auto; /* Remove fixed height */
        border-right: none; /* Remove border on small screens */
        box-shadow: none; /* Remove shadow */
    }

    .dash-body {
        width: 100%; /* Full width to center dashboard items */
        height: auto; /* Remove fixed height */
        margin: auto;
    }

    .dashboard-container {
        flex-wrap: wrap; /* Stack items on smaller screens */
        gap: 10px;
    }

    .dashboard-items {
        width: 100%; /* Full width on small screens */
        padding: 15px;
    }

    .dashboard-items .content {
        text-align: center; /* Center text for small screens */
    }

    .dashboard-icons {
        margin-left: 0; /* Remove spacing on small screens */
    }

    .h1-dashboard {
        font-size: 24px; /* Adjust font size for smaller screens */
    }

    .h3-dashboard {
        font-size: 14px; /* Adjust font size for smaller screens */
    }
    .header {
        font-size: 5px;
        padding: 10px;
    }
    .dashboard-flex {
        flex-direction: column; /* Gawing column para mag-stack */
        align-items: center; /* I-center ang content */
    }

    .calendar-container,
    .chart-wrapper {
        max-width: 100%; /* Full width para di masikip */
       margin:auto;
    }
}

@media screen and (max-width: 480px) {
    .header {
        font-size: 3px;
        padding: 8px;
    }
}

.chart-wrapper {
    width: 90%;
    max-width: 600px;
    margin: auto;
    text-align: center;
    padding: 15px;
}

.chart-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
    background: #fff;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}


.dashboard-flex {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    flex-wrap: wrap; /* Para mag-stack sa maliit na screen */
}

/* Default size (Large Screens) */
.calendar-container,
.chart-wrapper {
    flex: 1;
    max-width: 50%; /* Hinati ang space (50% bawat isa) */
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
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home menu-active menu-icon-home-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active">
                            <div><p class="menu-text">Home</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="bookings.php" class="non-style-link-menu"><div><p class="menu-text">Bookings</p></a></div>
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
        <table class="filter-container" style="border: none;" border="0">
        <div class="dashboard-container"> 
    <!-- New Bookings -->
            <a href="bookings.php" class="dashboard-items">
                <div class="content">
                    <div class="h1-dashboard"><?php echo $pendingCount; ?></div> <br>
                    <div class="h3-dashboard">Today's New Bookings</div>
                </div>
                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/book-hover.svg');"></div>
            </a>

            <a href="bookings.php" class="dashboard-items">
                <div class="content">
                    <div class="h1-dashboard"><?php echo $inProcessCount; ?></div> <br>
                    <div class="h3-dashboard">No. of In-Process</div>
                </div>
                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/process.png');"></div>
            </a>

            <!-- Clients -->
            <a href="report.php" class="dashboard-items">
                <div class="content">
                    <div class="h1-dashboard"><?php echo $totalMessages; ?></div> <br>
                    <div class="h3-dashboard">Message from Clients Today</div>
                </div>
                <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/message-icon.png');"></div>
            </a>
        </div>

        <div class="dashboard-flex">
            <div class="calendar-container">
                <h2 style="text-align: center; font-size: 24px; margin-bottom: 20px; color: #4a4e69;">APPROVED SCHEDULE</h2>
                <div id="calendar"></div>
            </div>

            <div class="chart-wrapper">
                <h3 class="chart-title" style="color: #4a4e69;">User Feedback Overview</h3>
                <div class="chart-container">
                    <canvas id="feedbackChart"></canvas>
                </div>
            </div>
        </div>    
        </table>
        </div>
    </div>
    <script>
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('open');
        }

        document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'en',
            timeZone: 'Asia/Manila',
            events: <?php echo $bookedDatesJSON; ?>, // Use PHP variable with event titles
            aspectRatio: 1.5,
            height: 'auto',
            contentHeight: 'auto',
        });

        calendar.render();
    });

    document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('feedbackChart').getContext('2d');

    var feedbackChart = new Chart(ctx, {
        type: 'bar', 
        data: {
            labels: ['Good', 'Neutral', 'Bad'],
            datasets: [{
                label: 'Feedback Ratings',
                data: [<?php echo $goodCount; ?>, <?php echo $neutralCount; ?>, <?php echo $badCount; ?>],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderColor: ['#218838', '#d39e00', '#c82333'],
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
});
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
