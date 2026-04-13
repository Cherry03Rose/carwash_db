<?php
include 'connect.php';

// Check if ID is provided and numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Update booking status to 'cancelled'
        $stmt = $pdo->prepare("UPDATE booking SET status = 'Cancelled' WHERE id = ?");
        $stmt->execute([$id]);

        // Redirect with success
        header("Location: booking.php?cancelled=1");
        exit();
    } catch (PDOException $e) {
        echo "Error cancelling booking: " . $e->getMessage();
    }
} else {
    echo "Invalid ID.";
    exit();
}
?>
