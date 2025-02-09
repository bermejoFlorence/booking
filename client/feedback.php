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
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    
    <title>Settings</title>

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

} else {
    session_unset();
    session_destroy();
    header("location: ../login.php");
    exit();
}

?>
</head>
<body>
<style>
       @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
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

.feedback-body {
            font-family: 'Poppins', sans-serif;
            background: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 50px; /* Add space above the form */
            padding-bottom: 50px; /* Add space above the form */
            margin: 0;
        }

        .feedback-container {
    background: white;
    padding: 50px;
    border-radius: 16px;
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 100%;
    max-width: 600px; /* Increase the form width */
}

h2 {
    font-size: 32px; /* Larger form title */
    margin-bottom: 25px;
}

        .rating {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .star {
            font-size: 40px;
            color: #ccc;
            cursor: pointer;
            transition: transform 0.2s, color 0.2s;
        }

        .star:hover {
            transform: scale(1.2);
        }

        .star.active {
            color: gold;
        }

        textarea {
    width: 100%;
    padding: 16px; /* More padding for better user experience */
    margin-top: 20px;
    border-radius: 12px;
    border: 1px solid #ddd;
    resize: none;
    height: 150px; /* Increase the textarea height */
    font-size: 18px;
}

button {
    background: green;
    color: white;
    border: none;
    padding: 15px 30px; /* Bigger button size */
    border-radius: 12px;
    font-size: 18px; /* Larger font for the button */
    cursor: pointer;
    margin-top: 20px;
}

        button:hover {
            background: darkgreen;
        }

        @media screen and (max-width: 480px) {
            h2 {
                font-size: 20px;
            }
            .star {
                font-size: 30px;
            }
        }

    </style>

<div class="header">
    <h1>EXZPHOTOGRAPHY STUDIO</h1>
</div>
<div class="hamburger" onclick="toggleMenu()">
            ☰
        </div>
    
    <div class="center-container">

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
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
             
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment  ">
                        <a href="bookings.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></a></div>
                    </td>
                </tr>

                <tr class="menu-row">
                    <td class="menu-btn menu-icon-feedback menu-active menu-icon-feedback-active">
                        <a href="feedback.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Feedback</p></a></div>
                    </td>
                </tr>

                <!-- <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr> -->
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
        <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
<div class="feedback-body">    
<div class="feedback-container">
        <h2>Give Your Feedback</h2>
        <form action="submit_feedback.php" method="POST">
            <div class="rating">
                <input type="hidden" name="rating" value="0">
                <i class='bx bx-star star' data-index="1"></i>
                <i class='bx bx-star star' data-index="2"></i>
                <i class='bx bx-star star' data-index="3"></i>
                <i class='bx bx-star star' data-index="4"></i>
                <i class='bx bx-star star' data-index="5"></i>
            </div>
            <textarea name="feedback" placeholder="Write your feedback..." required></textarea>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>  

        </table> 


                        </div>
                        </center>
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

        const allStars = document.querySelectorAll('.rating .star');
const ratingValue = document.querySelector('input[name="rating"]');
const ratingText = document.createElement('p'); // Lalagyan ng text equivalent
ratingText.style.marginTop = "10px";
document.querySelector(".rating").appendChild(ratingText);

const ratingWords = ["Very Dissatisfied", "Dissatisfied", "Neutral", "Satisfied", "Very Satisfied"];

allStars.forEach((star, idx) => {
    star.addEventListener('click', function () {
        let rating = idx + 1;
        ratingValue.value = rating;

        allStars.forEach((s, i) => {
            s.classList.remove('active');
            s.classList.replace('bxs-star', 'bx-star');
            if (i <= idx) {
                s.classList.add('active');
                s.classList.replace('bx-star', 'bxs-star');
            }
        });
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const feedbackForm = document.querySelector("form");
    
    feedbackForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent page reload

        const formData = new FormData(feedbackForm);

        fetch("submit_feedback.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: data.status === "success" ? "Success!" : "Error!",
                text: data.message,
                icon: data.status,
                confirmButtonColor: data.status === "success" ? "#3085d6" : "#d33",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed && data.status === "success") {
                    location.reload(); // Reload page after successful feedback submission
                }
            });
        })
        .catch(error => {
            Swal.fire({
                title: "Error!",
                text: "An error occurred. Please try again.",
                icon: "error",
                confirmButtonColor: "#d33",
                confirmButtonText: "OK"
            });
        });
    });
});

</script>

</body>
</html>