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
    <title>Plated by Phumi - Checkout</title>
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
        <section class="checkout">
            <h2>Order Details</h2>
            <p>Order ID: <?php echo $order['id']; ?></p>
            <p>Total: R<?php echo number_format($order['total'], 2); ?></p>
            <p>Items:</p>
            <ul>
                <?php $items = json_decode($order['items'], true);
                foreach ($items as $name => $quantity): ?>
                    <li><?php echo $name . ': ' . $quantity; ?></li>
                <?php endforeach; ?>
            </ul>
            <button class="btn btn-primary" onclick="makePayment()">Make Payment</button>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>

    <script>
        function makePayment() {
            window.location.href = "payment.php?order_id=<?php echo $order['id']; ?>";
        }
    </script>
</body>
</html>
