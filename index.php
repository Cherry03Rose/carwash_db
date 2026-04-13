<?php
session_start();


// Check if the user is logged in
$user_logged_in = isset($_SESSION['user_name']) && isset($_SESSION['user_email']);

if ($user_logged_in) {
    $user_name = $_SESSION['user_name'];
    $user_email = $_SESSION['user_email'];

    if (!empty($_SESSION['user_photo'])) {
        $user_photo = $_SESSION['user_photo']; // Gmail photo
        $show_initials = false;
    } else {
        // Generate initials from full name
        $name_parts = preg_split('/\s+/', trim($user_name));
        $initials = '';
        foreach ($name_parts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        $show_initials = true;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AutoWash - Car Wash Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"> 
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="lib/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .profile-dropdown {
            position: relative;
            display: inline-block;
            margin-left: 35px; /* More space from button */
            vertical-align: middle;
        }
        .profile-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
            cursor: pointer;
            background: #eee;
            transition: box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .initials {
    display: inline-block;
        }
        .dropdown-menu-custom {
            display: none;
            position: absolute;
            right: 0;
            top: 120%;
            min-width: 220px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border-radius: 7px;
            padding: 20px 15px 10px 15px;
            z-index: 9999;
            text-align: left;
        }
        .dropdown-menu-custom.active {
            display: block;
            animation: fadeIn 0.18s;
        }
        @keyframes fadeIn {
          from {opacity: 0; transform: translateY(10px);}
          to {opacity: 1; transform: translateY(0);}
        }
        .dropdown-menu-custom .user-name {
            font-weight: 700;
            font-size: 1.05em;
            margin-bottom: 2px;
            white-space: nowrap;
        }
        .dropdown-menu-custom .user-email {
            font-size: 0.95em;
            color: #888;
            margin-bottom: 12px;
            word-break: break-all;
        }
        .dropdown-menu-custom .dropdown-divider {
            margin: 9px 0;
        }
        .dropdown-menu-custom .logout-btn {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 7px 20px;
            border-radius: 4px;
            width: 100%;
            transition: background 0.2s;
            font-size: 1em;
        }
        .dropdown-menu-custom .logout-btn:hover {
            background: #bd2130;
        }
        @media (max-width: 991.98px) {
            .profile-dropdown {
                margin-left: 20px;
                margin-top: 10px;
            }
        }
        @media (max-width: 767.98px) {
            .profile-dropdown {
                margin-left: 0;
                margin-top: 10px;
            }
            .dropdown-menu-custom {
                left: auto;
                right: 0;
                min-width: 180px;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper"><!-- Wrapper starts -->
    <!-- Top Bar Start -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-12">
                    <div class="logo">
                        <a href="index.php">
                            <h1>Auto<span>Wash</span></h1>
                        </a>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7 d-none d-lg-block">
                    <div class="row">
                        <div class="col-4">
                            <div class="top-bar-item">
                                <div class="top-bar-icon">
                                    <i class="far fa-clock"></i>
                                </div>
                                <div class="top-bar-text">
                                    <h3>Opening Hour</h3>
                                    <p>Mon - Fri, 8:00am - 9:00pm</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="top-bar-item">
                                <div class="top-bar-icon">
                                    <i class="fa fa-phone-alt"></i>
                                </div>
                                <div class="top-bar-text">
                                    <h3>Call Us</h3>
                                    <p>+63 917 800 1234</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="top-bar-item">
                                <div class="top-bar-icon">
                                    <i class="far fa-envelope"></i>
                                </div>
                                <div class="top-bar-text">
                                    <h3>Email Us</h3>
                                    <p>AutoWashMain@gmail.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Top Bar End -->

    <!-- Nav Bar Start -->
    <div class="nav-bar">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
                <a href="#" class="navbar-brand">MENU</a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav mr-auto">
                        <a href="index.php" class="nav-item nav-link active">Home</a>
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="service.php" class="nav-item nav-link">Services</a>
                        <a href="price.php" class="nav-item nav-link">Price</a>
                        <a href="location.php" class="nav-item nav-link">Washing Points</a>
                    </div>
                    <div class="ml-auto d-flex align-items-center">
                        <a class="btn btn-custom" href="appointment.php">Get Appointment</a>
                       <?php if($user_logged_in): ?>
    <!-- Profile Circle & Dropdown -->
    <div class="profile-dropdown" id="profileDropdown">
        <div class="profile-circle" id="profileCircle" tabindex="0">
            <?php if (!$show_initials): ?>
                <img src="<?php echo htmlspecialchars($user_photo); ?>" alt="Profile">
            <?php else: ?>
                <span class="initials"><?php echo $initials; ?></span>
            <?php endif; ?>
        </div>
        <div class="dropdown-menu-custom" id="profileDropdownMenu">
            <div class="user-name" id="profileName"><?php echo htmlspecialchars($user_name); ?></div>
            <div class="user-email" id="profileEmail"><?php echo htmlspecialchars($user_email); ?></div>
            <div class="dropdown-divider"></div>
            <button class="logout-btn" onclick="handleLogout()">Logout</button>
        </div>
    </div>
<?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Nav Bar End -->

    <!-- Carousel Start -->
    <div class="carousel">
        <div class="container-fluid">
            <div class="owl-carousel">
                <div class="carousel-item">
                    <div class="carousel-img">
                        <img src="img/carousel-1.jpg" alt="Image">
                    </div>
                    <div class="carousel-text">
                        <h3>Washing & Detailing</h3>
                        <h1>Keep your Car Newer</h1>
                        <p>Experience premium car care with our expert washing and detailing services. From spotless exteriors to pristine interiors, we bring out the best in your vehicle—every time.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-img">
                        <img src="img/carousel-2.jpg" alt="Image">
                    </div>
                    <div class="carousel-text">
                        <h3>Washing & Detailing</h3>
                        <h1>Quality service for you</h1>
                        <p>From thorough exterior washes to precision detailing, we deliver exceptional care tailored to your car’s needs—ensuring it looks and feels like new, every time.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-img">
                        <img src="img/carousel-3.jpg" alt="Image">
                    </div>
                    <div class="carousel-text">
                        <h3>Washing & Detailing</h3>
                        <h1>Exterior & Interior Washing</h1>
                        <p>Restore your car’s shine inside and out with our expert washing and detailing services—leaving every surface spotless, refreshed, and showroom-ready.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Footer Start -->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-contact">
                        <h2>Get In Touch</h2>
                        <p><i class="fa fa-map-marker-alt"></i>123 Katipunan Avenue, Quezon City, Metro Manila</p>
                        <p><i class="fa fa-phone-alt"></i>+63 917 800 1234</p>
                        <p><i class="fa fa-envelope"></i>AutoWashMain@gmail.com</p>
                        <div class="footer-social">
                            <a class="btn" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn" href=""><i class="fab fa-youtube"></i></a>
                            <a class="btn" href=""><i class="fab fa-instagram"></i></a>
                            <a class="btn" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-link">
                        <h2>Popular Links</h2>
                        <a href="about.php">About Us</a>
                        <a href="appointment.php">Contact Us</a>
                        <a href="service.php">Our Services</a>
                        <a href="location.php">Service Points</a>
                        <a href="price.php">Pricing Plan</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-link">
                        <h2>Useful Links</h2>
                        <a href="#">Privacy policy</a>
                        <a href="#">Cookies</a>
                        <a href="#">Help</a>
                        <a href="#">FQAs</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container copyright">
            <p>&copy; <a href="index.php">AutoWash</a>, All Right Reserved. Designed By <a href="https://htmlcodex.com">HTML Codex</a></p>
        </div>
    </div>
    <!-- Footer End -->
    </div><!-- Wrapper ends -->

    <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>
    <div id="loader" class="show">
        <div class="loader"></div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
    <script src="js/main.js"></script>
    <script>
      // Toggle dropdown
      document.addEventListener('DOMContentLoaded', function() {
        var profileCircle = document.getElementById('profileCircle');
        var dropdownMenu = document.getElementById('profileDropdownMenu');
        var profileDropdown = document.getElementById('profileDropdown');
        if(!profileCircle) return;

        function closeDropdown(e) {
          if (
            profileDropdown && !profileDropdown.contains(e.target)
          ) {
            dropdownMenu.classList.remove('active');
          }
        }
        profileCircle.addEventListener('click', function(e) {
          dropdownMenu.classList.toggle('active');
          e.stopPropagation();
        });
        profileCircle.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            dropdownMenu.classList.toggle('active');
            e.preventDefault();
          }
        });
        document.addEventListener('click', closeDropdown);
        window.addEventListener('scroll', function() {
          dropdownMenu.classList.remove('active');
        });
      });
      function handleLogout() {
        window.location.href = "logout.php";
      }
    </script>
</body>
</html>