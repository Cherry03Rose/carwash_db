<?php
session_start();
include 'connect.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$services = $pdo->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Booking</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        .booking-container {
            max-width: 650px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .booking-header h2 {
            font-weight: 700;
            color: #202C45;
        }

        .form-group label {
            font-weight: 600;
            color: #202C45;
        }

        .form-control {
            padding: 12px;
            border-radius: 5px;
        }

        .btn-submit {
            background: #202C45;
            color: white;
            width: 100%;
            padding: 12px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
        }

        .btn-submit:hover {
            background: #010e9eae;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            width: 100%;
            padding: 12px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #E81C2E;
            text-decoration: none;
        }

        .service-checkbox {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            background: #f9f9f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .service-checkbox label {
            margin: 0;
            flex-grow: 1;
        }

        .service-price {
            font-weight: bold;
            color: #202C45;
        }

        select.form-control {
            font-size: 16px;
            line-height: 1.5;
            padding: 0.6rem 1rem;
            height: auto;
        }
    </style>
</head>
<body>
<div class="booking-container">
    <div class="booking-header text-center mb-4">
        <h2>Add New Booking</h2>
    </div>

    <?php if (!empty($_SESSION['booking_success'])): ?>
        <div class="alert alert-success text-center">
            <?= $_SESSION['booking_success']; ?>
        </div>
        <script>
            setTimeout(() => {
                window.location.href = 'admin_bookings.php';
            }, 3000);
        </script>
        <?php unset($_SESSION['booking_success']); ?>
    <?php elseif (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="admin_addbooking.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group" id="password-group" style="display: none;">
            <label>Password (for new user)</label>
            <input type="text" name="password" id="password" class="form-control">
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contact_number" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Plate Number</label>
            <input type="text" name="plate_number" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Car Size</label>
            <select name="car_size" class="form-control" required>
                <option value="">Select Size</option>
                <option value="Small">Small</option>
                <option value="Medium">Medium</option>
                <option value="Large">Large</option>
            </select>
        </div>

        <div class="form-group">
            <label>Select Services</label>
            <?php foreach ($services as $service): ?>
                <div class="service-checkbox">
                    <input type="checkbox" name="service_needed[]" id="service_<?= $service['id'] ?>" value="<?= $service['id'] ?>" data-price="<?= $service['price'] ?>">
                    <label for="service_<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?></label>
                    <span class="service-price">₱<?= number_format($service['price'], 2) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-group">
            <label>Preferred Date</label>
            <input type="date" name="preferred_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-group">
            <label>Preferred Time</label>
            <select name="preferred_time" class="form-control" required>
                <option value="">Select Time</option>
                <?php for ($hour = 8; $hour <= 17; $hour++): 
                    $time = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                ?>
                    <option value="<?= $time ?>"><?= $time ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Payment Method</label>
            <select name="payment_method" class="form-control">
                <option value="Cash">Cash</option>
                <option value="GCash">GCash</option>
                <option value="Card">Credit/Debit Card</option>
            </select>
        </div>

        <div class="form-group">
            <label>Total Price</label>
            <input type="text" class="form-control" id="total_price" value="₱0.00" readonly>
        </div>

        <button type="submit" class="btn btn-submit">Add Booking</button>

        <div class="text-center mt-3">
            <a href="admin_booking_summary.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // Calculate total service price
    $('input[name="service_needed[]"]').change(function() {
        let total = 0;
        $('input[name="service_needed[]"]:checked').each(function() {
            total += parseFloat($(this).data('price'));
        });
        $('#total_price').val('₱' + total.toFixed(2));
    });

    // Show/hide password field based on email existence
    $('input[name="email"]').on('blur', function() {
        const email = $(this).val().trim();
        if (email !== '') {
            $.post('checkif_user_exists.php', { email: email }, function(response) {
                if (response.exists) {
                    $('#password-group').hide();
                    $('#password').prop('required', false);
                } else {
                    $('#password-group').show();
                    $('#password').prop('required', true);
                }
            }, 'json');
        }
    });

    // Autofill car size if plate number exists
    $('input[name="plate_number"]').on('blur', function () {
        const plate = $(this).val().trim();
        if (plate !== '') {
            $.post('get_car_size.php', { plate: plate }, function(response) {
                if (response.success) {
                    $('select[name="car_size"]').val(response.type);
                }
            }, 'json');
        }
    });
});
</script>

</body>
</html>
