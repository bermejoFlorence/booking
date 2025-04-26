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

date_default_timezone_set('Asia/Manila');
$currentDate = date('Y-m-d');

$queryBookings = $database->prepare("SELECT COUNT(*) AS total_bookings FROM booking WHERE DATE(date_created) = ?");
$queryBookings->bind_param("s", $currentDate);
$queryBookings->execute();
$resultBookings = $queryBookings->get_result();
$rowBookings = $resultBookings->fetch_assoc();
$totalBookings = $rowBookings['total_bookings'];

$queryMessages = $database->prepare("SELECT COUNT(*) AS total_messages FROM contact_info WHERE DATE(date_created) = ?");
$queryMessages->bind_param("s", $currentDate);
$queryMessages->execute();
$resultMessages = $queryMessages->get_result();
$rowMessages = $resultMessages->fetch_assoc();
$totalMessages = $rowMessages['total_messages'];

$queryEvents = $database->prepare("SELECT date_event, event FROM booking WHERE stat = 'approved'");
$queryEvents->execute();
$resultEvents = $queryEvents->get_result();

$bookedDates = [];
while ($rowEvent = $resultEvents->fetch_assoc()) {
    $bookedDates[] = [
        'title' => $rowEvent['event'],
        'start' => $rowEvent['date_event'],
        'color' => '#4da0e0'
    ];
}
$bookedDatesJSON = json_encode($bookedDates);

$queryPending = $database->prepare("SELECT COUNT(*) AS total_pending FROM booking WHERE DATE(date_created) = ? AND stat = 'pending'");
$queryPending->bind_param("s", $currentDate);
$queryPending->execute();
$resultPending = $queryPending->get_result();
$rowPending = $resultPending->fetch_assoc();
$pendingCount = $rowPending['total_pending'];

$queryInProcess = $database->prepare("SELECT COUNT(*) AS total_in_process FROM booking WHERE stat = 'processing'");
$queryInProcess->execute();
$resultInProcess = $queryInProcess->get_result();
$rowInProcess = $resultInProcess->fetch_assoc();
$inProcessCount = $rowInProcess['total_in_process'];

$ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$queryRating = $database->prepare("SELECT rating FROM feedback");
$queryRating->execute();
$resultRating = $queryRating->get_result();

while ($row = $resultRating->fetch_assoc()) {
    switch ($row['rating']) {
        case 'Very Satisfied': $ratingCounts[5]++; break;
        case 'Satisfied': $ratingCounts[4]++; break;
        case 'Neutral': $ratingCounts[3]++; break;
        case 'Dissatisfied': $ratingCounts[2]++; break;
        case 'Very Dissatisfied': $ratingCounts[1]++; break;
    }
}

$querySentiment = $database->prepare("SELECT sentiment FROM feedback");
$querySentiment->execute();
$resultSentiment = $querySentiment->get_result();

$sentimentCounts = ['good' => 0, 'neutral' => 0, 'bad' => 0];

while ($row = $resultSentiment->fetch_assoc()) {
    $sentiment = strtolower($row['sentiment']);
    if (isset($sentimentCounts[$sentiment])) {
        $sentimentCounts[$sentiment]++;
    }
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$filter = '';
if (isset($_POST['filter_option'])) {
    $filter = $_POST['filter_option'];
} elseif (isset($_GET['filter_option'])) {
    $filter = $_GET['filter_option'];
}

$baseQuery = "";
if ($filter === "daily") {
    $today = date('Y-m-d');
    $baseQuery = "WHERE DATE(date) = '$today'";
} elseif ($filter === "weekly") {
    $today = date('Y-m-d');
    $last7Days = date('Y-m-d', strtotime('-6 days'));
    $baseQuery = "WHERE DATE(date) BETWEEN '$last7Days' AND '$today'";
} elseif ($filter === "monthly") {
    $currentYear = date('Y');
    $currentMonth = date('m');
    $baseQuery = "WHERE MONTH(date) = '$currentMonth' AND YEAR(date) = '$currentYear'";
} elseif ($filter === "yearly") {
    $baseQuery = "";
}

if ($filter === "yearly") {
    $query = "SELECT YEAR(date) AS sales_date, SUM(total_sales) AS total_amount FROM sales $baseQuery GROUP BY YEAR(date) ORDER BY sales_date DESC LIMIT $start, $limit";
    $countQuery = "SELECT COUNT(DISTINCT YEAR(date)) as total FROM sales $baseQuery";
} else {
    $query = "SELECT DATE(date) AS sales_date, SUM(total_sales) AS total_amount FROM sales $baseQuery GROUP BY DATE(date) ORDER BY sales_date DESC LIMIT $start, $limit";
    $countQuery = "SELECT COUNT(DISTINCT DATE(date)) as total FROM sales $baseQuery";
}

$result = $database->query($query);
$countResult = $database->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
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
    

.table-responsive::-webkit-scrollbar {
    height: 8px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background-color: #aaa;
    border-radius: 4px;
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
    max-width: 50%; /* Ginawang 50% para mas maayos ang alignment */
    height: auto; /* Iwasan ang fixed height, hayaan itong mag-adjust */
}

#calendar {
    border-radius: 10px; /* Rounded edges */
    padding: 20px;
    min-height: 300px; /* Minimum height para maiwasan ang overlapping */
}

.fc-button {
    background-color:rgba(21, 86, 238, 0.85) !important; /* Blue background */
    color: white !important; /* White text */
    border: none !important;
    border-radius: 5px !important;
    padding: 5px 10px !important;
}

.fc-button:hover {
    background-color: #0056b3 !important; /* Darker blue on hover */
}

.fc-prev-button, .fc-next-button {
    background-color:#2EAF7D !important; /* Dark gray background */
    color: white !important; /* White text */
}

.fc-prev-button:hover, .fc-next-button:hover {
    background-color:#2EAF7D !important; /* Darker gray on hover */
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
.dashboard-layout {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    align-items: flex-start;
    margin-top: 20px;
}

.calendar-panel {
    flex: 1 1 40%;
    min-width: 300px;
}

.chart-panel {
    flex: 1 1 55%;
    display: flex;
    flex-direction: column;
    gap: 20px;
    min-width: 320px;
}

.section-title, .chart-title {
    font-size: 18px;
    color: #4a4e69;
    text-align: center;
    margin-bottom: 10px;
}

.chart-wrapper {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 15px 20px;
    max-height: 360px;
    overflow: hidden;
}

.chart-container {
    position: relative;
    width: 100%;
    height: 250px;
}

canvas {
    width: 100% !important;
    height: 100% !important;
    max-height: 250px;
}

@media screen and (max-width: 768px) {
    .dashboard-layout {
        flex-direction: column;
    }
    .calendar-panel, .chart-panel {
        width: 100%;
    }
    .chart-wrapper {
        max-height: none;
    }
}

.menu-container {
  width: 100%;
}

.menu-row {
  padding: 12px 20px;
  display: flex;
  align-items: center;
  transition: background 0.2s ease;
}

.menu-row:hover {
  background-color: #d9edf7;
  cursor: pointer;
}

.menu-btn {
  display: flex;
  align-items: center;
  width: 100%;
}

.menu-btn i,
.menu-btn img,
.menu-btn svg {
  margin-right: 12px;
  font-size: 20px;
}

.menu-text {
  font-size: 16px;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Profile container adjustments */
.profile-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}

.profile-container img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
}

.profile-title {
  font-weight: bold;
  margin-bottom: 4px;
  font-size: 16px;
  text-align: center;
}

.profile-subtitle {
  font-size: 14px;
  color: #555;
  text-align: center;
  word-break: break-word;
  max-width: 150px;
}

/* Logout button */
.logout-btn {
  margin-top: 10px;
  width: 100%;
  text-align: center;
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

        <div class="dashboard-layout">
    <!-- Left: Calendar -->
    <div class="calendar-panel">
        <h2 class="section-title">APPROVED SCHEDULE</h2>
        <div id="calendar"></div>
    </div>

    <!-- Right: Charts (stacked) -->
    <div class="chart-panel">
        <div class="chart-wrapper">
            <h3 class="chart-title">User Feedback Overview</h3>
            <canvas id="feedbackChart"></canvas>
        </div>
        <div class="chart-wrapper">
            <h3 class="chart-title">Sentiment Analysis</h3>
            <canvas id="sentimentPieChart"></canvas>
        </div>
    </div>
</div>
  
        </table>
        <div class="table-container">
        <div id="summary" class="table-responsive">
        <h2 style="text-align: center; font-size: 22px; margin-bottom: 10px; color: #4a4e69;">SUMMARY OF TRANSACTIONS</h2>
        <form id="filterForm" method="post" class="header-search">
    <div class="search-container">
        <select name="filter_option" class="input-text header-searchbar">
            <option value="">-- Filter by --</option>
            <option value="daily" <?php if($filter == 'daily') echo 'selected'; ?>>Daily</option>
            <option value="weekly" <?php if($filter == 'weekly') echo 'selected'; ?>>Weekly</option>
            <option value="monthly" <?php if($filter == 'monthly') echo 'selected'; ?>>Monthly</option>
            <option value="yearly" <?php if($filter == 'yearly') echo 'selected'; ?>>Yearly</option>
        </select>
        <button type="submit" class="login-btn btn-primary btn" style="background-color: #224D98;">Apply</button>
        <a href="bookings.php" class="login-btn btn-secondary btn" style="background-color: #E5515B;">Reset</a>
    </div>
</form>


        <table class="sub-table" style="width: 100%; border-collapse: collapse; text-align: center;">
    <thead>
        <tr>
            <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd;">#</th>
            <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd;">Date</th>
            <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd;">Total Sales</th>
            <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd;">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $num = 1;
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $date = $row['sales_date'];
                $amount = $row['total_amount'];
                echo "<tr>
    <td>$num</td>
    <td>$date</td>
    <td>₱" . number_format($amount, 2) . "</td>
    <td>
        <a href='bookings.php?filter_year=" . date('Y', strtotime($date)) . "' 
           style='color: white; background-color: #2EAF7D; padding: 5px 10px; border-radius: 5px; text-decoration: none;'>
           View Details
        </a>
    </td>
</tr>";
                $num++;
            }
        } else {
            echo "<tr><td colspan='4' style='padding: 10px;'>No data found.</td></tr>";
        }
        ?>
        
    </tbody>
</table>
<div style="text-align:center; margin-top: 15px;">
<?php if ($totalPages > 1): ?>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
            <strong style='margin: 0 5px;'><?= $i ?></strong>
        <?php else: ?>
            <a href="?page=<?= $i ?>&filter_option=<?= urlencode($filter) ?>" style='margin: 0 5px;'><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
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

    document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('feedbackChart').getContext('2d');

    var feedbackChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
            datasets: [{
                label: 'Number of Feedbacks per Rating',
                data: [
                    <?php echo $ratingCounts[5]; ?>,
                    <?php echo $ratingCounts[4]; ?>,
                    <?php echo $ratingCounts[3]; ?>,
                    <?php echo $ratingCounts[2]; ?>,
                    <?php echo $ratingCounts[1]; ?>
                ],
                backgroundColor: ['#4CAF50', '#8BC34A', '#FFC107', '#FF9800', '#F44336'],
                borderRadius: 5,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
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

    document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent normal page reload
    
    // Get form data
    const formData = new FormData(this);
    const params = new URLSearchParams(formData).toString();
    
    // Redirect with filter param and scroll to summary
    window.location.href = `?${params}#summary`;
});

document.addEventListener('DOMContentLoaded', function () {
    var pieCtx = document.getElementById('sentimentPieChart').getContext('2d');

    var sentimentPieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Good', 'Neutral', 'Bad'],
            datasets: [{
                data: [
                    <?php echo $sentimentCounts['good']; ?>,
                    <?php echo $sentimentCounts['neutral']; ?>,
                    <?php echo $sentimentCounts['bad']; ?>
                ],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderColor: ['#fff', '#fff', '#fff'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
});

    </script>
</body>
</html>
