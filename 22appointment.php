 <?php
session_start(); 
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
        
 <!-- Booking Appointment Section -->
<div class="appointment-wrapper">
  <div class="col-lg-5">
    <div class="appointment-form-container">
      <h3 class="appointment-form-title">Book an Appointment</h3>

      <!-- Booking Form -->
      <form class="appointment-form" id="appointmentForm">
        <!-- Name -->
        <div class="appointment-form-group">
          <input
            type="text"
            class="appointment-input"
            name="name"
            placeholder="Name (e.g., Juan Dela Cruz)"
            required
          />
        </div>

        <!-- Email -->
        <div class="appointment-form-group">
          <input
            type="email"
            class="appointment-input"
            name="email"
            placeholder="Email (e.g., juan@example.com)"
            required
          />
        </div>

        <!-- Contact Number -->
        <div class="appointment-form-group">
          <input
            type="tel"
            class="appointment-input"
            name="contact_number"
            placeholder="Contact Number (e.g., 09171234567)"
            pattern="09[0-9]{9}"
            maxlength="11"
            required
          />
        </div>

        <!-- Plate Number -->
        <div class="appointment-form-group">
          <input
            type="text"
            class="appointment-input"
            name="plate_number"
            placeholder="Plate Number"
            required
          />
        </div>

        <!-- Preferred Date -->
        <div class="appointment-form-group">
          <input
            type="date"
            class="appointment-input"
            name="preferred_date"
            required
          />
        </div>

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

        <!-- Car Size -->
        <div class="appointment-form-group">
          <select
            class="appointment-input"
            name="car_size"
            id="carSizeSelect"
            required
          >
            <option value="" disabled selected>Select Car Size</option>
            <option value="small">Small</option>
            <option value="medium">Medium</option>
            <option value="large">Large</option>
          </select>
        </div>

        <!-- Submit Button -->
        <div class="appointment-form-group">
          <button class="appointment-btn" type="submit">Send Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Styling for Checkboxes -->
<style>
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

<!-- JavaScript for AJAX Form Submission with Total Price -->
<script>
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const checkedBoxes = Array.from(
    document.querySelectorAll('input[name="service_needed[]"]:checked')
  );

  if (checkedBoxes.length === 0) {
    alert('Please select at least one service.');
    return;
  }

  let totalPrice = 0;
  const selectedServices = checkedBoxes.map(box => {
    const price = parseFloat(box.dataset.price) || 0;
    totalPrice += price;
    return box.value;
  });

  const form = e.target;
  const formData = new FormData(form);
  formData.append('total_price', totalPrice);
  formData.append('selected_services', JSON.stringify(selectedServices));

  fetch('submit_booking.php', {
    method: 'POST',
    body: formData,
  })
  .then(response => {
    if (response.redirected) {
      window.location.href = response.url;
    } else {
      return response.text().then(text => {
        alert(
          'Server Response: ' + text +
          '\n\nSelected Services: ' + selectedServices.join(', ') +
          '\nTotal Price: ₱' + totalPrice
        );
      });
    }
  })
  .catch(error => {
    alert('Error submitting form: ' + error.message);
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
