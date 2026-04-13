<?php
session_start();
include 'connect.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user details
$user_id = $_SESSION['user_id'];
$user_stmt = $pdo->prepare("SELECT name, email, contact_number FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>AutoWash - Car Wash Website Template</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="Free Website Template" name="keywords">
        <meta content="Free Website Template" name="description">

        <!-- Favicon -->
        <link href="img/favicon.ico" rel="icon">

        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"> 
        
        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="lib/flaticon/font/flaticon.css" rel="stylesheet">
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <!-- Top Bar Start -->
        <div class="top-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-4 col-md-12">
                        <div class="logo">
                            <a href="index.php">
                                <h1>Auto<span>Wash</span></h1>
                                <!-- <img src="img/logo.jpg" alt="Logo"> -->
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
                            <a href="index.php" class="nav-item nav-link">Home</a>
                            <a href="about.php" class="nav-item nav-link">About</a>
                            <a href="service.php" class="nav-item nav-link">Services</a>
                            <a href="price.php" class="nav-item nav-link">Price</a>
                            <a href="location.php" class="nav-item nav-link">Washing Points</a>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-custom active-page" href="appointment.php">Get Appointment</a>
                             <?php include 'profile.php'; ?>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

    <style>

.appointment-container {
    max-width: 650px;
    margin: 30px auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    padding: 30px;
}

.appointment-header {
    text-align: center;
    margin-bottom: 30px;
}

.appointment-header h2 {
    color: #202C45;
    font-weight: 700;
}

.form-group label {
    font-weight: 600;
    color: #202C45;
}

.form-control {
    border-radius: 5px;
    padding: 12px 15px;
    border: 1px solid #ddd;
}

.btn-submit {
    background: #202C45;
    color: white;
    border: none;
    padding: 12px 30px;
    font-weight: 600;
    border-radius: 5px;
    width: 100%;
    transition: all 0.3s;
}

.btn-submit:hover {
    background: #E81C2E;
}

.service-checkbox {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    background: #f9f9f9;
}

.service-checkbox:hover {
    background: #eef;
}

.service-checkbox input[type="checkbox"] {
    margin-right: 10px;
}

.service-name {
    flex-grow: 1;
}

.service-price {
    font-weight: 600;
    color: #202C45;
}
select.form-control {
    font-size: 16px;
    line-height: 1.5;
    padding: 0.6rem 1rem;
    height: auto; /* prevent clipping */
}

    </style>
</head>
<body>

<!-- Booking Appointment Section -->
    <div class="appointment-container">
        <div class="appointment-header">
            <h2>Book Your Appointment</h2>
            <p>Fill out the form below to schedule your car service</p>
        </div>
        
        <form id="bookingForm" action="submit_booking.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- User Information -->
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>" readonly>
            </div>
            
            <!-- Vehicle Information -->
            <div class="form-group">
                <label for="plate_number">Plate Number</label>
                <input type="text" class="form-control" name="plate_number" id="plate_number" required>
            </div>
            
            <div class="form-group">
                <label for="car_size">Car Size</label>
                <select class="form-control" name="car_size" id="car_size" required>
                    <option value="">Select Car Size</option>
                    <option value="Small">Small</option>
                    <option value="Medium">Medium</option>
                    <option value="Large">Large</option>
                </select>
            </div>
            
            <!-- Appointment Details -->
            <div class="form-group">
                <label for="preferred_date">Preferred Date</label>
                <input type="date" class="form-control" name="preferred_date" id="preferred_date" min="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="preferred_time">Preferred Time</label>
                <select class="form-control" name="preferred_time" id="preferred_time" required>
                    <option value="">Select Time</option>
                    <?php
                    // Generate time slots from 8:00 AM to 5:00 PM
                    for ($hour = 8; $hour <= 17; $hour++) {
                        $time = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                        echo "<option value='$time'>$time</option>";
                    }
                    ?>
                </select>
            </div>
            
            <!-- Services -->
            <div class="form-group">
                <label>Select Services</label>
                <div id="servicesContainer">
                    <?php
                    // Fetch services from database
                    $services = $pdo->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($services as $service) {
                        echo '<div class="service-checkbox">
                                <input type="checkbox" name="service_needed[]" value="' . $service['id'] . '" id="service_' . $service['id'] . '" data-price="' . $service['price'] . '">
                                <label class="service-name" for="service_' . $service['id'] . '">' . htmlspecialchars($service['name']) . '</label>
                                <span class="service-price">₱' . number_format($service['price'], 2) . '</span>
                            </div>';
                    }
                    ?>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select class="form-control" name="payment_method" id="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="cash">Cash</option>
                    <option value="gcash">GCash</option>
                    <option value="card">Credit/Debit Card</option>
                </select>
            </div>
            
            <!-- Total Price Display -->
            <div class="form-group">
                <label>Estimated Total Price</label>
                <input type="text" class="form-control" id="total_price" value="₱0.00" readonly>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn btn-submit">Book Appointment</button>
        </form>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Calculate total price when services are selected
        $('input[name="service_needed[]"]').change(function() {
            calculateTotal();
        });
        
        function calculateTotal() {
            let total = 0;
            $('input[name="service_needed[]"]:checked').each(function() {
                total += parseFloat($(this).data('price'));
            });
            $('#total_price').val('₱' + total.toFixed(2));
        }
        
        // Form submission with AJAX
        $('#bookingForm').on('submit', function(e) {
            e.preventDefault();
            
            // Check if at least one service is selected
            if ($('input[name="service_needed[]"]:checked').length === 0) {
                alert('Please select at least one service');
                return;
            }
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            
            // Show loading state
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // Submit the form
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Redirect to success page
                        window.location.href = 'appointment_success.php?id=' + response.booking_id;
                    } else {
                        alert('Error: ' + response.message);
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred. Please try again.');
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            });
        });
    });
    </script>
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
                            <a href="service.php">Our Service</a>
                            <a href="location.php">Service Points</a>
                            <a href="price.php">Pricing Plan</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-link">
                            <h2>Useful Links</h2>
                            <a href="">Terms of use</a>
                            <a href="">Privacy policy</a>
                            <a href="">Cookies</a>
                            <a href="">Help</a>
                            <a href="">FQAs</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container copyright">
                <p>&copy; <a href="index.php">AutoWash</a>, All Right Reserved. Designed By <a href="https://htmlcodex.com">HTML Codex</a></p>
            </div>
        </div>
        <!-- Footer End -->
        
        <!-- Back to top button -->
        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>
        
        <!-- Pre Loader -->
        <div id="loader" class="show">
            <div class="loader"></div>
        </div>

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/counterup/counterup.min.js"></script>
        
        <!-- Contact Javascript File -->
        <script src="mail/jqBootstrapValidation.min.js"></script>
        <script src="mail/contact.js"></script>

        <!-- Template Javascript -->
        <script src="js/main.js"></script>
</body>
</html>