<?php  
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception; 

require 'vendor/autoload.php'; 
include 'connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    // Sanitize and validate inputs
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

    // Basic server-side validation
    if (empty($name) || empty($email) || empty($password)) {
        $message = "<div id='flash-message' style='padding:12px; background-color:#f8d7da; color:#721c24; 
        border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
        font-family:Segoe UI, sans-serif; text-align:center;'>All fields are required.</div>";
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div id='flash-message' style='padding:12px; background-color:#f8d7da; color:#721c24; 
        border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
        font-family:Segoe UI, sans-serif; text-align:center;'>Invalid email format.</div>";
        return;
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $checkEmail->bindParam(':email', $email);
    $checkEmail->execute();

    if ($checkEmail->rowCount() > 0) {
        $message = "<div id='flash-message' style='padding:12px; background-color:#f8d7da; color:#721c24; 
        border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
        font-family:Segoe UI, sans-serif; text-align:center;'>This email is already registered.</div>";
        return;
    }

    // Generate verification token
    $token = bin2hex(random_bytes(50));

    // Store user (with plain password — for testing only)
    $sql = "INSERT INTO users (name, email, password, token) 
            VALUES (:name, :email, :password, :token)";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => $password,
            ':token'    => $token
        ]);

        // Send verification email
        $mail = new PHPMailer(true); 
        try {
            $mail->isSMTP(); 
            $mail->Host = 'localhost'; 
            $mail->SMTPAuth = false; 
            $mail->Port = 1025; 

            $mail->setFrom('no-reply@yourdomain.com', 'SIAL System'); 
            $mail->addAddress($email, $name); 
            $mail->isHTML(true); 
            $mail->Subject = 'Verify your email address';
            $mail->Body = "Hello <b>$name</b>,<br><br>
            Please click the link below to verify your email:<br><br>
            <a href='http://localhost/CARSERVICESfinal/verify.php?token=$token'>Verify Email</a><br><br>
            If you did not register, please ignore this message.";

            $mail->send(); 
            $message = "<div style='padding:12px; background-color:#d4edda; color:#155724; 
            border:1px solid #c3e6cb; border-radius:5px; margin:20px auto; width:500px; 
            font-family:Segoe UI, sans-serif; text-align:center;'>Registration successful. Please check your email for verification.</div>";
        } catch (Exception $e) {
            $message = "<div style='padding:12px; background-color:#f8d7da; color:#721c24; 
            border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
            font-family:Segoe UI, sans-serif; text-align:center;'>Mailer Error: {$mail->ErrorInfo}</div>";
        }

    } catch (PDOException $e) {
        $message = "<div style='padding:12px; background-color:#f8d7da; color:#721c24; 
        border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
        font-family:Segoe UI, sans-serif; text-align:center;'>Database Error: " . $e->getMessage() . "</div>";
    }
}
?>

