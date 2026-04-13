<?php 
require 'connect.php';

$message = '';
$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'contact_number' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $formData['name'] = trim($_POST['name'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $formData['contact_number'] = trim($_POST['contact_number'] ?? '');

    // Validate inputs
    if (empty($formData['name'])) {
        $errors['name'] = "Name is required";
    }

    if (empty($formData['email'])) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    }

    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match";
    }

    // Validate contact number (optional)
    if (!empty($formData['contact_number'])) {
        if (!preg_match('/^\d{11}$/', $formData['contact_number'])) {
            $errors['contact_number'] = "Contact number must be exactly 11 digits (e.g., 09XXXXXXXXX)";
        }
    }

    // Check if email exists
    if (empty($errors['email'])) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$formData['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = "Email already registered";
        }
    }

    if (empty($errors)) {
        $token = time() . '-' . rand(1000, 9999);
        $plainPassword = $password;

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, contact_number, token, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt->execute([
            $formData['name'],
            $formData['email'],
            $plainPassword,
            $formData['contact_number'],
            $token
        ])) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            $message = '<div class="error-message">Registration failed. Please try again.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Account</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  height: 620px; /* fixed height for consistency */
  overflow: hidden;
}

.form-side {
  padding: 54px 44px;
  flex: 1.5;
  overflow-y: auto;  /* enable vertical scrolling */
  max-height: 100%;  /* restrict height to container */
  background: #fff;
  display: flex;
  flex-direction: column;
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


    form {
      display: flex;
      flex-direction: column;
      gap: 28px;
      width: 100%;
      max-width: 380px;
      margin: 0 auto;
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
    
    .form-group {
      position: relative;
      display: flex;
      flex-direction: column;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
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

    .signup-btn {
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

    .signup-btn:hover {
      background: linear-gradient(90deg,#31416d 80%,#e53935 100%);
      box-shadow: 0 4px 16px rgba(229,57,53,0.14);
    }

    .error-message {
      color: #e53935;
      font-size: 0.95rem;
      text-align: left;
      margin-top: 5px;
    }

    .success-message {
      color: #28a745;
      background: #d4edda;
      padding: 12px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
      border: 1px solid #c3e6cb;
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
    <img src="picforlogin.jpg" alt="Register Visual">
  </div>
  <div class="form-side">
    <?php if (!empty($message)) echo $message; ?>
    <div class="form-title">CREATE AN ACCOUNT</div>
    <form action="" method="POST"> 
      <div class="form-group">
        <input type="text" id="name" name="name" placeholder="Name" required value="<?= htmlspecialchars($formData['name']) ?>">
        <?php if (isset($errors['name'])) echo '<div class="error-message">'.$errors['name'].'</div>'; ?>
      </div>

      <div class="form-group">
        <input type="email" id="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($formData['email']) ?>">
        <?php if (isset($errors['email'])) echo '<div class="error-message">'.$errors['email'].'</div>'; ?>
      </div>

      <div class="form-group">
        <input type="text" id="contact_number" name="contact_number" placeholder="Phone Number" maxlength="11" pattern="\d{11}" title="Enter exactly 11 digits starting with 09" value="<?= htmlspecialchars($formData['contact_number']) ?>"  oninput="this.value = this.value.replace(/\D/g, '')">
        <?php if (isset($errors['contact_number'])) echo '<div class="error-message">'.$errors['contact_number'].'</div>'; ?>
      </div>

      <div class="form-group">
        <input type="password" id="password" name="password" placeholder="Password" required minlength="8">
        <i class="fa-solid fa-eye-slash" id="togglePassword"></i>
        <?php if (isset($errors['password'])) echo '<div class="error-message">'.$errors['password'].'</div>'; ?>
      </div>

      <div class="form-group">
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
        <i class="fa-solid fa-eye-slash" id="toggleConfirmPassword"></i>
        <?php if (isset($errors['confirmPassword'])) echo '<div class="error-message">'.$errors['confirmPassword'].'</div>'; ?>
      </div>

      <div class="form-actions">
        <button type="submit" class="signup-btn">Sign Up</button>
        <div style="margin: 10px 0; font-size: 0.9rem; color: #777;">or</div>
        <a href="google-signin.php" class="google-signin-btn">
          <img src="https://www.svgrepo.com/show/303108/google-icon-logo.svg" alt="Google Logo" style="width: 20px; height: 20px;">
          Sign up with Google
        </a>
        <p style="margin: 16px 0 0; font-size: 0.95rem; color: #555;">
          Already have an account?
          <a href="login.php" style="color: #202C45; text-decoration: underline; font-weight: 500;">Sign In</a>
        </p>
      </div>
    </form>
  </div>
</div>

<script>
  const togglePassword = document.getElementById('togglePassword');
  const password = document.getElementById('password');

  togglePassword.addEventListener('click', () => {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    togglePassword.classList.toggle('fa-eye');
    togglePassword.classList.toggle('fa-eye-slash');
  });

  const toggleConfirm = document.getElementById('toggleConfirmPassword');
  const confirmPassword = document.getElementById('confirmPassword');

  toggleConfirm.addEventListener('click', () => {
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    toggleConfirm.classList.toggle('fa-eye');
    toggleConfirm.classList.toggle('fa-eye-slash');
  });

  // Auto-dismiss messages after 5 seconds
  setTimeout(() => {
    const messages = document.querySelectorAll('.error-message, .success-message');
    messages.forEach(msg => {
      msg.style.transition = 'opacity 0.5s ease';
      msg.style.opacity = '0';
      setTimeout(() => msg.remove(), 500);
    });
  }, 5000);
</script>
</body>
</html>
