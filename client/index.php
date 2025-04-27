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
$in_studio_services = $database->query("SELECT * FROM services WHERE category = 'In Studio' ORDER BY date_created DESC");

// Kunin ang "New Born Photoshoot" services
$new_born_services = $database->query("SELECT * FROM services WHERE category = 'New Born Photoshoot' ORDER BY date_created DESC");

// Kunin ang "New Born Photoshoot" services
$wedding_services = $database->query("SELECT * FROM services WHERE category = 'Wedding' ORDER BY date_created DESC");

$other_wedding_services = $database->query("SELECT * FROM services WHERE category = 'Other Wedding Services' ORDER BY date_created DESC");

?>

<?php

//query sa pag kuha ng feedback
$query = "SELECT f.comment, f.date_created, f.rating, c.c_fullname 
          FROM feedback f
          JOIN client c ON f.client_id = c.client_id
          ORDER BY f.date_created DESC"; // Sorting by latest feedback

$result = mysqli_query($database, $query);

// Mapping ng rating text sa numerical value
$rating_map = [
    "Very Dissatisfied" => 1,
    "Dissatisfied" => 2,
    "Neutral" => 3,
    "Satisfied" => 4,
    "Very Satisfied" => 5
];
?>

<style>

.slider_section .detail-box {
  color: #000000;
}

.slider_section .detail-box h1 {
  font-weight: bold;
  text-transform: uppercase;
  margin-bottom: 0;
}

.slider_section .detail-box p {
  margin: 25px 0;
}

.slider_section .detail-box .btn-box {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  margin: 0 -5px;
}

.slider_section .detail-box .btn-box a {
  margin: 5px;
  text-align: center;
  width: 165px;
}

.slider_section .detail-box .btn-box .btn1 {
  display: inline-block;
  padding: 10px 15px;
  background-color: #0d00ff;
  color: #ffffff;
  border-radius: 0;
  border: 1px solid #0004ff;
  -webkit-transition: all .2s;
  transition: all .2s;
}

.slider_section .detail-box .btn-box .btn1:hover {
  background-color: transparent;
  color: #0033ff;
}

.slider_section .detail-box .btn-box .btn2 {
  display: inline-block;
  padding: 10px 15px;
  background-color: #6bb7be;
  color: #ffffff;
  border-radius: 0;
  border: 1px solid #6bb7be;
  -webkit-transition: all .2s;
  transition: all .2s;
}

.slider_section .detail-box .btn-box .btn2:hover {
  background-color: transparent;
  color: #6bb7be;
}
.center-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Depende sa gusto mong taas */
    width: 100%;
    position: relative; /* Siguraduhin na may relative positioning ito */
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
        .feedback-container {
    text-align: center;
    margin-top: 20px;
}

.feedback-carousel {
    position: relative;
    max-width: 450px; /* Mas maliit ang box */
    margin: auto;
    overflow: hidden;
    border-radius: 10px;
    background: #fff;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.feedback-slides {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.feedback-slide {
    min-width: 100%;
    box-sizing: border-box;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    padding: 15px;
}

.feedback-content {
    text-align: left;
    width: 100%;
}

.feedback-rating {
    font-style: italic;
    font-size: 14px;
    margin-bottom: 8px;
}

.feedback-stars {
    display: inline-block;
    font-size: 16px;
    margin-left: 5px;
}

.feedback-star {
    color: gold;
}

.feedback-star.empty {
    color: #ccc;
}

/* Para sa Name at Comment */
.feedback-client {
    font-weight: bold;
    font-size: 18px;
    color: #4a90e2;
    margin-top: 8px;
}

.feedback-comment {
    font-size: 14px;
    color: #000;
    margin-top: 5px;
}

/* Navigation Buttons (Nasa Labas ng Box) */
.feedback-controls {
    display: flex;
    justify-content: center;
    margin-top: 10px;
    padding-bottom: 10px;
}

.feedback-btn {
    background-color: #5c9ea8;
    color: white;
    border: none;
    cursor: pointer;
    padding: 10px;
    font-size: 14px;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 8px;
}

.feedback-btn:hover {
    background-color: #468489;
}

/* ✅ RESPONSIVE DESIGN */
@media screen and (max-width: 768px) {
    .feedback-carousel {
        max-width: 350px; /* Mas maliit sa tablets */
        padding: 10px;
    }

    .feedback-slide {
        padding: 10px;
    }

    .feedback-client {
        font-size: 16px;
    }

    .feedback-comment {
        font-size: 13px;
    }

    .feedback-btn {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
}

@media screen and (max-width: 480px) {
    .feedback-carousel {
        max-width: 300px; /* Mas maliit sa mobile */
    }

    .feedback-client {
        font-size: 14px;
    }

    .feedback-comment {
        font-size: 12px;
    }

    .feedback-btn {
        width: 28px;
        height: 28px;
        font-size: 10px;
    }
}
.btn {
   
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
    <a href="index.php" class="menu-btn menu-icon-home <?php if($activePage=='home') echo 'menu-active'; ?>">
      <p class="menu-text">Home</p>
    </a>

    <a href="bookings.php" class="menu-btn menu-icon-appoinment <?php if($activePage=='bookings') echo 'menu-active'; ?>">
      <p class="menu-text">My Bookings</p>
    </a>

    <a href="feedback.php" class="menu-btn menu-icon-feedback <?php if($activePage=='feedback') echo 'menu-active'; ?>">
      <p class="menu-text">Feedback</p>
    </a>

    <a href="profile_set.php" class="menu-btn menu-icon-settings <?php if($activePage=='profile') echo 'menu-active'; ?>">
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

            <div class="services-title" style="padding-bottom: 20px; padding-top: 20px;">SERVICE OFFERED</div>
            <!-- In studio service cards -->
            <div class="services-title" style="padding-bottom: 10px">IN STUDIO PHOTOSHOOT</div>
            <div class="services-cards">

                <?php while ($service = $in_studio_services->fetch_assoc()): ?>
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
            </div>
              <!-- New Born service cards -->
        <div class="services-title" style="padding-bottom: 10px">NEW BORN PHOTOSHOOT</div>
        <div class="services-cards">

<?php while ($service = $new_born_services->fetch_assoc()): ?>
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
        </div>
        
          <!-- Wedding service cards -->
        <div class="services-title" style="padding-bottom: 10px">WEDDING PHOTOSHOOT</div>
                <div class="services-cards">

        <?php while ($service = $wedding_services->fetch_assoc()): ?>
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
        </div>

            <!-- Other wedding service cards -->
            <div class="services-title" style="padding-bottom: 10px">OTHER WEDDING PHOTOSHOOT
            </div>
                <div class="services-cards">

        <?php while ($service = $other_wedding_services->fetch_assoc()): ?>
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
        </div>
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
                                    <input type="hidden" id="hidden-event" name="event">
                                    <select id="event" name="event" required>
                                        <option value="Wedding">Wedding</option>
                                        <option value="Debut Party">Debut Party</option>
                                        <option value="Christening">Christening</option>
                                        <option value="Birthday Party">Birthday Party</option>
                                        <option value="Party">Party</option>
                                        <option value="Party">On Studio</option>
                                        <option value="Party">New Born Photoshoot</option>
                                        <option value="Party">Other Wedding Photoshoot</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="event-date" style="position: relative;">Date of Event:</label>
                                    <input type="text" id="event-date" name="date_event" required>
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
        <div class="feedback-container">
        <div class="services-title" style="padding-bottom: 10px">CLIENT'S FEEDBACK</div>
    <div class="feedback-carousel">
        <div class="feedback-slides">
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <div class="feedback-slide">
                    <div class="feedback-content">
                        <p class="feedback-rating"><strong>RATING:</strong> 
                            <?php 
                            // Kunin ang numerical rating mula sa database text
                            $rating_text = $row['rating'];
                            $rating = isset($rating_map[$rating_text]) ? $rating_map[$rating_text] : 0;
                            
                            // Mag-display ng stars ayon sa rating
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<span class="feedback-star">&#9733;</span>'; // Filled star (Gold)
                                } else {
                                    echo '<span class="feedback-star empty">&#9733;</span>'; // Empty star (Gray)
                                }
                            }
                            ?>
                        </p>
                        <p class="feedback-client"><?php echo htmlspecialchars($row['c_fullname']); ?></p>
                        <p class="feedback-comment"><?php echo htmlspecialchars($row['comment']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Navigation Arrows (Nasa Labas ng Box) -->
    <div class="feedback-controls">
        <button class="feedback-btn" onclick="prevFeedback()">&#8592;</button>
        <button class="feedback-btn" onclick="nextFeedback()">&#8594;</button>
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

        
        const availButtons = document.querySelectorAll('.avail-btn');
const eventDropdown = document.getElementById('event');
const hiddenEventInput = document.getElementById('hidden-event'); // Hidden input for event value

// Button click listener
availButtons.forEach(button => {
    button.addEventListener('click', function() {
        const serviceCard = button.closest('.service-card');
        const title = serviceCard.querySelector('.service-title').innerText;
        const price = serviceCard.querySelector('.service-price').innerText;
        const servicesTitle = serviceCard.closest('.services-cards').previousElementSibling.innerText.trim(); // Get the section title

        document.getElementById('modal-title').innerText = `Package: ${title}`;
        document.getElementById('modal-price').innerText = `Price: ${price}`;
        document.getElementById('modal-package').value = title;
        document.getElementById('modal-price-hidden').value = price.replace('₱', '');
        document.getElementById('popup1').style.display = 'flex';

        // Modify event dropdown based on the service type
        if (servicesTitle === "IN STUDIO PHOTOSHOOT") {
            // Remove "Wedding" option
            Array.from(eventDropdown.options).forEach(option => {
                if (option.value === "Wedding") {
                    option.style.display = "none";
                }
            });
            eventDropdown.removeAttribute("disabled"); // Enable dropdown
        } else {
            // Show all options if not in In Studio Services
            Array.from(eventDropdown.options).forEach(option => {
                option.style.display = "block";
            });
        }

        // If New Born Photoshoot, set default and disable event selection
        if (servicesTitle === "NEW BORN PHOTOSHOOT") {
            let found = false;
            Array.from(eventDropdown.options).forEach(option => {
                if (option.value === "New Born Photoshoot") {
                    option.selected = true;
                    found = true;
                }
            });

            // If "Wedding Photoshoot" is not in the dropdown, add it
            if (!found) {
                const newOption = document.createElement("option");
                newOption.value = "New Born Photoshoot";
                newOption.text = "New Born Photoshoot";
                newOption.selected = true;
                eventDropdown.appendChild(newOption);
            }

            eventDropdown.setAttribute("disabled", "disabled");

            // ✅ Update hidden input to store event value even when disabled
            hiddenEventInput.value = "New Born Photoshoot";
        } else {
            eventDropdown.removeAttribute("disabled");
            hiddenEventInput.value = eventDropdown.value; // Sync hidden input with event dropdown
        }

        // If Wedding Photoshoot, set default and disable event selection        
        if (servicesTitle === "WEDDING PHOTOSHOOT") {
            let found = false;
            Array.from(eventDropdown.options).forEach(option => {
                if (option.value === "Wedding") {
                    option.selected = true;
                    found = true;
                }
            });

            // If "Wedding Photoshoot" is not in the dropdown, add it
            if (!found) {
                const newOption = document.createElement("option");
                newOption.value = "Wedding";
                newOption.text = "Wedding";
                newOption.selected = true;
                eventDropdown.appendChild(newOption);
            }

            eventDropdown.setAttribute("disabled", "disabled");

            // ✅ Update hidden input to store event value even when disabled
            hiddenEventInput.value = "Wedding";
        } else {
            eventDropdown.removeAttribute("disabled");
            hiddenEventInput.value = eventDropdown.value; // Sync hidden input with event dropdown
        }

         // If Other Photoshoot, set default and disable event selection        
         if (servicesTitle === "OTHER WEDDING PHOTOSHOOT") {
            let found = false;
            Array.from(eventDropdown.options).forEach(option => {
                if (option.value === "Other Wedding Photoshoot") {
                    option.selected = true;
                    found = true;
                }
            });

            // If "other Photoshoot" is not in the dropdown, add it
            if (!found) {
                const newOption = document.createElement("option");
                newOption.value = "Other Wedding Photoshoot";
                newOption.text = "Other Wedding Photoshoot";
                newOption.selected = true;
                eventDropdown.appendChild(newOption);
            }

            eventDropdown.setAttribute("disabled", "disabled");

            // ✅ Update hidden input to store event value even when disabled
            hiddenEventInput.value = "Other Wedding Photoshoot";
        } else {
            eventDropdown.removeAttribute("disabled");
            hiddenEventInput.value = eventDropdown.value; // Sync hidden input with event dropdown
        }
    });
});

// ✅ Ensure hidden input updates when event dropdown changes
eventDropdown.addEventListener('change', function() {
    hiddenEventInput.value = eventDropdown.value;
});

// Close modal
function closeModal() {
    document.getElementById('popup1').style.display = 'none';
}


let feedbackIndex = 0;
const feedbackSlides = document.querySelectorAll(".feedback-slide");

function showFeedback(n) {
    feedbackIndex = (n + feedbackSlides.length) % feedbackSlides.length;
    document.querySelector(".feedback-slides").style.transform = `translateX(-${feedbackIndex * 100}%)`;
}

function nextFeedback() {
    showFeedback(feedbackIndex + 1);
}

function prevFeedback() {
    showFeedback(feedbackIndex - 1);
}


setInterval(nextFeedback, 5000); // Auto-slide every 5 seconds


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
                            window.location.href = "bookings.php"; // Redirect to bookings.php
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


    </script>
</body>
</html>
