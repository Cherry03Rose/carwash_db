<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $bookingId = $_POST['booking_id'];

    // Validate the booking ID
    if (!is_numeric($bookingId)) {
        header('Location: booking.php?error=invalid_id');
        exit();
    }

    // Update the booking status to 'Completed'
    $updateQuery = "UPDATE booking SET status = 'Completed' WHERE id = :id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->bindValue(':id', (int)$bookingId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: booking.php?completed=1');
        exit();
    } else {
        header('Location: booking.php?error=update_failed');
        exit();
    }
} else {
    header('Location: booking.php?error=invalid_request');
    exit();
}