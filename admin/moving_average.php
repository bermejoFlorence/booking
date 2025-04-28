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

// Set timezone
date_default_timezone_set('Asia/Manila');
$current_year = date('Y');

// Fetch all yearly total sales (from 2019 to current year)
$sales_data = [];
$query = "SELECT YEAR(date) AS year, SUM(total_sales) AS total_sales
          FROM sales
          WHERE YEAR(date) >= 2019
          GROUP BY YEAR(date)
          ORDER BY YEAR(date)";
$result = $database->query($query);

while ($row = $result->fetch_assoc()) {
    $sales_data[(int)$row['year']] = (float)$row['total_sales'];
}
?>


    <style>
   .dash-body {
    padding: 20px;
    width: calc(100% - 250px); /* Para mas accurate kung may sidebar */
    margin-left: 250px;
    margin-top: 80px;
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
    .graph-section h2 {
        font-size: 18px;
    }
    canvas {
        height: 300px;
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
.graph-section {
    padding-top: 40px; /* 👈 Pushes content below the fixed header */
    text-align: center;
    width: 100%;
}
.graph-section h2 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
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
            <p class="profile-title">Administrator</p>
            <p class="profile-subtitle"><?php echo $user_email; ?></p>
            <button onclick="showLogoutModal()" class="logout-btn btn-primary-soft btn">Log out</button>
        </div>

        <!-- Sidebar Links -->
        <a href="index.php" class="menu-btn menu-icon-home">
            <p class="menu-text">Home</p>
        </a>

        <a href="bookings.php" class="menu-btn menu-icon-appoinment">
            <p class="menu-text">Bookings</p>
        </a>

        <a href="report.php" class="menu-btn menu-icon-message">
            <p class="menu-text">Reports</p>
        </a>

        <a href="feedback.php" class="menu-btn menu-icon-feedback">
            <p class="menu-text">Feedback</p>
        </a>

        <a href="moving_average.php" class="menu-btn menu-icon-sales menu-active">
            <p class="menu-text">Sales</p>
        </a>

        <a href="settings.php" class="menu-btn menu-icon-settings">
            <p class="menu-text">Settings</p>
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

<div class="dash-body" style="margin-top: 15px; padding: 20px;">
    <div class="graph-section">
        <h2>Annual Sales Report (2019 - <?php echo $current_year; ?> + Forecast to <?php echo $current_year + 5; ?>)</h2>
        <canvas id="salesChart" style="margin-top: 20px;"></canvas>
    </div>
</div>


    </div>
    
    <script>
function toggleMenu() {
    const menu = document.querySelector('.menu');
    menu.classList.toggle('open');
}

document.addEventListener("DOMContentLoaded", function () {
    const salesData = <?php echo json_encode($sales_data); ?>;

    const currentYear = <?php echo $current_year; ?>;
    const startYear = 2019;
    const forecastYears = 5;

    // Build labels: from startYear to currentYear + forecastYears
    const labels = [];
    for (let y = startYear; y <= currentYear + forecastYears; y++) {
        labels.push(y);
    }

    // Split actual vs forecast
      const actualSalesData = [];
    const forecastSalesData = [];

    // Get all actual data first
    labels.forEach(year => {
        if (salesData[year]) {
            actualSalesData.push(salesData[year]);
            forecastSalesData.push(null); // No forecast yet
        } else {
            // Forecast logic: simple average of last 5 available years
            const availableYears = Object.keys(salesData).map(y => parseInt(y)).sort();
            const last5Years = availableYears.slice(-5);
            const last5Totals = last5Years.map(y => salesData[y]);
            const average = last5Totals.reduce((a, b) => a + b, 0) / last5Totals.length;

            actualSalesData.push(null); // No actual sales for future
            forecastSalesData.push(average);

            // Also simulate new forecasted year
            salesData[year] = average;
        }
    });

    const ctx = document.getElementById("salesChart").getContext("2d");

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Actual Sales",
                    data: actualSalesData,
                    borderColor: "blue",
                    backgroundColor: "transparent",
                    tension: 0.4
                },
                {
                    label: "Forecasted Sales",
                    data: forecastSalesData,
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
                    text: 'Annual Sales Report with 5-Year Forecast',
                    font: {
                        size: 20
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Year'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Sales Amount (PHP)'
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
