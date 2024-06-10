<?php
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a token and expiry time (1 hour from now)
        $token = bin2hex(random_bytes(16));
        $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // Update the user's record with the token and expiry
        $stmt = $conn->prepare("UPDATE users SET password_reset_token = ?, token_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $token_expiry, $user_id);
        $stmt->execute();

        // Send the email with the reset link
        $reset_link = "http://yourdomain.com/reset_password.php?token=" . $token;
        $to = $email;
        $subject = "Password Reset Request";
        $message = "You requested a password reset. Click the following link to reset your password: " . $reset_link;
        $headers = "From: no-reply@yourdomain.com\r\n";
        
        if (mail($to, $subject, $message, $headers)) {
            echo "Password reset link has been sent to your email.";
        } else {
            echo "Failed to send email. Please try again.";
        }
    } else {
        echo "No account found with that email.";
    }

    $stmt->close();
}
?>
