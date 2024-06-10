<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

require 'database.php';

$result = $conn->query("SELECT * FROM main_menu");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $items = json_encode($_POST['items']);
    $total = $_POST['total'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO orders (user_id, items, total) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $user_id, $items, $total);

    if ($stmt->execute()) {
        header("Location: checkout.php?order_id=" . $stmt->insert_id);
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
    <title>Plated by Phumi - Main Menu</title>
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
        <section class="top-list">
            <h2>Our Mainstay Menu</h2>
            <p>Explore our main menu items</p>
            <form action="order.php" method="post" id="orderForm">
                <div class="cards">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                                <input type="number" name="items[<?php echo htmlspecialchars($row['name']); ?>]" value="0" min="0">
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div>
                    <p>Total: $<span id="total">0.00</span></p>
                    <input type="hidden" name="total" id="totalInput" value="0">
                    <button type="submit">Place Order</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('orderForm').addEventListener('input', function() {
            let total = 0;
            document.querySelectorAll('input[type="number"]').forEach(input => {
                const price = parseFloat(input.parentElement.querySelector('.price').textContent.replace('$', ''));
                total += input.value * price;
            });
            document.getElementById('total').textContent = total.toFixed(2);
            document.getElementById('totalInput').value = total.toFixed(2);
        });
    </script>
</body>
</html>
