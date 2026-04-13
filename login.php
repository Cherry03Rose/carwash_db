<?php
session_start();
$registered = isset($_GET['registered']) && $_GET['registered'] == 1;
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'connect.php';

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $errors['email'] = 'Account with this email does not exist.';
        } elseif (!empty($user['password']) && $user['password'] !== $password && !password_verify($password, $user['password'])) {
            $errors['password'] = 'Incorrect password.';
        } else {
            //Set session variables including role
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? 'user'; // fallback to 'user'
            $_SESSION['login_success'] = true;

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin_booking_summary.php');
            } else {
                header('Location: index.php');
            }
            exit();
        }
    }
}
?>

<?php
$verifiedMessage = '';
if (isset($_GET['verified']) && $_GET['verified'] == 1) {
    $verifiedMessage = "<div id='flash-message' style='padding:12px; background-color:#d4edda; color:#155724;
    border:1px solid #c3e6cb; border-radius:5px; margin:20px auto; width:500px;
    font-family:Segoe UI, sans-serif; text-align:center;'>Your email has been verified. You can now log in.</div>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SIGN IN</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Font Awesome for eye icon -->
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
     height: 620px;/* 🔄 FIXED HEIGHT for consistent container size */
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

    input[type="email"],
    input[type="password"],
    input[type="text"] {
      padding: 15px 45px 15px 18px;
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

    .form-group i {
      position: absolute;
      top: 50%;
      right: 16px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #888;
      font-size: 1rem;
    }

    .form-actions {
      margin-top: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
    }

    .signin-btn {
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

    .signin-btn:hover {
      background: linear-gradient(90deg,#31416d 80%,#e53935 100%);
      box-shadow: 0 4px 16px rgba(229,57,53,0.14);
    }

    .error-message {
      color: #e53935;
      font-size: 0.95rem;
      text-align: left;
      margin-top: 5px;
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

    .google-signin-btn {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 12px 24px;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      font-size: 15px;
      color: #555;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      transition: all 0.2s ease-in-out;
    }

    .google-signin-btn:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-color: #aaa;
      color: #222;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image-side">
      <img src="picforlogin.jpg" alt="Login Visual">
    </div>
    <div class="form-side">
         <?php if ($registered): ?>
        <p style="color: green; text-align: center; font-weight: bold; margin-bottom: 20px;">
          Registration successful! Please log in.
        </p>
      <?php endif; ?>
  <?php if (!empty($verifiedMessage)) echo $verifiedMessage; ?>
      <div class="form-title">SIGN IN</div>
      <form action="" method="POST">
        <div class="form-group">
          <input type="email" id="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>" required>
          <?php if (!empty($errors['email'])): ?>
            <div class="error-message"><?= $errors['email'] ?></div>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <input type="password" id="password" name="password" placeholder="Password" required>
          <i class="fa-solid fa-eye-slash" id="togglePassword"></i>
          <?php if (!empty($errors['password'])): ?>
            <div class="error-message"><?= $errors['password'] ?></div>
          <?php endif; ?>
        </div>

        <div style="text-align: right; margin-top: -20px; margin-bottom: 20px;">
          <a href="forgot_password.php" style="color: #202C45; text-decoration: underline; font-weight: 500;">Forgot password?</a>
        </div>

        <div class="form-actions">
          <button type="submit" class="signin-btn">Sign In</button>
          <div style="margin: 10px 0; font-size: 0.9rem; color: #777;">or</div>
          <a href="google-signin.php" class="google-signin-btn">
            <img src="https://www.svgrepo.com/show/303108/google-icon-logo.svg" alt="Google Logo" style="width: 20px; height: 20px;">
            Sign in with Google
          </a>
          <p style="margin: 16px 0 0; font-size: 0.95rem; color: #555;">
            Don't have an account?
            <a href="register.php" style="color: #202C45; text-decoration: underline; font-weight: 500;">Sign Up</a>
          </p>
        </div>
      </form>
    </div>
  </div>

  <script>

     setTimeout(() => {
    const flash = document.getElementById('flash-message');
    if (flash) {
      flash.style.transition = 'opacity 0.5s ease';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 500); // removes the element after fading out
    }
  }, 3000);

    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    toggle.addEventListener('click', () => {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      toggle.classList.toggle('fa-eye');
      toggle.classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html>
