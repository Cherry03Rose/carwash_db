<?php
include 'connect.php';

$plate = trim($_POST['plate'] ?? '');

$response = ['success' => false];

if (!empty($plate)) {
    $stmt = $pdo->prepare("SELECT type FROM vehicles WHERE plate_number = ?");
    $stmt->execute([$plate]);
    $car = $stmt->fetch();

    if ($car) {
        $response = [
            'success' => true,
            'type' => $car['type']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
