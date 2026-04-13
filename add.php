<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Prepare the SQL statement
        $sql = "INSERT INTO booking (name, email, contact_number, plate_number, preferred_date, service_needed, car_size)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        // Bind and execute values
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['contact_number'],
            $_POST['plate_number'],
            $_POST['preferred_date'],
            $_POST['service_needed'],
            $_POST['car_size']
        ]);

        // Redirect after successful booking
        header("Location: booking.php?success=1");
        exit();

    } catch (PDOException $e) {
        // Error handling
        echo "Error: " . $e->getMessage();
    }
}
?>
