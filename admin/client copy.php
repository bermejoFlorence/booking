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
// Query para i-display ang mga client details
$sql = "SELECT client_id, emp_id, c_fullname, c_email, c_contactnum, c_address, date_created 
        FROM client 
        ORDER BY date_created DESC
        LIMIT $records_per_page OFFSET $offset";
$result = $database->query($sql);
?>

   <style>
    .dash-body {
        margin: 30px auto; /* Center content */
        padding: 0 20px;
    }

    .header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px; /* Space sa pagitan ng header at table */
    flex-wrap: wrap;
    margin-top: 30px; /* Space sa taas ng header */
}

    .heading-main12 {
        font-size: 20px;
        color: rgb(49, 49, 49);
        margin: 0;
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
}
@media screen and (max-width: 480px) {
    .header {
        font-size: 3px;
        padding: 8px;
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
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="User Image">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle"><?php echo $user_email; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="bookings.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-sales">
                        <a href="moving_average.php" class="non-style-link-menu"><div><p class="menu-text">Sales</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings menu-active menu-icon-settings-active">
                        <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px;">
        
    <div class="header-section">
        <p class="heading-main12" style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px;">LIST OF CLIENT</p>
        <form action="" method="post" class="header-search">
            <div class="search-container">
                <input type="search" name="search" class="input-text header-searchbar" 
                    placeholder="Type Client Name" 
                    value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>" />
                <button type="submit" class="login-btn btn-primary btn">Search</button>
                <a href="client.php" class="login-btn btn-secondary btn">Reset</a>
            </div>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
        $search = $database->real_escape_string($_POST['search']);
        $sql = "SELECT client_id, emp_id, c_fullname, c_email, c_contactnum, c_address, date_created 
                FROM client 
                WHERE c_fullname LIKE '%$search%' 
                ORDER BY date_created DESC";
    
        $result = $database->query($sql);
    
        if ($result->num_rows > 0) {
            echo "<p>Search results for '<strong>$search</strong>':</p>";
        } else {
            echo "<p>No results found for '<strong>$search</strong>'.</p>";
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
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Email Address</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Contact Number</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Address</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Date Created</th>

                </tr>
            </thead>

            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        $formatted_date = date("F j, Y", strtotime($row['date_created'])); // Convert date format
                        echo "<tr>";
                        echo "<td>{$count}</td>";
                        echo "<td>{$row['c_fullname']}</td>";
                        echo "<td>{$row['c_email']}</td>";
                        echo "<td>{$row['c_contactnum']}</td>";
                        echo "<td>{$row['c_address']}</td>";
                        echo "<td>{$formatted_date}</td>"; // Display formatted date
                        echo "</tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='6'>No records available.</td></tr>";
                }
                ?>
                </tbody>
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

        function openEditModal(service) {
    document.getElementById('editServiceId').value = service.service_id;
    document.getElementById('editName').value = service.name;
    document.getElementById('editDetail1').value = service.detail_1;
    document.getElementById('editDetail2').value = service.detail_2;
    document.getElementById('editDetail3').value = service.detail_3;
    document.getElementById('editDetail4').value = service.detail_4;
    document.getElementById('editDetail5').value = service.detail_5;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
    </script>
</body>
</html>
