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

include("../connection.php");

$userrow = $database->query("SELECT * FROM client WHERE c_email='$useremail'");

if ($userrow && $userrow->num_rows > 0) {
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["client_id"];
    $username = $userfetch["c_fullname"];
    $usercontact = $userfetch["c_contactnum"];
    $useraddress = $userfetch["c_address"];
} else {
    session_unset();
    session_destroy();
    header("location: ../login.php");
    exit();
}

$services = $database->query("SELECT * FROM services ORDER BY date_created DESC");
?>

<style>
.center-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Depende sa gusto mong taas */
    width: 100%;
    position: relative; /* Siguraduhin na may relative positioning ito */
}

.header {
    background-color: green;
    color: white;
    text-align: center;
    padding: 5px;
    font-size: 10px;
    font-weight: bold;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000; 
}

@media screen and (max-width: 768px) {
    .header {
        font-size: 5px;
        padding: 10px;
    }
}

@media screen and (max-width: 480px) {
    .header {
        font-size: 3px;
        padding: 8px;
    }
}
.ui-datepicker-unselectable .ui-state-default {
            background: red !important;
            color: white !important;
            opacity: 0.6; /* Para mukhang disabled */
            pointer-events: none; /* Hindi ma-click */
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
            <!-- Close Button -->
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
                                    <p class="profile-title"><?php echo $username; ?></p>
                                    <p class="profile-subtitle"><?php echo $useremail; ?></p>
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
                    <td class="menu-btn menu-icon-appointment menu-active menu-icon-home-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Home</p></a></div></a>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="bookings.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-feedback">
                        <a href="feedback.php" class="non-style-link-menu"><div><p class="menu-text">Feedback</p></a></div>
                    </td>
                </tr>

                <!-- <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr> -->
            </table>
        </div>

        <div class="dash-body" style="margin-top: 15px">

        <div class="carousel">
                <div class="carousel-slides">
                    <div class="carousel-slide"><img src="image/img_1.jpg" alt="Image 1"></div>
                    <div class="carousel-slide"><img src="image/img_2.jpg" alt="Image 2"></div>
                    <div class="carousel-slide"><img src="image/img_3.jpg" alt="Image 3"></div>
                </div>
                <button class="carousel-btn prev" onclick="prevSlide()">&#10094;</button>
                <button class="carousel-btn next" onclick="nextSlide()">&#10095;</button>
                <div class="carousel-dots"></div>
            </div>

            <div class="services-title" style="padding-bottom: 10px">S E R V I C E S</div>
            
            <div class="services-cards">
    <?php while ($service = $services->fetch_assoc()): ?>
        <div class="service-card">
            <h3 class="service-title"><?= htmlspecialchars($service['name']) ?></h3>
            
            <?php 
                $detailsArray = explode("|", $service['details']); 
                foreach ($detailsArray as $detail): 
            ?>
                <p class="service-details"><?= htmlspecialchars($detail) ?></p>
            <?php endforeach; ?>

            <p class="service-price">₱<?= number_format($service['price'], 2) ?></p>
            <button class="avail-btn" 
                data-bs-toggle="modal" 
                data-bs-target="#availModal"
                data-service-name="<?= htmlspecialchars($service['name']) ?>"
                data-service-details="<?= htmlspecialchars($service['details']) ?>"
                data-service-price="₱<?= number_format($service['price'], 2) ?>">
                Avail
            </button>
        </div>
    <?php endwhile; ?>
            <div id="popup1" class="overlay">
                <div class="popup">
                    <span class="close" onclick="closeModal();">&times;</span>
                    <center>
                        <h2>Booking Details</h2>
                    </center>
                    <div class="modal-content">
                        <p><strong></strong> <span id="modal-title" style="font-size: 1.2em; font-weight: bold;"></span></p>
                        <p><strong></strong> <span id="modal-price" style="font-size: 1.2em; font-weight: bold;"></span></p>
                        <form id="service-form" method="POST" action="save_booking.php">
                                <input type="hidden" name="package" id="modal-package" value="">
                                <input type="hidden" name="price" id="modal-price-hidden" value="">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name:</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($username); ?>"  readonly>
                                </div>
                                <div class="form-group">  
                                <label for="contact">Contact Number:</label>
                                    <input type="text" id="contact" name="contact"  value="<?php echo htmlspecialchars($usercontact); ?>"  readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($useraddress); ?>" readonly>
                            </div>
                            <div class="form-row">
                            <div class="form-group">
                                    <label for="event">Event:</label>
                                    <select id="event" name="event" required>
                                        <option value="Wedding">Wedding</option>
                                        <option value="Debut Party">Debut Party</option>
                                        <option value="Christening">Christening</option>
                                        <option value="Birthday Party">Birthday Party</option>
                                        <option value="Party">Party</option>
                                        <option value="Party">On Studio</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="event-date">Date of Event:</label>
                                    <input type="text" id="event-date" name="date_event" required readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="event-address">Address of Event:</label>
                                <input type="text" id="event-address" name="address_event" required>
                            </div>
                            <div class="button-container">
                                <button type="submit" form="service-form">Book Now</button>
                            </div>
                        </form>
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

        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const dotsContainer = document.querySelector('.carousel-dots');

        // Create dots
        slides.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.classList.add('carousel-dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            dotsContainer.appendChild(dot);
        });

        const dots = document.querySelectorAll('.carousel-dot');

        function updateCarousel() {
            const slidesContainer = document.querySelector('.carousel-slides');
            slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;

            dots.forEach(dot => dot.classList.remove('active'));
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            updateCarousel();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateCarousel();
        }

        // Auto-next every 3 seconds
        setInterval(nextSlide, 3000);

            // Get all the avail buttons
// Get all the avail buttons
const availButtons = document.querySelectorAll('.avail-btn');

// Button click listener
availButtons.forEach(button => {
    button.addEventListener('click', function() {
        const serviceCard = button.closest('.service-card');
        const title = serviceCard.querySelector('.service-title').innerText;
        const price = serviceCard.querySelector('.service-price').innerText;

        document.getElementById('modal-title').innerText = `Package: ${title}`;
        document.getElementById('modal-price').innerText = `Price: ${price}`;
        document.getElementById('modal-package').value = title;
        document.getElementById('modal-price-hidden').value = price.replace('₱', '');
        document.getElementById('popup1').style.display = 'flex';
    });
});

// Close modal
function closeModal() {
    document.getElementById('popup1').style.display = 'none';
}


// Optional: Handle form submission
document.getElementById('service-form').addEventListener('submit', function(e) {
    return true;
    // Get form data
    const name = document.getElementById('name').value;
    const contact = document.getElementById('contact').value;
    const notes = document.getElementById('notes').value;

    // Do something with the form data (like sending it to the server)
    console.log('Form submitted', { name, contact, notes });

    // Close the modal after submission (optional)
    closeModal();
});

$(document).ready(function () {
        var bookedDates = [];

        // Kunin ang booked dates mula sa server
        $.getJSON("get_booked_dates.php", function (data) {
            bookedDates = data;
        });

        // Initialize jQuery UI Datepicker
        $("#event-date").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: 0, // Disable past dates
            beforeShowDay: function (date) {
                var formattedDate = $.datepicker.formatDate("yy-mm-dd", date);

                if (bookedDates.includes(formattedDate)) {
                    return [false, "ui-datepicker-unselectable", "This date is already booked"];
                }
                return [true, ""];
            }
        });

        // AJAX Form Submission with SweetAlert
        $("#service-form").submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "save_booking.php",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "OK"
                        }).then(() => {
                            location.reload(); // Reload page after clicking OK
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: response.message,
                            icon: "error",
                            confirmButtonColor: "#d33",
                            confirmButtonText: "OK"
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "OK"
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
