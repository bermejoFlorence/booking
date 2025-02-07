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

$user_email = ($result->num_rows > 0) ? $result->fetch_assoc()["emp_email"] : "Unknown Email";

date_default_timezone_set('Asia/Manila');

$current_year = date("Y");
$previous_year = $current_year - 1; // Para sa actual sales
$next_year = $current_year + 1; // Para sa forecast

// Query to get monthly sales for the latest full year
$query = "SELECT DATE_FORMAT(date, '%M') AS month, SUM(total_sales) AS total_sales 
          FROM sales 
          WHERE YEAR(date) = ? 
          GROUP BY MONTH(date) 
          ORDER BY MONTH(date) ASC";

$stmt = $database->prepare($query);
$stmt->bind_param("i", $previous_year);
$stmt->execute();
$result = $stmt->get_result();

$actual_months = [];
$actual_sales = [];

while ($row = $result->fetch_assoc()) {
    $actual_months[] = $row['month'];
    $actual_sales[] = $row['total_sales'];
}

// Check if there is enough data
$N = count($actual_sales);
if ($N < 2) {
    die("Not enough data to generate a reliable forecast.");
}

// Compute Linear Regression
$sumX = $sumY = $sumXY = $sumXX = 0;
for ($i = 0; $i < $N; $i++) {
    $sumX += $i + 1;
    $sumY += $actual_sales[$i];
    $sumXY += ($i + 1) * $actual_sales[$i];
    $sumXX += ($i + 1) * ($i + 1);
}

// Calculate slope (m) and intercept (b)
$m = ($N * $sumXY - $sumX * $sumY) / ($N * $sumXX - $sumX * $sumX);
$b = ($sumY - $m * $sumX) / $N;

// Predict sales for the next year
$predicted_sales = [];
$forecast_months = [];
$months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

for ($i = 1; $i <= 12; $i++) {
    $forecast_months[] = $months[$i - 1];
    $predicted_sales[] = max(0, round($m * ($N + $i) + $b, 2)); // Avoid negative sales
}

?>

    <style>
        .dash-body {
    margin: 30px auto; /* Adds top margin and centers horizontally */
    padding: 0 20px; /* Adds inner padding */
    width: 90%; /* Set width to allow centering with auto margin */
    max-width: 1200px; /* Optional: Limit the width for large screens */
}
        .dashboard-container {
    display: flex;
    justify-content: space-between; /* Distribute items evenly */
    gap: 20px;
    margin: 20px auto;
    padding: 20px;
    max-width: 1200px;
    flex-wrap: nowrap; /* Prevent stacking */
}
canvas { max-width: 100%; height: 400px; }

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
}



@media screen and (max-width: 480px) {
    .header {
        font-size: 3px;
        padding: 8px;
    }

}

.header {
    background-color: green;
    color: white;
    text-align: center;
    padding: 5px;
    font-size: 10px;
    font-weight: bold;
    width: 100%;
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
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
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
        <h2>Sales Report (<?php echo $previous_year . ' & ' . $current_year; ?>)</h2>
        <canvas id="salesChart"></canvas>
        
        </table>
        </div>
    </div>
    <script>
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('open');
        }

        const ctx = document.getElementById("salesChart").getContext("2d");

// Get PHP-generated sales data
const actualSalesData = [<?php echo implode(",", $actual_sales); ?>];
const predictedSalesData = [<?php echo implode(",", $predicted_sales); ?>];

// Get dynamic year values from PHP
const previousYear = <?php echo $previous_year; ?>;
const nextYear = <?php echo $next_year; ?>;

// Labels for months
const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

// Create the Chart
new Chart(ctx, {
    type: 'line',
    data: {
        labels: months,
        datasets: [
            {
                label: `Actual Sales (${previousYear})`,
                data: actualSalesData,
                borderColor: "blue",
                backgroundColor: "rgba(0, 0, 255, 0.2)",
                fill: false,
                tension: 0.4,
                pointStyle: 'circle',
                pointRadius: 5
            },
            {
                label: `Predicted Sales (${nextYear})`,
                data: predictedSalesData,
                borderColor: "red",
                backgroundColor: "rgba(255, 0, 0, 0.2)",
                borderDash: [5, 5], // Dashed line for forecast
                fill: false,
                tension: 0.4,
                pointStyle: 'rectRot',
                pointRadius: 5
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: `Sales Forecast: ${previousYear} (Actual) vs ${nextYear} (Predicted)`
            },
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                enabled: true
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
                }
            }
        }
    }
});

    </script>
</body>
</html>
