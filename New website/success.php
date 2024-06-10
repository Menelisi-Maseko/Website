<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require 'database.php';

if (!isset($_GET['order_id'])) {
    header("Location: menu.php");
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: menu.php");
    exit();
}

$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - Plated by Phumi</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <header>
        <h1>Plated by Phumi</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="success">
            <h2>Thank You for Your Order!</h2>
            <p>Order ID: <?php echo $order['id']; ?></p>
            <p>Total: R<?php echo number_format($order['total'], 2); ?></p>
            <p>Items:</p>
            <ul>
                <?php 
                $items = json_decode($order['items'], true);
                foreach ($items as $name => $quantity): ?>
                    <li><?php echo htmlspecialchars($name) . ': ' . htmlspecialchars($quantity); ?></li>
                <?php endforeach; ?>
            </ul>
            <p>Thank you for your support! Your order will be ready for collection at the following address:</p>
            <p><strong>Address:</strong></p>
            <p>21 Main Avenue, Bethal , New Bethal east</p>
            <p>Please bring proof of payment when collecting your order. We look forward to serving you!</p>
            <p><strong>Note order will be processed once payment is reflected.:</strong></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>
</body>
</html>
