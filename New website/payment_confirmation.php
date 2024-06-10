<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $total = $_POST['total'];
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    if ($payment_method == "Online Payment") {
        // Implement online payment processing
        // For now, assume the online payment was successful
    } elseif ($payment_method == "Cash on Collection") {
        // Assume cash on collection
    } else {
        die("Invalid payment method.");
    }

    $stmt = $conn->prepare("UPDATE orders SET status = 'paid', payment_method = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $payment_method, $order_id, $user_id);

    if ($stmt->execute()) {
        header("Location: success.php?order_id=" . $order_id);
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - Plated by Phumi</title>
</head>
<body>
    <header>
        <h1>Payment Confirmation</h1>
    </header>
    <div class="container">
        <h2>Payment successful!</h2>
        <p>Your order ID: <?php echo htmlspecialchars($_GET['order_id']); ?></p>
        <a href="index.php" class="btn btn-primary">Return to Home</a>
    </div>
    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>
</body>
</html>
