<?php
include 'connect.php';
header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

echo json_encode(['exists' => $user ? true : false]);
