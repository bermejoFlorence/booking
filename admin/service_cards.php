
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // displaying errors
?>
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

$studio_query = $database->query("SELECT service_id, name, details, price, date_created FROM services WHERE category = 'In Studio'");

// Fetch New Born Photoshoot Services
$newborn_query = $database->query("SELECT service_id, name, details, price, date_created FROM services WHERE category = 'New Born Photoshoot'");

$wedding_query = $database->query("SELECT service_id, name, details, price, date_created FROM services WHERE category = 'Wedding'");


$other_wedding_query = $database->query("SELECT service_id, name, details, price, date_created FROM services WHERE category = 'Other Wedding Services'");


if (!$studio_query || !$newborn_query) {
    die("Query Failed: " . $database->error); // Debugging output
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

        <a href="moving_average.php" class="menu-btn menu-icon-sales">
            <p class="menu-text">Sales</p>
        </a>

        <a href="settings.php" class="menu-btn menu-icon-settings menu-active">
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
        
        <div class="dash-body" style="margin-top: 15px;">
        
        <div class="header-section">
    <p class="heading-main12" style="font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px;">SERVICE DETAILS</p>
</div>
<div class="table-container">
        <div class="table-responsive">
        <table class="sub-table" style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead>
            <tr>
            <th colspan="6" style="font-size: 18px; font-weight: bold; padding: 15px; background-color: #f2f2f2; text-align: center;">
                IN STUDIO SERVICES
            </th>
        </tr>
                <tr>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">#</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Name</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Details</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Price</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Last Update</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Action</th>
                </tr>
            </thead>

            <tbody>
    <?php 
    $counter = 1;
    while ($row = $studio_query->fetch_assoc()) : 
        // Hatiin ang details mula sa database
        $detailsArray = explode('|', htmlspecialchars($row['details']));
    ?>
    <tr>
        <td style="text-align: center;"><?php echo $counter++; ?></td>
        <td style="text-align: center;"><?php echo htmlspecialchars($row['name']); ?></td>
        <td>
            <?php echo implode('<br>', $detailsArray); ?>
        </td>
        <td style="text-align: center;">P <?php echo htmlspecialchars($row['price']); ?>.00</td>
        <td style="text-align: center;"> <?php echo date("F j, Y", strtotime($row['date_created'])); ?></td>
        <td style="text-align: center;">
            <button onclick="openEditModal(
                <?php echo $row['service_id']; ?>,
                '<?php echo addslashes(htmlspecialchars($row['name'])); ?>',
                '<?php echo addslashes(implode('|', $detailsArray)); ?>',
                '<?php echo htmlspecialchars($row['price']); ?>'
            )" 
            style="background-color: #2EAF7D; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                Edit
            </button>
        </td>
    </tr>
    <?php endwhile; ?>
</tbody>
        </table>

        
        </div>
</div>
   <!-- New Born Photoshoot Services -->
   <table class="sub-table" style="width: 100%; border-collapse: collapse; text-align: center; margin-top: 20px;">
        <thead>
            <tr>
                <th colspan="6" style="font-size: 18px; font-weight: bold; padding: 15px; background-color: #f2f2f2; text-align: center;">
                    NEW BORN PHOTOSHOOT SERVICES
                </th>
            </tr>
            <tr>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">#</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Name</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Details</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Price</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Last Update</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $counter = 1;
            while ($row = $newborn_query->fetch_assoc()) : 
                $detailsArray = explode('|', htmlspecialchars($row['details']));
            ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo implode('<br>', $detailsArray); ?></td>
                <td>P <?php echo htmlspecialchars($row['price']); ?>.00</td>
                <td><?php echo date("F j, Y", strtotime($row['date_created'])); ?></td>
                <td style="text-align: center;">
                <button onclick="openEditModal(
                <?php echo $row['service_id']; ?>,
                '<?php echo addslashes(htmlspecialchars($row['name'])); ?>',
                '<?php echo addslashes(implode('|', $detailsArray)); ?>',
                '<?php echo htmlspecialchars($row['price']); ?>'
            )" 
            style="background-color: #2EAF7D; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                Edit
            </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

      <!-- Wedding Photoshoot Services -->
   <table class="sub-table" style="width: 100%; border-collapse: collapse; text-align: center; margin-top: 20px;">
        <thead>
            <tr>
                <th colspan="6" style="font-size: 18px; font-weight: bold; padding: 15px; background-color: #f2f2f2; text-align: center;">
                    WEDDING PHOTOSHOOT SERVICES
                </th>
            </tr>
            <tr>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">#</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Name</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Details</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Price</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Last Update</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $counter = 1;
            while ($row = $wedding_query->fetch_assoc()) : 
                $detailsArray = explode('|', htmlspecialchars($row['details']));
            ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo implode('<br>', $detailsArray); ?></td>
                <td>P <?php echo htmlspecialchars($row['price']); ?>.00</td>
                <td><?php echo date("F j, Y", strtotime($row['date_created'])); ?></td>
                <td style="text-align: center;">
                <button onclick="openEditModal(
                <?php echo $row['service_id']; ?>,
                '<?php echo addslashes(htmlspecialchars($row['name'])); ?>',
                '<?php echo addslashes(implode('|', $detailsArray)); ?>',
                '<?php echo htmlspecialchars($row['price']); ?>'
            )" 
            style="background-color: #2EAF7D; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                Edit
            </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

        <!-- Other Wedding Photoshoot Services -->
   <table class="sub-table" style="width: 100%; border-collapse: collapse; text-align: center; margin-top: 20px;">
        <thead>
            <tr>
                <th colspan="6" style="font-size: 18px; font-weight: bold; padding: 15px; background-color: #f2f2f2; text-align: center;">
                    OITHER WEDDING PHOTOSHOOT SERVICES
                </th>
            </tr>
            <tr>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">#</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Name</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Price</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Last Update</th>
                    <th style="font-size: 16px; font-weight: bold; padding: 10px; border-bottom: 2px solid #ddd; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $counter = 1;
            while ($row = $other_wedding_query->fetch_assoc()) : 
                $detailsArray = explode('|', htmlspecialchars($row['details']));
            ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>P <?php echo htmlspecialchars($row['price']); ?>.00</td>
                <td><?php echo date("F j, Y", strtotime($row['date_created'])); ?></td>
                <td style="text-align: center;">
                <button onclick="openEditModal(
                <?php echo $row['service_id']; ?>,
                '<?php echo addslashes(htmlspecialchars($row['name'])); ?>',
                '<?php echo addslashes(implode('|', $detailsArray)); ?>',
                '<?php echo htmlspecialchars($row['price']); ?>'
            )" 
            style="background-color: #2EAF7D; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                Edit
            </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div id="modalContent" style="background: white; padding: 30px; border-radius: 12px; width: 400px; position: relative;">
        <h2 style="text-align: center; margin-bottom: 20px;">Edit Service</h2>
        <form id="editForm" method="POST" action="update_service.php">
            <input type="hidden" id="category" name="category">

            <input type="hidden" id="editServiceId" name="service_id">
            
            <label for="service_name">Service Name:</label>
            <input type="text" id="service_name" name="service_name" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
            
            <label for="service_details">Service Details:</label>
            <div id="serviceDetailsContainer" style="margin-bottom: 10px;">
                <input type="text" id="detail1" name="service_details[]" placeholder="Detail 1" required style="width: 100%; padding: 8px; margin-bottom: 5px;">
                <input type="text" id="detail2" name="service_details[]" placeholder="Detail 2" style="width: 100%; padding: 8px; margin-bottom: 5px;">
                <input type="text" id="detail3" name="service_details[]" placeholder="Detail 3" style="width: 100%; padding: 8px; margin-bottom: 5px;">
                <input type="text" id="detail4" name="service_details[]" placeholder="Detail 4" style="width: 100%; padding: 8px; margin-bottom: 5px;">
                <input type="text" id="detail5" name="service_details[]" placeholder="Detail 5" style="width: 100%; padding: 8px; margin-bottom: 5px;">
            </div>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required step="0.01" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <button type="submit" style="background-color: rgb(39, 134, 211); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">Save</button>
            <button type="button" onclick="closeEditModal()" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 16px;">Cancel</button>
        </form>
    </div>
</div>


</div>

    </div>
    <script>
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('open');
        }

        function openEditModal(serviceId) {
    // Kunin ang modal at ipakita ito
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('editServiceId').value = serviceId;

    // Kunin ang data mula sa server gamit ang AJAX
    fetch('get_service_data.php?id=' + serviceId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('service_name').value = data.service_name;
            document.getElementById('service_description').value = data.service_description;
        })
        .catch(error => console.error('Error:', error));
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
function openEditModal(serviceId, serviceName, serviceDetails, price) {
    document.getElementById('editServiceId').value = serviceId;
    document.getElementById('service_name').value = serviceName;
    document.getElementById('price').value = price;

    const detailsContainer = document.getElementById('serviceDetailsContainer');
    detailsContainer.innerHTML = ''; // Clear existing inputs

    const detailsArray = serviceDetails.split('|');
    detailsArray.forEach((detail, index) => {
        let input = document.createElement('input');
        input.type = 'text';
        input.name = 'service_details[]';
        input.value = detail;
        input.placeholder = `Detail ${index + 1}`;
        input.style = 'width: 100%; padding: 8px; margin-bottom: 5px;';
        detailsContainer.appendChild(input);
    });

    document.getElementById('editModal').style.display = 'flex';
}

document.getElementById("editForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Pigilan ang default submit action

    const formData = new FormData(this);
    
    fetch('update_service.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(); // Reload page after success
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred.',
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
        console.error('Error:', error);
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
