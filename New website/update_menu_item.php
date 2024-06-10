<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu_id = $_POST['menu_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_path = null;

    // Handle file upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        
        // Check if the uploads directory exists, create if not
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }

        // Check file size
        if ($_FILES["image"]["size"] > 500000) {
            die("Sorry, your file is too large.");
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            die("Sorry, there was an error uploading your file.");
        }
    }

    // Update the menu item
    if ($image_path) {
        $stmt = $conn->prepare("UPDATE main_menu SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $name, $description, $price, $image_path, $menu_id);
    } else {
        $stmt = $conn->prepare("UPDATE main_menu SET name = ?, description = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $name, $description, $price, $menu_id);
    }

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        die("Error updating record: " . $stmt->error);
    }
}
?>
