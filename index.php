
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // displaying errors
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <link rel="icon" href="webpage/images/fevicon.png" type="image/gif" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>EXZPHOTOGRAPHY STUDIO</title>


  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="webpage/css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- font awesome style -->
  <link href="webpage/css/font-awesome.min.css" rel="stylesheet" />
  <!-- Custom styles for this template -->
  <link href="webpage/css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="webpage/css/responsive.css" rel="stylesheet" />

  <?php
include 'connection.php'; // database connection

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
</head>

<body>

  <div class="hero_area">
    <!-- header section strats -->
    <header class="header_section long_section px-0">
      <nav class="navbar navbar-expand-lg custom_nav-container ">
        <a class="navbar-brand" href="index.php">
          <span style="color: orange; ">EXZPHOTOGRAPHY</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class=""> </span>
        </button>
        <!-- sidebar -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
            <ul class="navbar-nav  ">
              <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="webpage/about.php"> About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="webpage/contact.php">Contact Us</a>
              </li>
            </ul>
          </div>
          <div class="quote_btn-container">
            <a href="login.php">
              <span class="btn btn-outline-primary">
                Login
              </span>
            </a>
            <a href="create-account.php">
              <span class="btn btn-primary">
                Register
              </span>
              </a>
          </div>
        </div>
      </nav>
    </header>
    <!-- end header section -->
    <!-- slider section -->
    <section class="slider_section long_section">
      <div id="customCarousel" class="carousel slide">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="container ">
              <div class="row">
                <div class="col-md-5">
                  <div class="detail-box">
                    <h1>
                      CAPTURE <br>
                      EVERY MOMENTS
                    </h1>
                    <p>
                    Your moments deserve to be captured by professionals. Let’s create something beautiful together!
                    </p>
                    <div class="btn-box">
                      <a href="webpage/contact.php" class="btn1">
                        Contact Us
                      </a>
                      <a href="webpage/about.php" class="btn2">
                        About Us
                      </a>
                    </div>
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="img-box">
                    <img src="webpage/images/photographer.png" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
    <!-- end slider section -->
  </div>

  <!-- pictures section -->

  <section class="blog_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          SAMPLE PICTURES
        </h2>
      </div>
      <div class="row">
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box">
            <div class="img-box">
              <img src="webpage/images/2.jpg" alt="">
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box">
            <div class="img-box">
              <img src="webpage/images/3.jpg" alt="">
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box">
            <div class="img-box">
              <img src="webpage/images/4.jpg" alt="">
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box">
            <div class="img-box">
              <img src="webpage/images/9.jpg" alt="">
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box">
            <div class="img-box">
              <img src="webpage/images/6.jpg" alt="">
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 mx-auto">
          <div class="box">
            <div class="img-box">
              <img src="webpage/images/7.jpg" alt="">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end picture section -->

  <!-- feedback section -->
  <section class="client_section layout_padding-bottom">
  <div class="container">
    <div class="heading_container">
      <h2>Client's Feedback</h2>
    </div>
    <div id="carouselExample2Controls" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">
        <?php
        $active = "active"; // Para sa unang item lang mag-set ng active class
        while ($row = mysqli_fetch_assoc($result)) {
          $rating_text = $row['rating']; // Kunin ang rating text
          $rating = isset($rating_map[$rating_text]) ? $rating_map[$rating_text] : 0; // Convert to number
        ?>
          <div class="carousel-item <?= $active ?>">
            <div class="row">
              <div class="col-md-11 col-lg-10 mx-auto">
                <div class="box">
                  <div class="detail-box">
                  <div class="rating">
                  <i>RATING: </i>
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                          if ($i <= $rating) {
                            echo '<i class="fa fa-star" style="color: #FFD700;"></i>'; // Gold star
                          } else {
                            echo '<i class="fa fa-star" style="color: #ccc;"></i>'; // Grey star
                          }
                        }
                        ?>
                      </div>
                    <div class="name">
                    <h6>&nbsp;&nbsp;&nbsp;<?= htmlspecialchars($row['c_fullname']) ?></h6>
                      
                    </div>
                    <p> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= htmlspecialchars($row['comment']) ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php
          $active = ""; // Tanggalin ang active class pagkatapos ng unang item
        }
        ?>
      </div>
      <div class="carousel_btn-container">
        <a class="carousel-control-prev" href="#carouselExample2Controls" role="button" data-slide="prev">
          <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample2Controls" role="button" data-slide="next">
          <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
          <span class="sr-only">Next</span>
        </a>
      </div>
    </div>
  </div>
  </section>
  <!-- end feedback section -->

  <!-- info section -->
  <section class="info_section long_section">
    <div class="container">
      <div class="contact_nav">
        <a href="">
          <i class="fa fa-phone" aria-hidden="true"></i>
          <span>
            Call : +63 9916500574
          </span>
        </a>
        <a href="">
          <i class="fa fa-envelope" aria-hidden="true"></i>
          <span>
            Email : exzphotographystudio@gmail.com
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
  <script src="webpage/js/jquery-3.4.1.min.js"></script>
  <!-- bootstrap js -->
  <script src="webpage/js/bootstrap.js"></script>
  <!-- custom js -->
  <script src="webpage/js/custom.js"></script>
  
</body>

</html>