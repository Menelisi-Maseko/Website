<?php
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 4) {
        echo "Password must be at least 4 characters.";
    } elseif ($new_password !== $confirm_password) {
        echo "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Validate the token and its expiry time
        $stmt = $conn->prepare("SELECT id FROM users WHERE password_reset_token = ? AND token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id);

        if ($stmt->fetch()) {
            // Update the user's password and clear the token and expiry time
            $stmt = $conn->prepare("UPDATE users SET password = ?, password_reset_token = NULL, token_expiry = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);

            if ($stmt->execute()) {
                echo "Password has been reset.";
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Invalid or expired token.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plated by Phumi - Reset Password</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <header>
        <h1>Plated by Phumi</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="reset-password">
            <h2>Reset Password</h2>
            <form action="reset_password.php" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>
</body>
</html>
