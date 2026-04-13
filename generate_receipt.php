<?php
// Increase resource limits BEFORE anything heavy loads
set_time_limit(300);
ini_set('memory_limit', '1024M');

require 'vendor/autoload.php';
include 'connect.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Validate ID
if (!isset($_GET['id'])) {
die("No appointment ID provided.");
}

$bookingId = $_GET['id'];

// Fetch booking from DB
$stmt = $pdo->prepare("SELECT * FROM booking WHERE id = ?");
$stmt->execute([$bookingId]);
$booking = $stmt->fetch();

if (!$booking) {
die("Appointment not found.");
}

// Decode services JSON
$services = json_decode($booking['service_needed'], true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($services)) {
$services = [$booking['service_needed']];
}
$servicesDisplay = implode(", ", $services);

// Format total price
$totalPrice = isset($booking['total_price']) ? "₱" . number_format($booking['total_price'], 2) : "N/A";

// Setup Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false);
$dompdf = new Dompdf($options);

// HTML content for PDF
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            color: #202C45;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section strong {
            display: inline-block;
            width: 150px;
        }
        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <h1>AutoWash Appointment Receipt</h1>
    <hr>
    <div class="section"><strong>Name:</strong> ' . htmlspecialchars($booking['name']) . '</div>
    <div class="section"><strong>Email:</strong> ' . htmlspecialchars($booking['email']) . '</div>
    <div class="section"><strong>Contact Number:</strong> ' . htmlspecialchars($booking['contact_number']) . '</div>
    <div class="section"><strong>Plate Number:</strong> ' . htmlspecialchars($booking['plate_number']) . '</div>
    <div class="section"><strong>Preferred Date:</strong> ' . htmlspecialchars($booking['preferred_date']) . '</div>
    <div class="section"><strong>Services:</strong> ' . htmlspecialchars($servicesDisplay) . '</div>
    <div class="section"><strong>Total Price:</strong> ' . $totalPrice . '</div>
    <div class="section"><strong>Car Size:</strong> ' . htmlspecialchars($booking['car_size']) . '</div>
    <hr>
    <div class="footer">Thank you for booking with AutoWash!</div>
</body>
</html>
';

// Generate PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("appointment_receipt_" . $bookingId . ".pdf", ["Attachment" => false]);
exit;
