<?php
$token = $_GET['token'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
      display: flex;
      max-width: 1020px;
      width: 98%;
      min-height: 600px;
      overflow: hidden;
    }

    .image-side {
      flex: 1.18;
      min-width: 270px;
      background: #eee;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .image-side img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .form-side {
      flex: 1.5;
      padding: 54px 44px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: #fff;
    }

    .form-title {
      font-size: 2.4rem;
      font-weight: 800;
      color: #202C45;
      margin-bottom: 38px;
      letter-spacing: 1.5px;
      text-align: center;
      text-shadow: 0 2px 8px rgba(32,44,69,0.05);
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 28px;
      width: 100%;
      max-width: 380px;
      margin: 0 auto;
    }

    .form-group {
      position: relative;
      display: flex;
      flex-direction: column;
    }

    input[type="password"] {
      padding: 15px 18px;
      border: 1.5px solid #dde1ee;
      border-radius: 10px;
      font-size: 1.16rem;
      background: #f6f8fa;
      transition: border-color 0.2s, box-shadow 0.2s;
      box-shadow: 0 1px 4px rgba(32,44,69,0.05);
      font-family: 'Segoe UI', Arial, sans-serif;
      outline: none;
    }

    input:focus {
      border-color: #202C45;
      background: #fff;
      box-shadow: 0 0 0 2px rgba(32,44,69,0.08);
    }

    .submit-btn {
      background: linear-gradient(90deg,#202C45 60%,#31416d 100%);
      color: #fff;
      border: none;
      border-radius: 22px;
      padding: 15px 52px;
      font-size: 1.18rem;
      font-weight: 700;
      letter-spacing: 1px;
      cursor: pointer;
      transition: background 0.18s, box-shadow 0.18s;
      box-shadow: 0 2px 12px rgba(32,44,69,0.10);
    }

    .submit-btn:hover {
      background: linear-gradient(90deg,#31416d 80%,#e53935 100%);
      box-shadow: 0 4px 16px rgba(229,57,53,0.14);
    }

    .form-footer {
      text-align: center;
      margin-top: 20px;
    }

    .form-footer a {
      color: #202C45;
      text-decoration: underline;
      font-weight: 500;
    }

    @media (max-width: 900px) {
      .container {
        flex-direction: column;
        min-height: unset;
        max-width: 98%;
      }

      .image-side {
        height: 180px;
        min-width: unset;
      }

      .form-side {
        padding: 26px 10px;
      }

      .form-title {
        font-size: 1.6rem;
        margin-bottom: 24px;
      }

      form {
        max-width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image-side">
      <img src="picforlogin.jpg" alt="Reset Password Visual">
    </div>
    <div class="form-side">
      <div class="form-title">Reset Your Password</div>
      <form action="update_password.php" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <div class="form-group">
          <input type="password" name="password" placeholder="New Password (min 8 characters)" required minlength="8">
        </div>
        <div class="form-group">
          <input type="password" name="confirmPassword" placeholder="Confirm New Password" required>
        </div>
        <button type="submit" class="submit-btn">Update Password</button>

        <div class="form-footer">
          <p>Back to <a href="login.php">Login</a></p>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
