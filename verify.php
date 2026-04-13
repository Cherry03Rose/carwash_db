<?php 
include 'connect.php';

if (isset($_GET['token'])) {
    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING); 

    if ($token) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE token = :token LIMIT 1"); 
        $stmt->bindParam(':token', $token); 

        try {
            $stmt->execute(); 
            if ($stmt->rowCount() > 0) {
                $updateStmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE token = :token"); 
                $updateStmt->bindParam(':token', $token); 

                if ($updateStmt->execute()) {
                    // ✅ Redirect to login.php with success message
                    header("Location: login.php?verified=1");
                    exit;
                } else {
                    echo "<div id='flash-message' style='padding:12px; background-color:#f8d7da; color:#721c24; 
                    border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
                    font-family:Segoe UI, sans-serif; text-align:center;'>Error updating verification status.</div>";
                }
            } else {
                echo "<div id='flash-message' style='padding:12px; background-color:#f8d7da; color:#721c24; 
                border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
                font-family:Segoe UI, sans-serif; text-align:center;'>Invalid or expired token.</div>";
            }
        } catch (PDOException $e) {
            echo "<div id='flash-message' style='padding:12px; background-color:#f8d7da; color:#721c24; 
            border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
            font-family:Segoe UI, sans-serif; text-align:center;'>Database Error: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div id='flash-message' style='padding:12px; background-color:#f8d7da; color:#721c24; 
        border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
        font-family:Segoe UI, sans-serif; text-align:center;'>Token is invalid.</div>";
    }
} else {
    echo "<div id='flash-message' style='padding:12px; background-color:#f8d7da; color:#721c24; 
    border:1px solid #f5c6cb; border-radius:5px; margin:20px auto; width:500px; 
    font-family:Segoe UI, sans-serif; text-align:center;'>No token provided.</div>";
}

$conn = null;
?>
