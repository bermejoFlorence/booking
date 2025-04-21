<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <link rel="icon" href="images/fevicon.png" type="image/gif" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>EXZPHOTOGRAPHY / Contact US</title>


  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

</head>

<body class="sub_page">

  <div class="hero_area">
    <!-- header section strats -->
        <!-- header section strats -->
    <header class="header_section long_section px-0">
      <nav class="navbar navbar-expand-lg custom_nav-container ">
        <a class="navbar-brand" href="../index.php">
          <span style="color: orange; ">EXZPHOTOGRAPHY</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class=""> </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
            <ul class="navbar-nav  ">
              <li class="nav-item active">
                <a class="nav-link" href="../index.php">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php"> About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="contact.php">Contact Us</a>
              </li>
            </ul>
          </div>
          <div class="quote_btn-container">
            <a href="../login.php">
              <span class="btn btn-outline-primary">
                Login
              </span>
            </a>
            <a href="../create-account.php">
              <span class="btn btn-primary">
                Register
              </span>
              </a>
          </div>
        </div>
      </nav>
    </header>
    <!-- end header section -->
      </div>

  <!-- contact section -->
  <section class="contact_section  long_section">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="form_container">
            <div class="heading_container">
              <h2>
                Contact Us
              </h2>
            </div>
            <form id="contactForm">
                <div>
                    <input type="text" name="client_name" placeholder="Your Name" required />
                </div>
                <div>
                    <input type="text" name="phone_num" placeholder="Phone Number" required />
                </div>
                <div>
                    <input type="email" name="email" placeholder="Email" required />
                </div>
                <div>
                    <input type="text" class="message-box" name="message" placeholder="Message" required />
                </div>
                <div class="btn_box">
                    <button type="submit">
                        SEND
                    </button>
                </div>
            </form>
          </div>
        </div>
        <div class="col-md-6">
          <div class="map_container">
            <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3547.4855632758677!2d122.9697367766905!3d13.749346162933605!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a21b004071031d%3A0x72c36ca23cf05d73!2sExzphotography%20Studio!5e1!3m2!1sen!2sph!4v1745222629208!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> 
          </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end contact section -->

<!-- info section -->
<section class="info_section long_section">

<div class="container">
  <div class="contact_nav">
    <a href="">
      <i class="fa fa-phone" aria-hidden="true"></i>
      <span>
        Call : +63 912345678
      </span>
    </a>
    <a href="">
      <i class="fa fa-envelope" aria-hidden="true"></i>
      <span>
        Email : rysha.andaya@cbsua.edu.ph
      </span>
    </a>
    <div>
        <div class="info_form">
          <h4>
            FOLLOW US
          </h4>
          <div class="social_box">
            <a href="">
              <i class="fa fa-facebook" aria-hidden="true"></i>
            </a>
            <a href="">
              <i class="fa fa-twitter" aria-hidden="true"></i>
            </a>
            <a href="">
              <i class="fa fa-linkedin" aria-hidden="true"></i>
            </a>
            <a href="">
              <i class="fa fa-instagram" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
  </div>
</div>
</section>
<!-- end info_section -->




  <!-- jQery -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <!-- bootstrap js -->
  <script src="js/bootstrap.js"></script>
  <!-- custom js -->
  <script src="js/custom.js"></script>
  
  <script>
document.getElementById("contactForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Pigilan ang default form submission

    const formData = new FormData(this);

    fetch("submit_form.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            Swal.fire({
                title: "Success!",
                text: "Your message has been sent successfully.",
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK"
            }).then(() => {
                document.getElementById("contactForm").reset(); // I-reset ang form pagkatapos
            });
        } else {
            Swal.fire({
                title: "Error!",
                text: "There was a problem sending your message. Please try again.",
                icon: "error",
                confirmButtonColor: "#d33",
                confirmButtonText: "OK"
            });
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({
            title: "Error!",
            text: "An unexpected error occurred. Please try again later.",
            icon: "error",
            confirmButtonColor: "#d33",
            confirmButtonText: "OK"
        });
    });
});
</script>

</body>

</html>