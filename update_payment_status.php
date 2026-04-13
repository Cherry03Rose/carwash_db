<?php
session_start();
require 'connect.php';

header('Content-Type: application/json');

// Validate CSRF token
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

// Validate input
$payment_id = $_POST['payment_id'] ?? '';
$status = $_POST['status'] ?? '';
$validStatuses = ['pending', 'paid', 'failed', 'refunded'];

if (!in_array($status, $validStatuses) || !is_numeric($payment_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

try {
    // Check if related booking is cancelled
    $bookingStatusStmt = $pdo->prepare("
        SELECT b.status 
        FROM bookings b
        JOIN payments p ON b.id = p.booking_id
        WHERE p.id = :payment_id
    ");
    $bookingStatusStmt->execute([':payment_id' => $payment_id]);
    $bookingStatus = strtolower($bookingStatusStmt->fetchColumn());

    $disable = false;
    if ($bookingStatus === 'cancelled') {
        $status = 'failed'; // Force to failed
        $message = 'Booking was cancelled. Payment status set to failed.';
        $disable = true;
    } else {
        $message = 'Payment status updated successfully.';
    }

    // Update status, paid_at, and transaction_id if paid
    if ($status === 'paid') {
        $transaction_id = bin2hex(random_bytes(5)); // 10-character random ID
        $stmt = $pdo->prepare("UPDATE payments SET status = :status, paid_at = NOW(), transaction_id = :transaction_id WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':transaction_id' => $transaction_id,
            ':id' => $payment_id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE payments SET status = :status, paid_at = NULL, transaction_id = NULL WHERE id = :id");
        $stmt->execute([
            ':status' => $status,
            ':id' => $payment_id
        ]);
    }

    echo json_encode([
    'success' => true,
    'message' => $message,
    'status' => $status,
    'disable' => $disable,
    'transaction_id' => $status === 'paid' ? $transaction_id : null,
    'paid_at' => $status === 'paid' ? date('Y-m-d H:i:s') : null
]);
    exit;

} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}
