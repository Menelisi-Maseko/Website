<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

require 'database.php';

// Fetch orders from the database
$query = "
    SELECT orders.id, users.username, orders.items, orders.total, orders.status, orders.payment_method
    FROM orders 
    JOIN users ON orders.user_id = users.id
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}

$result = $stmt->get_result();
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();

// Fetch menu items from the database
$menuQuery = "SELECT * FROM main_menu";
$menuStmt = $conn->prepare($menuQuery);

if (!$menuStmt) {
    die('Prepare failed: ' . $conn->error);
}

if (!$menuStmt->execute()) {
    die('Execute failed: ' . $menuStmt->error);
}

$menuResult = $menuStmt->get_result();
$menuItems = [];
while ($row = $menuResult->fetch_assoc()) {
    $menuItems[] = $row;
}

$menuStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="admin">
            <h2>Manage Orders</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Username</th>
                        <th>Order Details</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment Method</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo htmlspecialchars($order['items']); ?></td>
                            <td>R<?php echo htmlspecialchars($order['total']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td>
                                <form action="update_order_status.php" method="post">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                    <select name="status" class="form-control">
                                        <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Processed" <?php if ($order['status'] == 'Processed') echo 'selected'; ?>>Processed</option>
                                        <option value="Collected" <?php if ($order['status'] == 'Collected') echo 'selected'; ?>>Collected</option>
                                        <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                    </select>
                                    <select name="payment_method" class="form-control mt-2">
                                        <option value="Online Payment" <?php if ($order['payment_method'] == 'Online Payment') echo 'selected'; ?>>Online Payment</option>
                                        <option value="Cash on Collection" <?php if ($order['payment_method'] == 'Cash on Collection') echo 'selected'; ?>>Cash on Collection</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="admin">
            <h2>Manage Menu</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Menu ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Update Item</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menuItems as $item) : ?>
                        <tr>
                            <form action="update_menu_item.php" method="post" enctype="multipart/form-data">
                                <td><?php echo htmlspecialchars($item['id']); ?></td>
                                <td><input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" class="form-control" required></td>
                                <td><textarea name="description" class="form-control" required><?php echo htmlspecialchars($item['description']); ?></textarea></td>
                                <td><input type="number" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" class="form-control" required></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100px;">
                                    <input type="file" name="image" class="form-control-file">
                                </td>
                                <td>
                                    <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>
</body>
</html>
