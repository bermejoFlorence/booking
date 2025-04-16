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

$current_year = date('Y');
$previous_year = $current_year - 1; // The latest actual sales year
$forecast_year = $current_year; // The next predicted year

// Query to get sales data only for the previous year
$query = "SELECT DATE_FORMAT(date, '%M') AS month, SUM(total_sales) AS total_sales 
          FROM sales 
          WHERE YEAR(date) = $previous_year 
          GROUP BY MONTH(date) 
          ORDER BY MONTH(date)";
$result = $database->query($query);

// Store actual sales for the previous year
$actual_sales = [];
while ($row = $result->fetch_assoc()) {
    $actual_sales[$row['month']] = $row['total_sales'];
}

// Define months for consistency
$months = ["January", "February", "March", "April", "May", "June", 
           "July", "August", "September", "October", "November", "December"];

// Compute Moving Average for the Forecast Year (Using last 3 years of data)
$predicted_sales = [];
for ($i = 0; $i < 12; $i++) {
    $sales_3_years = [];

    for ($y = $current_year - 3; $y < $current_year; $y++) { // Last 3 years
        $query = "SELECT SUM(total_sales) AS total_sales 
                  FROM sales 
                  WHERE YEAR(date) = $y AND DATE_FORMAT(date, '%M') = '{$months[$i]}'";
        $result = $database->query($query);
        $data = $result->fetch_assoc();
        if ($data['total_sales'] !== null) {
            $sales_3_years[] = $data['total_sales'];
        }
    }

    // Compute moving average
    $predicted_sales[$months[$i]] = count($sales_3_years) > 0 ? array_sum($sales_3_years) / count($sales_3_years) : 0;
}


?>

    <style>
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
       
        <h2>Sales Report (<?php echo $previous_year . ' & ' . $forecast_year; ?>)</h2>
        <canvas id="salesChart"></canvas>
        
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

            const datasets = [
                {
                    label: `Actual Sales (${prevYear})`,
                    data: months.map(month => actualSales[month] || 0),
                    borderColor: "blue",
                    fill: false,
                    tension: 0.4
                },
                {
                    label: `Predicted Sales (${forecastYear})`,
                    data: months.map(month => predictedSales[month] || 0),
                    borderColor: "red",
                    borderDash: [5, 5], // Dashed line for forecast
                    fill: false,
                    tension: 0.4
                }
            ];

            // Generate Chart
            new Chart(document.getElementById("salesChart").getContext("2d"), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: { display: true, text: 'Sales Data & Forecast' },
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Months' } },
                        y: { title: { display: true, text: 'Sales Amount' } }
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
