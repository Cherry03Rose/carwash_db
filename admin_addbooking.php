<?php
session_start();
include 'connect.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid CSRF token";
    header("Location: admin_addbooking_form.php");
    exit();
}

try {
    $pdo->beginTransaction();

    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact_number']);
    $plate_number = trim($_POST['plate_number']);
    $car_size = $_POST['car_size'];
    $services = $_POST['service_needed'] ?? [];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $user_id = $user['id'];
        $stmt = $pdo->prepare("UPDATE users SET name = ?, contact_number = ? WHERE id = ?");
        $stmt->execute([$name, $contact, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, contact_number) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, trim($_POST['password']), $contact]);
        $user_id = $pdo->lastInsertId();
    }

    // Check if vehicle exists
    $stmt = $pdo->prepare("SELECT id FROM vehicles WHERE user_id = ? AND plate_number = ?");
    $stmt->execute([$user_id, $plate_number]);
    $vehicle = $stmt->fetch();

    if ($vehicle) {
        $vehicle_id = $vehicle['id'];
        $stmt = $pdo->prepare("UPDATE vehicles SET type = ? WHERE id = ?");
        $stmt->execute([$car_size, $vehicle_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vehicles (user_id, plate_number, type) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $plate_number, $car_size]);
        $vehicle_id = $pdo->lastInsertId();
    }

    // Calculate total price
    $total_price = 0;
    if (!empty($services)) {
        $placeholders = implode(',', array_fill(0, count($services), '?'));
        $stmt = $pdo->prepare("SELECT SUM(price) FROM services WHERE id IN ($placeholders)");
        $stmt->execute($services);
        $total_price = $stmt->fetchColumn();
    }

    $primary_service_id = $services[0] ?? null;

    // Add booking
    $stmt = $pdo->prepare("INSERT INTO bookings 
        (user_id, vehicle_id, service_id, preferred_date, preferred_time, total_price, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([
        $user_id,
        $vehicle_id,
        $primary_service_id,
        $_POST['preferred_date'],
        $_POST['preferred_time'],
        $total_price
    ]);

    $booking_id = $pdo->lastInsertId();

    // Add booking services
    foreach ($services as $service_id) {
        $stmt = $pdo->prepare("INSERT INTO booking_services (booking_id, service_id) VALUES (?, ?)");
        $stmt->execute([$booking_id, $service_id]);
    }

    // Add payment
    $stmt = $pdo->prepare("INSERT INTO payments (booking_id, amount, method, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$booking_id, $total_price, $_POST['payment_method']]);

    $pdo->commit();
    $_SESSION['booking_success'] = "Booking added successfully!";
    header("Location: admin_addbooking_form.php");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error adding booking: " . $e->getMessage();
    header("Location: admin_addbooking_form.php");
    exit();
}
