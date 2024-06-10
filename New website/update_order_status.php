<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status']) && isset($_POST['payment_method'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $payment_method = $_POST['payment_method'];

    $stmt = $conn->prepare("UPDATE orders SET status = ?, payment_method = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssi", $status, $payment_method, $order_id);

    if ($stmt->execute()) {
        header("Location: admin.php?message=Order status updated successfully");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
