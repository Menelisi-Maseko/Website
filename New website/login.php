<?php
session_start();
require 'database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, is_admin FROM users WHERE email = ?");
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            die('Execute failed: ' . $stmt->error);
        }

        $stmt->store_result();
        $stmt->bind_result($user_id, $hashed_password, $is_admin);

        if ($stmt->fetch() && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['is_admin'] = $is_admin;

            if ($is_admin) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: menu.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
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
    <title>Plated by Phumi - Login</title>
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
        <section class="login">
            <h2>Login</h2>
            <?php if ($error): ?>
                <p class="text-danger"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <p><a href="forgot_password.php">Forgot Password?</a></p>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>
</body>
</html>
