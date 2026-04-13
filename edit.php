<?php
include 'connect.php';

// Get booking data by ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM booking WHERE id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch();
    if (!$booking) {
        echo "Booking not found.";
        exit;
    }
} else {
    echo "No booking ID specified.";
    exit;
}

// Handle form submission for update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Preserve the original status from the earlier fetch
    $currentStatus = $booking['status'];

    $sql = "UPDATE booking SET 
                name = ?, 
                email = ?, 
                contact_number = ?, 
                plate_number = ?, 
                preferred_date = ?, 
                service_needed = ?, 
                car_size = ?, 
                status = ? 
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['contact_number'],
        $_POST['plate_number'],
        $_POST['preferred_date'],
        $_POST['service_needed'],
        $_POST['car_size'],
        $currentStatus,  // keep original status
        $id
    ]);

    header('Location: booking.php?updated=1');
    exit();
}
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

    <style>
        .appointment-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background-color: #f1f1f1;
        }

        .appointment-form-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .appointment-form-title {
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
            color: #343a40;
            text-align: center;
        }

        .appointment-form-group {
            margin-bottom: 20px;
        }

        .appointment-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .appointment-input:focus {
            border-color: #202C45;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.15);
            outline: none;
        }

        input[type="date"].appointment-input {
            appearance: none;
            background-color: #fff;
            color: #495057;
            margin-bottom: 20px;
        }

        .appointment-btn {
            width: 100%;
            background-color: #202C45;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .appointment-btn:hover {
            background-color: #375678;
        }
.service-checkboxes label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9f9f9;
  padding: 8px 12px;
  margin-bottom: 5px;
  border-radius: 4px;
  border: 1px solid #ddd;
  cursor: pointer;
}
.service-checkboxes label:hover {
  background: #eef;
}
.service-checkboxes input[type="checkbox"] {
  transform: scale(1.2);
}
.service-checkboxes span {
  flex: 1;
}
</style>

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
                                    <p>Mon - Fri, 8:00 - 9:00</p>
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
                                    <p>+012 345 6789</p>
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
                                    <p>info@example.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Top Bar End -->

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
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Dashboard</a>
                                <div class="dropdown-menu">
                                    <a href="booking.php" class="dropdown-item">Scheduled Bookings</a>
                                </div>
                            </div>
                        </div>
                        <div class="ml-auto">
                            <a class="btn btn-custom" href="appointment.php">Get Appointment</a>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    <!-- Nav Bar Start -->

    <!-- Edit Booking Form -->
    <div class="appointment-wrapper">
        <form method="POST" class="appointment-form-container">
            <div class="appointment-form-title">Edit Booking</div>

            <div class="appointment-form-group">
                <input type="text" name="name" class="appointment-input" value="<?= htmlspecialchars($booking['name']) ?>" required>
            </div>
            <div class="appointment-form-group">
                <input type="email" name="email" class="appointment-input" value="<?= htmlspecialchars($booking['email']) ?>" required>
            </div>
            <div class="appointment-form-group">
                <input type="text" name="contact_number" class="appointment-input" value="<?= htmlspecialchars($booking['contact_number']) ?>">
            </div>
            <div class="appointment-form-group">
                <input type="text" name="plate_number" class="appointment-input" value="<?= htmlspecialchars($booking['plate_number']) ?>">
            </div>
            <div class="appointment-form-group">
                <input type="date" name="preferred_date" class="appointment-input" value="<?= htmlspecialchars($booking['preferred_date']) ?>" required>
            </div>
            <div class="appointment-form-group">

       <!-- Services (Styled Checkboxes) -->
        <div class="appointment-form-group">
          <label style="display:block; margin-bottom:5px;">Select Services:</label>
          <div class="service-checkboxes">
            <label><span>Exterior Washing (₱250)</span><input type="checkbox" name="service_needed[]" value="Exterior Washing" data-price="250"></label>
            <label><span>Interior Washing (₱350)</span><input type="checkbox" name="service_needed[]" value="Interior Washing" data-price="350"></label>
            <label><span>Vacuum Cleaning (₱180)</span><input type="checkbox" name="service_needed[]" value="Vacuum Cleaning" data-price="180"></label>
            <label><span>Seats Washing (₱150)</span><input type="checkbox" name="service_needed[]" value="Seats Washing" data-price="150"></label>
            <label><span>Window Wiping (₱100)</span><input type="checkbox" name="service_needed[]" value="Window Wiping" data-price="100"></label>
            <label><span>Wet Cleaning (₱250)</span><input type="checkbox" name="service_needed[]" value="Wet Cleaning" data-price="250"></label>
            <label><span>Oil Changing (₱1,000)</span><input type="checkbox" name="service_needed[]" value="Oil Changing" data-price="1000"></label>
            <label><span>Brake Repairing (₱1,200)</span><input type="checkbox" name="service_needed[]" value="Brake Repairing" data-price="1200"></label>
            <label><span>Basic Cleaning (₱399)</span><input type="checkbox" name="service_needed[]" value="Basic Cleaning" data-price="399"></label>
            <label><span>Premium Cleaning (₱599)</span><input type="checkbox" name="service_needed[]" value="Premium Cleaning" data-price="599"></label>
            <label><span>Complex Cleaning (₱799)</span><input type="checkbox" name="service_needed[]" value="Complex Cleaning" data-price="799"></label>
          </div>
        </div>

            </div>
            <div class="appointment-form-group">
                <select name="car_size" class="appointment-input" required>
                    <option value="small" <?= $booking['car_size'] == 'small' ? 'selected' : '' ?>>Small</option>
                    <option value="medium" <?= $booking['car_size'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="large" <?= $booking['car_size'] == 'large' ? 'selected' : '' ?>>Large</option>
                </select>
            </div>
            
            <button type="submit" class="appointment-btn">Update Booking</button>
            
        </form>
    </div>
    
     <!-- Footer Start -->
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-contact">
                            <h2>Get In Touch</h2>
                            <p><i class="fa fa-map-marker-alt"></i>123 Street, New York, USA</p>
                            <p><i class="fa fa-phone-alt"></i>+012 345 67890</p>
                            <p><i class="fa fa-envelope"></i>info@example.com</p>
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
                            <a href="">About Us</a>
                            <a href="">Contact Us</a>
                            <a href="">Our Services</a>
                            <a href="">Service Points</a>
                            <a href="">Pricing Plan</a>
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
                <p>&copy; <a href="#">Your Site Name</a>, All Right Reserved. Designed By <a href="https://htmlcodex.com">HTML Codex</a></p>
            </div>
        </div>
        <!-- Footer End -->
</body>

</html>
