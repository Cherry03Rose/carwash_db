<?php
require 'connect.php';
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;

$message = '';
$isSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate token and expiry
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Save token to DB
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);

        // Send email via MailHog
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->Port = 1025;
            $mail->SMTPAuth = false;

            $mail->setFrom('no-reply@autowash.test', 'AutoWash');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $link = "http://localhost/CARSERVICESfinal/reset_password.php?token=$token";
            $mail->Body = "Click to reset your password: <a href='$link'>$link</a>";

            $mail->send();
            $message = "We've sent a password reset link to your email. Please check your inbox and follow the instructions to reset your password.";
            $isSuccess = true;
        } catch (Exception $e) {
            $message = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "No account found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Password Reset Status</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
      background: radial-gradient(circle at top left, #202C45 60%, white 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .container {
      background: #fff;
      border-radius: 22px;
      box-shadow: 0 10px 38px rgba(32,44,69,0.14);
      padding: 50px 40px;
      text-align: center;
      max-width: 600px;
      width: 90%;
    }

    .message {
      padding: 20px;
      border-radius: 10px;
      font-size: 1.1rem;
      margin-top: 10px;
    }

    .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    a {
      color: #155724;
      font-weight: bold;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Password Reset</h2>
    <div class="message <?= $isSuccess ? 'success' : 'error' ?>">
      <?= $message ?>
    </div>
  </div>
</body>
</html>
