<?php
include 'connect.php';

if (!isset($_GET['id'])) {
    header('Location: appointment.php');
    exit;
}

$bookingId = $_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            b.*,
            u.name, u.email, u.contact_number,
            v.plate_number, v.type AS car_size,
            s.name AS service_name, s.price AS service_price,
            p.method AS payment_method
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN vehicles v ON b.vehicle_id = v.id
        JOIN services s ON b.service_id = s.id
        JOIN payments p ON p.booking_id = b.id
        WHERE b.id = ?
    ");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        header('Location: appointment.php');
        exit;
    }

    // Get all services for this booking
    $servicesStmt = $pdo->prepare("
        SELECT s.name, s.price 
        FROM booking_services bs
        JOIN services s ON bs.service_id = s.id
        WHERE bs.booking_id = ?
    ");
    $servicesStmt->execute([$bookingId]);
    $additional_services = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Add primary service to the list
    $services = [
        ['name' => $booking['service_name'], 'price' => $booking['service_price']]
    ];
    $services = array_merge($services, $additional_services);

    // Calculate total price
    $totalPrice = 0;
    foreach ($services as $service) {
        $totalPrice += $service['price'];
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: appointment.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Confirmed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Barlow', sans-serif;
            background-color: #f8f9fa;
            padding: 40px 20px;
        }
        .card {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
            padding: 40px;
        }
        h2 {
            color:#202C45;
            font-weight: 700;
            font-size: 36px;
        }
        p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        ul {
            list-style: none;
            padding-left: 0;
            font-size: 18px;
            line-height: 1.8;
        }
        li strong {
            display: inline-block;
            width: 180px;
            color: #343a40;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 40px;
        }
        .btn {
            padding: 14px 28px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-back {
            background-color: #ffffff;
            color: #343a40;
            border: 2px solid #dee2e6;
        }
        .btn-back:hover {
            background-color: #f8f9fa;
        }
        .btn-view {
            background-color:#202C45;
            color: #fff;
        }
        .btn-view:hover {
            background-color:#1a2439;
        }
        @media (max-width: 600px) {
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2 class="text-center">Thank you for booking with us!</h2>
        <p class="text-center">Here is the summary of your appointment:</p>
        <ul>
            <li><strong>Name:</strong> <?= htmlspecialchars($booking['name']) ?></li>
            <li><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></li>
            <li><strong>Contact:</strong> <?= htmlspecialchars($booking['contact_number']) ?></li>
            <li><strong>Plate Number:</strong> <?= htmlspecialchars($booking['plate_number']) ?></li>
            <li><strong>Date:</strong> <?= htmlspecialchars($booking['preferred_date']) ?> at <?= htmlspecialchars($booking['preferred_time']) ?></li>
            <li><strong>Services:</strong> 
                <?php foreach ($services as $service): ?>
                    <div><?= htmlspecialchars($service['name']) ?> (₱<?= number_format($service['price'], 2) ?>)</div>
                <?php endforeach; ?>
            </li>
            <li><strong>Car Size:</strong> <?= htmlspecialchars($booking['car_size']) ?></li>
            <li><strong>Payment Method:</strong> <?= ucfirst(htmlspecialchars($booking['payment_method'])) ?></li>
            <li><strong>Total Price:</strong> ₱<?= number_format($totalPrice, 2) ?></li>
        </ul>
        
        <div class="btn-container">
            <a href="index.php" class="btn btn-back">Back to Home</a>
            <a href="generate_receipt.php?id=<?= $bookingId ?>" class="btn btn-view" target="_blank">Download Receipt</a>
        </div>
    </div>
</body>
</html>