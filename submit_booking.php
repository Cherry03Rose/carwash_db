<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Get form data
$plate_number = $_POST['plate_number'] ?? '';
$preferred_date = $_POST['preferred_date'] ?? '';
$preferred_time = $_POST['preferred_time'] ?? '08:00:00';
$car_size = $_POST['car_size'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';
$user_id = $_SESSION['user_id'];
$services = $_POST['service_needed'] ?? [];

// Validate required fields
if (empty($plate_number) || empty($preferred_date) || empty($car_size) || 
   empty($payment_method) || empty($services) || !is_array($services)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Check if vehicle exists or create new
    $stmt = $pdo->prepare("SELECT id FROM vehicles WHERE plate_number = ? AND user_id = ?");
    $stmt->execute([$plate_number, $user_id]);
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        // Create new vehicle
        $stmt = $pdo->prepare("INSERT INTO vehicles (user_id, plate_number, type) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $plate_number, $car_size]);
        $vehicle_id = $pdo->lastInsertId();
    } else {
        $vehicle_id = $vehicle['id'];
    }

    // 2. Calculate total price
    $placeholders = implode(',', array_fill(0, count($services), '?'));
    $stmt = $pdo->prepare("SELECT SUM(price) FROM services WHERE id IN ($placeholders)");
    $stmt->execute($services);
    $total_price = $stmt->fetchColumn();

    if (!$total_price) {
        throw new Exception("Invalid services selected");
    }

    // 3. Get primary service (first selected service)
    $primary_service_id = $services[0];
    
    // 4. Create booking
    $stmt = $pdo->prepare("INSERT INTO bookings 
        (user_id, vehicle_id, service_id, preferred_date, preferred_time, total_price, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([
        $user_id, 
        $vehicle_id, 
        $primary_service_id,
        $preferred_date, 
        $preferred_time, 
        $total_price
    ]);
    $booking_id = $pdo->lastInsertId();

    // 5. Add additional services to booking_services (skip primary service)
    if (count($services) > 1) {
        $stmt = $pdo->prepare("INSERT INTO booking_services (booking_id, service_id) VALUES (?, ?)");
        for ($i = 1; $i < count($services); $i++) {
            $stmt->execute([$booking_id, $services[$i]]);
        }
    }

    // 6. Create payment record
    $stmt = $pdo->prepare("INSERT INTO payments 
        (booking_id, amount, method, status) 
        VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$booking_id, $total_price, $payment_method]);

    $pdo->commit();

    echo json_encode([
        'success' => true, 
        'booking_id' => $booking_id,
        'message' => 'Booking created successfully'
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>