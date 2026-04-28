<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// CSRF CHECK
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // INPUT CLEANING
    $product_id = intval($_POST['product_id']);
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity_in_stock']);
    $category = trim($_POST['category']);
    $sup_id = intval($_POST['sup_id']);

    // GET CURRENT IMAGE
    $stmt = $conn->prepare("SELECT product_img FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $product_img = $product['product_img'];
    $stmt->close();

    // IMAGE UPLOAD SECURITY
    if (!empty($_FILES['product_img']['name'])) {

        $allowed_types = ['jpg','jpeg','png','gif'];
        $image_name = time() . "_" . basename($_FILES["product_img"]["name"]);
        $target_dir = "images/";
        $target_file = $target_dir . $image_name;

        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_types)) {
            move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file);
            $product_img = $image_name;
        }
    }

    // UPDATE (SECURE)
    $sql = "UPDATE products 
            SET product_name=?, description=?, price=?, quantity_in_stock=?, category=?, sup_id=?, product_img=? 
            WHERE product_id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdisssi",
        $product_name,
        $description,
        $price,
        $quantity,
        $category,
        $sup_id,
        $product_img,
        $product_id
    );

    $stmt->execute();

    header("Location: AdminManageProducts.php?msg=updated");
    exit();
}
?>