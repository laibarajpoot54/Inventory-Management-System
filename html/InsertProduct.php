<?php
include("../Config/db.php");

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ Get & sanitize input
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $quantity_in_stock = $_POST['quantity_in_stock'];
    $category = trim($_POST['category']);
    $sup_id = $_POST['sup_id'];

    // ✅ Validation
    if (empty($product_name) || empty($price) || empty($quantity_in_stock) || empty($category) || empty($sup_id)) {
        die("❌ All fields are required.");
    }

    if (!is_numeric($price) || $price < 0) {
        die("❌ Invalid price.");
    }

    if (!is_numeric($quantity_in_stock) || $quantity_in_stock < 0) {
        die("❌ Invalid quantity.");
    }

    // ✅ Image Upload Handling
    $image_path = "";

    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === 0) {

        $image_name = $_FILES['product_img']['name'];
        $image_tmp = $_FILES['product_img']['tmp_name'];

        $target_dir = "images/";
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        // Allowed types
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image_ext, $allowed_types)) {
            die("❌ Only JPG, JPEG, PNG, GIF allowed.");
        }

        // Unique file name (important)
        $new_image_name = time() . "_" . basename($image_name);
        $image_path = $target_dir . $new_image_name;

        if (!move_uploaded_file($image_tmp, $image_path)) {
            die("❌ Failed to upload image.");
        }
    }

    // ✅ Prepared Statement (SQL Injection Safe)
    $stmt = $conn->prepare("INSERT INTO products 
        (product_name, description, price, quantity_in_stock, category, sup_id, product_img) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssdisis",
        $product_name,
        $description,
        $price,
        $quantity_in_stock,
        $category,
        $sup_id,
        $image_path
    );

    // Execute
    if ($stmt->execute()) {
        echo "✅ Product added successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>