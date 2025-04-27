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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->

    <title>Dashboard</title>
</head>
<body>
<?php
session_start();
include("../connection.php");

if (isset($_SESSION["user"])) {
    if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

// Kunin ang client details
$userrow = $database->query("SELECT * FROM client WHERE c_email='$useremail'");

if ($userrow && $userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["client_id"];
    $fullname = $userfetch["c_fullname"];
    $email = $userfetch["c_email"];
    $contactnum = $userfetch["c_contactnum"];
    $address = $userfetch["c_address"];
    $hashed_password = $userfetch["c_password"]; // Kunin ang naka-hash na password
} else {
    session_unset();
    session_destroy();
    header("location: ../login.php");
    exit();
}

// Pag-update ng password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // I-verify ang kasalukuyang password
    if (password_verify($current_password, $hashed_password)) {
        if ($new_password === $confirm_password) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            // I-update ang password sa database
            $update_query = $database->prepare("UPDATE client SET c_password = ? WHERE client_id = ?");
            $update_query->bind_param("si", $new_password_hash, $userid);
            
            if ($update_query->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Password changed successfully! Please log in again.',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        window.location.href = '../login.php';
                    });
                </script>";
                session_unset();
                session_destroy();
                exit();
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Something went wrong. Try again.',
                        confirmButtonColor: '#dc3545'
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'New passwords do not match!',
                    confirmButtonColor: '#dc3545'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Incorrect current password!',
                confirmButtonColor: '#dc3545'
            });
        </script>";
    }
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
.dash-body {
    padding-top: 10px;
    padding-left: auto;
    padding-right: auto;
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
</style>
<div class="header">
    <h1>EXZPHOTOGRAPHY STUDIO</h1>
</div>
<div class="hamburger" onclick="toggleMenu()">
            ☰
</div>
    <div class="center-container">
        <!-- Hamburger Icon -->
        
        <div class="menu">
    <div class="close-btn" onclick="toggleMenu()">✖</div>

    <div class="menu-container">

        <!-- Profile Section -->
        <div class="profile-container">
            <img src="../img/user.png" alt="User Image">
            <p class="profile-title"><?php echo $username; ?></p>
            <p class="profile-subtitle"><?php echo $useremail; ?></p>
            <button onclick="showLogoutModal()" class="logout-btn btn-primary-soft btn">Log out</button>
        </div>

        <!-- Sidebar Links -->
        <a href="index.php" class="menu-btn menu-icon-home">
            <p class="menu-text">Home</p>
        </a>

        <a href="bookings.php" class="menu-btn menu-icon-appoinment">
            <p class="menu-text">My Bookings</p>
        </a>

        <a href="feedback.php" class="menu-btn menu-icon-feedback">
            <p class="menu-text">Feedback</p>
        </a>

        <a href="profile_set.php" class="menu-btn menu-icon-settings menu-active">
            <p class="menu-text">Profile Settings</p>
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
        <div class="dash-body">
    <div class="user-details">
        <img src="../img/user.png" alt="User Profile" class="profile-img">
        <h2><?php echo $fullname; ?></h2>
        <p class="profile-info"><span class="info-label">Email:</span> <?php echo $email; ?></p>
        <p class="profile-info"><span class="info-label">Contact Number:</span> <?php echo $contactnum; ?></p>
        <p class="profile-info"><span class="info-label">Address:</span> <?php echo $address; ?></p>
        <button class="btn" onclick="openModal()">Change Password</button>
    </div>

    <!-- Modal for Change Password -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <h3>Change Password</h3>
            <form method="POST">
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
    <?php
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the current password is correct
    if (password_verify($current_password, $current_password_hash)) {
        if ($new_password === $confirm_password) {
            // Encrypt the new password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password in database
            $update_query = $database->prepare("UPDATE client SET c_password = ? WHERE client_id = ?");
            $update_query->bind_param("si", $new_password_hash, $user_id);
            if ($update_query->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Password changed successfully!',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        window.location.href = 'settings.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Something went wrong. Try again.',
                        confirmButtonColor: '#dc3545'
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'New passwords do not match!',
                    confirmButtonColor: '#dc3545'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Incorrect current password!',
                confirmButtonColor: '#dc3545'
            });
        </script>";
    }
}
?>

     
    </script>
</body>
</html>
