<?php
require_once 'vendor/autoload.php';
require_once 'connect.php';
session_start();

// Initialize Google Client
$client = new Google_Client();
$client->setClientId('718915719369-b9i4hlk8pklk2o8an0jl1o7ipo8obttc.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/CARSERVICESfinal/google-callback.php');
$client->addScope('email');
$client->addScope('profile'); // ✅ Correct scope

// Handle OAuth response
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        // Get user profile info
        $oauth = new Google_Service_Oauth2($client);
        $googleUser = $oauth->userinfo->get();

        $email = $googleUser->email;
        $name = $googleUser->name;
        $picture = $googleUser->picture;

        // Check if user already exists in the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            // New user — insert into database
            $insert = $pdo->prepare("INSERT INTO users (name, email, picture, created_at) VALUES (?, ?, ?, NOW())");
            $insert->execute([$name, $email, $picture]);

            $_SESSION['new_user'] = true;
            $userId = $pdo->lastInsertId();
        } else {
            $_SESSION['returning_user'] = true;
            $userId = $user['id'];
        }

        // ✅ Store correct session data
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_photo'] = $picture; // ✅ FIXED

        // Redirect to homepage
        header('Location: index.php');
        exit;
    } else {
        echo "Google login failed: " . htmlspecialchars($token['error']);
    }
} else {
    echo "No code parameter found.";
}
?>
