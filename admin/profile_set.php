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
    <title>Settings</title>
</head>
<body>
<?php
session_start();
include("../connection.php");

if (!isset($_SESSION["user"]) || $_SESSION['usertype'] != 'a') {
    header("location: ../login.php");
    exit();
}

$user_id = $_SESSION["user"];

$query = $database->prepare("SELECT emp_email, full_name, address, date_of_birth, emp_password FROM employee WHERE emp_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $emp_email = $row['emp_email'];
    $full_name = $row['full_name'];
    $emp_email = $row['emp_email'];
    $address = $row['address'];
    $date_of_birth = $row['date_of_birth'];
    $current_password_hash = $row['emp_password'];
} else {
    $full_name = "Unknown";
    $emp_email = "Unknown Email";
    $address = "No Address Provided";
    $date_of_birth = "Unknown";
    $current_password_hash = "";
}
?>

<style>

.popup{
    animation: transitionIn-Y-bottom 0.5s;
}
.sub-table{
    animation: transitionIn-Y-bottom 0.5s;
}
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
/* Responsive adjustments */
@media (max-width: 768px) {
    .settings-title {
        font-size: 1.5rem;
    }
}
@media (max-width: 480px) {
    .settings-title {
        font-size: 1.2rem;
    }
}

.dashboard-items {
    padding: 20px;
    margin: 10px auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 90%; /* Default for smaller screens */
}
@media (min-width: 768px) {
    .dashboard-items {
        flex-direction: row; /* For larger screens */
        width: 45%; /* Reduce width for side-by-side alignment */
    }
}
@media (min-width: 1024px) {
    .dashboard-items {
        width: 30%; /* Even smaller width for large desktops */
    }
}

.user-details {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin: space-between;
  
}
.profile-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
}
.profile-info {
    font-size: 18px;
    margin: 10px 0;
}
.info-label {
    font-weight: bold;
    color: #555;
}
.btn {
    display: inline-block;
    padding: 10px 20px;
    margin-top: 20px;
    background: #007bff;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
}
.btn:hover {
    background: #0056b3;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}
.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    width: 400px;
}
.modal input {
    width: 100%;
    padding: 10px;  
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.modal button {
    padding: 10px 20px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.modal button:hover {
    background: #218838;
}
.close-btn {
    background: red;
    margin-left: 10px;
}
@media (max-width: 768px) {
    .container {
        width: 90%;
    }
    .modal-content {
        width: 90%;
    }
    .user-details {
        position: absolute;
    left: 50%;
    transform: translateX(-50%);
    }
}
.designation {
    font-size: 16px;
    color: #777;
    font-weight: normal;
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
            <div class="close-btn" onclick="toggleMenu()" style="background-color: white;">✖</div>
            
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
                                    <p class="profile-subtitle"><?php echo $emp_email; ?></p>
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

                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings menu-active menu-icon-settings-active">
                        <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
        <div class="user-details">
    <img src="../img/user.png" alt="User Profile" class="profile-img">
    <h2><?php echo $full_name; ?> <br><span class="designation">(Administrator)</span></h2>
    <p class="profile-info"><span class="info-label">Email:</span> <?php echo $emp_email; ?></p>
    <p class="profile-info"><span class="info-label">Address:</span> <?php echo $address; ?></p>
    <p class="profile-info"><span class="info-label">Date of Birth:</span> <?php echo $date_of_birth; ?></p>
    <button class="btn" onclick="openModal()">Change Password</button>
</div>


<!-- Modal for Change Password -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <h3>Change Password</h3>
        <form method="POST" action="">
            <input type="hidden" name="current_password_hash" value="<?php echo $current_password_hash; ?>">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="change_password">Change Password</button>
            <button type="button" class="close-btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>
    </div>

    <script>
             function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('open');
        }
        function openModal() {
        document.getElementById("passwordModal").style.display = "flex";
    }
    function closeModal() {
        document.getElementById("passwordModal").style.display = "none";
    }
    </script>
    <?php
if (isset($_POST["change_password"])) {
    $entered_current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    $current_password_hash = $_POST["current_password_hash"];

    if (!password_verify($entered_current_password, $current_password_hash)) {
        echo "<script>alert('Incorrect current password!');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('New password and confirm password do not match!');</script>";
    } elseif (strlen($new_password) < 6) {
        echo "<script>alert('Password must be at least 6 characters!');</script>";
    } else {
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = $database->prepare("UPDATE employee SET emp_password = ? WHERE emp_id = ?");
        $update_query->bind_param("si", $new_password_hashed, $user_id);
        if ($update_query->execute()) {
            echo "<script>alert('Password changed successfully!');</script>";
        } else {
            echo "<script>alert('Error changing password!');</script>";
        }
    }
}
?>

</body>
</html>