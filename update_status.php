<?php
session_start();
include 'connect.php';

// Set header to return JSON
header('Content-Type: application/json');

// CSRF check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Role check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Sanitize and validate input
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$status = $_POST['status'] ?? '';

$valid_statuses = ['Pending', 'Approved', 'Completed', 'Cancelled'];

if ($booking_id <= 0 || !in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    // Perform the update
   $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $stmt->execute([$status, $booking_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made']);
    }
} catch (PDOException $e) {
    error_log("Update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
exit;
