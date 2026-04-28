<?php
session_start();
include("../config/db.php");

// ======================
// AUTH CHECK
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ======================
// CSRF CHECK
// ======================
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    die("❌ CSRF validation failed!");
}

// ======================
// INPUT CLEANING
// ======================
$product_name = trim($_POST['product_name']);
$description  = trim($_POST['description']);
$price        = (float) $_POST['price'];
$quantity     = (int) $_POST['quantity_in_stock'];
$category     = trim($_POST['category']);
$sup_id       = (int) $_POST['sup_id'];

// ======================
// VALIDATION
// ======================
if (
    empty($product_name) ||
    empty($description) ||
    empty($category)
) {
    die("❌ Required fields missing!");
}

if ($price <= 0 || $quantity < 0) {
    die("❌ Invalid price or quantity!");
}

// ======================
// IMAGE VALIDATION
// ======================
if (!isset($_FILES['product_img']) || $_FILES['product_img']['error'] !== 0) {
    die("❌ Image upload failed!");
}

$allowed = ['jpg', 'jpeg', 'png', 'gif'];

$fileName = $_FILES['product_img']['name'];
$tmpName  = $_FILES['product_img']['tmp_name'];
$ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    die("❌ Invalid image type!");
}

// ======================
// SECURE FILE NAME
// ======================
$newName = time() . "_" . bin2hex(random_bytes(5)) . "." . $ext;

// folder
$uploadDir = "../images/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$finalPath = $uploadDir . $newName;

if (!move_uploaded_file($tmpName, $finalPath)) {
    die("❌ Failed to upload image!");
}

// DB path
$dbPath = "images/" . $newName;

// ======================
// SQL (SECURE)
// ======================
$sql = "INSERT INTO products 
(product_name, description, price, quantity_in_stock, category, sup_id, product_img)
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Database error!");
}

$stmt->bind_param(
    "ssdisis",
    $product_name,
    $description,
    $price,
    $quantity,
    $category,
    $sup_id,
    $dbPath
);

if ($stmt->execute()) {
    echo "<script>
        alert('✅ Product added successfully!');
        window.location.href='AdminAddProductForm.php';
    </script>";
} else {
    die("❌ Insert failed!");
}

$stmt->close();
$conn->close();
?>