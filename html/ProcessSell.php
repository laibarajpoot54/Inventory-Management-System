<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

/* ======================
   CSRF CHECK
====================== */
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['sell_error'] = "Invalid request (CSRF detected).";
    header("Location: SellProductForm.php");
    exit();
}

/* ======================
   INPUT VALIDATION
====================== */
$product_id = (int)($_POST['product_id'] ?? 0);
$quantity   = (int)($_POST['quantity'] ?? 0);

if ($product_id <= 0 || $quantity <= 0) {
    $_SESSION['sell_error'] = "Invalid input.";
    header("Location: SellProductForm.php");
    exit();
}

/* ======================
   GET PRODUCT SAFELY
====================== */
$stmt = $conn->prepare("SELECT product_id, product_name, quantity_in_stock, price FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    $_SESSION['sell_error'] = "Product not found.";
    header("Location: SellProductForm.php");
    exit();
}

/* ======================
   STOCK CHECK
====================== */
if ($product['quantity_in_stock'] < $quantity) {
    $_SESSION['sell_error'] = "Not enough stock available.";
    header("Location: SellProductForm.php");
    exit();
}

/* ======================
   TRANSACTION SAFETY
====================== */
$conn->begin_transaction();

try {

    // Update stock
    $new_stock = $product['quantity_in_stock'] - $quantity;

    $update = $conn->prepare("UPDATE products SET quantity_in_stock = ? WHERE product_id = ?");
    $update->bind_param("ii", $new_stock, $product_id);
    $update->execute();

    // Insert sale
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($user_id <= 0) {
        throw new Exception("Invalid user session");
    }

    $total_price = $product['price'] * $quantity;

    $insert = $conn->prepare("
        INSERT INTO sales (product_id, user_id, quantity_sold, total_price, date_of_sale)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $insert->bind_param("iiid", $product_id, $user_id, $quantity, $total_price);
    $insert->execute();

    $conn->commit();

    $_SESSION['sell_success'] = "Product sold successfully!";

    // refresh CSRF token (important)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['sell_error'] = $e->getMessage();
}

header("Location: SellProductForm.php");
exit();
?>