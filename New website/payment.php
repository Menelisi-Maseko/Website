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
$payment_link = "https://www.example.com/pay?order_id=" . $order['id'] . "&amount=" . $order['total']; // Replace with actual payment link

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Plated by Phumi</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Payment</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Complete your payment here</h2>
        <p>Order ID: <?php echo $order['id']; ?></p>
        <p>Total: R<?php echo number_format($order['total'], 2); ?></p>
        <p>Items:</p>
        <ul>
            <?php $items = json_decode($order['items'], true);
            foreach ($items as $name => $quantity): ?>
                <li><?php echo htmlspecialchars($name) . ': ' . htmlspecialchars($quantity); ?></li>
            <?php endforeach; ?>
        </ul>
        <form action="payment_confirmation.php" method="post">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
            <input type="hidden" name="total" value="<?php echo htmlspecialchars($order['total']); ?>">
            <label>
                <input type="radio" name="payment_method" value="Online Payment" checked> Online Payment
            </label>
            <label>
                <input type="radio" name="payment_method" value="Cash on Collection"> Cash on Collection
            </label>
            <button type="submit" class="btn btn-primary">Confirm Payment</button>
        </form>
        <h3>Scan to Pay:</h3>
        <img src="images\Scan.png" alt="QR Code"></a>
        <p>
            Bank: Nedbank<br>
            Account Name: Plated by Phumi<br>
            Account Type: Savings<br>
            Account Number: 1208 397 966<br>
            Branch Code: 987654<br>
        </p>
        <p>Please include your Order ID as the payment reference.</p>
    </div>
    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>
</body>
</html>
