<?php
include("../Config/db.php");
// Check request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ Sanitize input
    $product_id = trim($_POST['product_id']);
    $user_id = trim($_POST['user_id']);
    $quantity_sold = trim($_POST['quantity_sold']);
    $total_price = trim($_POST['total_price']);
    $date_of_sale = trim($_POST['date_of_sale']);

    // ✅ Validation
    if (empty($product_id) || empty($user_id) || empty($quantity_sold) || empty($total_price) || empty($date_of_sale)) {
        die("❌ All fields are required.");
    }

    if (!is_numeric($product_id) || !is_numeric($user_id)) {
        die("❌ Invalid product or user ID.");
    }

    if (!is_numeric($quantity_sold) || $quantity_sold <= 0) {
        die("❌ Invalid quantity.");
    }

    if (!is_numeric($total_price) || $total_price < 0) {
        die("❌ Invalid total price.");
    }

    // ✅ Date validation
    if (!strtotime($date_of_sale)) {
        die("❌ Invalid date format.");
    }

    // ✅ Optional: Check stock before selling
    $checkStock = $conn->prepare("SELECT quantity_in_stock FROM products WHERE product_id = ?");
    $checkStock->bind_param("i", $product_id);
    $checkStock->execute();
    $result = $checkStock->get_result();

    if ($result->num_rows === 0) {
        die("❌ Product not found.");
    }

    $row = $result->fetch_assoc();
    if ($row['quantity_in_stock'] < $quantity_sold) {
        die("❌ Not enough stock available.");
    }

    $checkStock->close();

    // ✅ Insert Sale (Prepared Statement)
    $stmt = $conn->prepare("INSERT INTO sales 
        (product_id, user_id, quantity_sold, total_price, date_of_sale) 
        VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iiids", $product_id, $user_id, $quantity_sold, $total_price, $date_of_sale);

    // Execute
    if ($stmt->execute()) {

        // ✅ Update stock after sale
        $updateStock = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock - ? WHERE product_id = ?");
        $updateStock->bind_param("ii", $quantity_sold, $product_id);
        $updateStock->execute();
        $updateStock->close();

        echo "✅ Sale recorded successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>