<?php
session_start();
require 'database.php';

$result = $conn->query("SELECT * FROM main_menu");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plated by Phumi</title>
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
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="alert alert-warning" role="alert">
                You need to <a href="login.php" class="alert-link">login</a> or <a href="register.php" class="alert-link">create an account</a> to place orders.
            </div>
        <?php endif; ?>
       
        <section class="top-list">
            <h2>Our Demo Menu</h2>
            <p>Explore our main menu items</p>
            <div class="cards">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="price">R<?php echo number_format($row['price'], 2); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Plated by Phumi. All rights reserved.</p>
    </footer>
</body>
</html>
