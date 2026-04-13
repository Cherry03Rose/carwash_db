<?php
require 'connect.php';

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirmPassword'];

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        // Validate token and expiry
        $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user && strtotime($user['token_expiry']) > time()) {
            // Update password and clear token
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
            $stmt->execute([$password, $user['id']]);

            $message = "Password updated successfully. <a href='login.php' style='color: #155724; text-decoration: underline;'>Click here to log in.</a>";
            $success = true;
        } else {
            $message = "Invalid or expired token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Password Reset Result</title>
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
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Password Reset</h2>
    <div class="message <?= $success ? 'success' : 'error' ?>">
      <?= $message ?>
    </div>
  </div>
</body>
</html>
