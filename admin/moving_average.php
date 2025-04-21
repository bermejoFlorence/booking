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

// Get logged-in user details
$user_id = $_SESSION["user"];
$query = $database->prepare("SELECT emp_email FROM employee WHERE emp_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user_email = ($result->num_rows > 0) ? $result->fetch_assoc()["emp_email"] : "Unknown Email";

// Set timezone and date variables
date_default_timezone_set('Asia/Manila');
$current_year = date('Y');
$previous_year = $current_year - 1; // Last full year of actual sales
$forecast_year = $current_year + 3; // Predicting 3 years ahead
$business_start_year = 2019;

// Historical years to use: current - 8 up to current - 4 (5 years before the 3-year gap)
$start_year = max($business_start_year, $current_year - 8); // Not earlier than business start
$end_year = $current_year - 4; // End before 3-year gap

// Fetch actual sales for the previous year (used for plotting)
$query = "SELECT DATE_FORMAT(date, '%M') AS month, SUM(total_sales) AS total_sales 
          FROM sales 
          WHERE YEAR(date) = $previous_year 
          GROUP BY MONTH(date) 
          ORDER BY MONTH(date)";
$result = $database->query($query);

// Store actual sales in array
$actual_sales = [];
while ($row = $result->fetch_assoc()) {
    $actual_sales[$row['month']] = $row['total_sales'];
}

// Define months (for consistency)
$months = ["January", "February", "March", "April", "May", "June", 
           "July", "August", "September", "October", "November", "December"];

// Forecast calculation (Moving Average based on 5 years with 3-year gap)
$predicted_sales = [];

for ($i = 0; $i < 12; $i++) {
    $sales_years = [];

    for ($y = $start_year; $y <= $end_year; $y++) {
        $query = "SELECT SUM(total_sales) AS total_sales 
                  FROM sales 
                  WHERE YEAR(date) = $y AND DATE_FORMAT(date, '%M') = '{$months[$i]}'";
        $result = $database->query($query);
        $data = $result->fetch_assoc();

        if ($data['total_sales'] !== null) {
            $sales_years[] = $data['total_sales'];
        }
    }

    // Compute average for the forecast month
    $predicted_sales[$months[$i]] = count($sales_years) > 0 
        ? array_sum($sales_years) / count($sales_years) 
        : 0;
}
?>


<style>
    .dash-body {
    padding: 20px;
    width: calc(100% - 250px); /* Para di masikip pag may sidebar */
    margin-left: 250px;
    margin-top: 80px;
    font-size: 14px;
}

canvas {
    max-width: 100%;
    height: 400px;
}

h2 {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
    word-wrap: break-word;
    word-break: break-word;
    white-space: normal;
}

@media screen and (max-width: 768px) {
    .dash-body {
        width: 100%;
        margin-left: 0;
        margin-top: 80px;
        padding: 15px;
    }

    .header {
        font-size: 16px;
        padding: 10px;
    }

    h2 {
        font-size: 20px;
    }
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
                    <td class="menu-btn menu-icon-home ">
                        <a href="index.php" class="non-style-link-menu ">
                            <div><p class="menu-text">Home</p></div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="bookings.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
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
                    <td class="menu-btn menu-icon-sales menu-active menu-icon-sales-active">
                        <a href="moving_average.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Sales</p></a></div>
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
        <tr>
            <td colspan="2" style="text-align: center;">
                <h2 style="font-size: 20px; margin-bottom: 10px;">
                    Sales Report (<?php echo $previous_year . ' & ' . $forecast_year; ?>)
                </h2>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <canvas id="salesChart" style="max-width: 100%; height: 400px;"></canvas>
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

        document.addEventListener("DOMContentLoaded", function () {
        const months = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"];

        const actualSales = <?php echo json_encode($actual_sales); ?>;
        const predictedSales = <?php echo json_encode($predicted_sales); ?>;
        const prevYear = <?php echo $previous_year; ?>;
        const forecastYear = <?php echo $forecast_year; ?>;

        const ctx = document.getElementById("salesChart").getContext("2d");

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: `Actual Sales (${prevYear})`,
                        data: months.map(month => actualSales[month] || 0),
                        borderColor: "blue",
                        backgroundColor: "transparent",
                        tension: 0.4
                    },
                    {
                        label: `Forecasted Sales (${forecastYear})`,
                        data: months.map(month => predictedSales[month] || 0),
                        borderColor: "red",
                        borderDash: [5, 5],
                        backgroundColor: "transparent",
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Sales Forecast vs Actual',
                        font: {
                            size: 20
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Months'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sales Amount'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>
</body>
</html>
