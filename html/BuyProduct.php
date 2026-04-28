<?php
session_start();
include("../config/db.php");

// =======================
// AUTH CHECK
// =======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
  exit();
}

// =======================
// CSRF TOKEN
// =======================
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// =======================
// VALID PRODUCT ID
// =======================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "Invalid request.";
  exit();
}

$product_id = (int) $_GET['id'];

// =======================
// FETCH PRODUCT (SECURE)
// =======================
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
  echo "Product not found.";
  exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// =======================
// HANDLE PURCHASE
// =======================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // CSRF check
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF request.");
  }

  $quantity = (int) $_POST['quantity'];

  if ($quantity < 1) {
    echo "Quantity must be at least 1.";
    exit();
  }

  // Reload stock safely (avoid stale data issue)
  $stmt = $conn->prepare("SELECT quantity_in_stock, price, product_name FROM products WHERE product_id = ?");
  $stmt->bind_param("i", $product_id);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_assoc();
  $stmt->close();

  if ($quantity > $row['quantity_in_stock']) {
    echo "Sorry, only {$row['quantity_in_stock']} in stock.";
    exit();
  }

  $new_stock = $row['quantity_in_stock'] - $quantity;
  $user_id = (int) $_SESSION['id'];
  $total_price = $row['price'] * $quantity;
  $date = date("Y-m-d H:i:s");

  // =======================
  // TRANSACTION (IMPORTANT)
  // =======================
  $conn->begin_transaction();

  try {

    // 1. Update stock
    $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $new_stock, $product_id);
    $stmt->execute();
    $stmt->close();

    // 2. Insert sale
    $stmt = $conn->prepare("INSERT INTO sales (product_id, user_id, quantity_sold, total_price, date_of_sale) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiids", $product_id, $user_id, $quantity, $total_price, $date);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

  } catch (Exception $e) {
    $conn->rollback();
    die("Transaction failed.");
  }

  // =======================
  // SUCCESS PAGE
  // =======================
  echo "
  <!DOCTYPE html>
  <html lang='en'>
  <head>
    <meta charset='UTF-8'>
    <title>Purchase Confirmation</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      .confirmation-box {
        background-color: white;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        text-align: center;
        max-width: 500px;
        width: 90%;
      }
      .confirmation-box h2 {
        font-size: 28px;
        color: #2ecc71;
        margin-bottom: 20px;
      }
      .confirmation-box p {
        font-size: 18px;
        color: #333;
        margin-bottom: 30px;
      }
      .back-link {
        display: inline-block;
        padding: 12px 24px;
        background-color: #2ecc71;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
      }
    </style>
  </head>
  <body>
    <div class='confirmation-box'>
      <h2>✅ Purchase Successful!</h2>
      <p>You bought <strong>$quantity</strong> unit(s) of <strong>" . htmlspecialchars($row['product_name']) . "</strong>.</p>
      <a href='shopProducts.php' class='back-link'>← Back to Shop</a>
    </div>
  </body>
  </html>
  ";
  exit();
}
?>

<!-- =======================
     FORM (UI SAME)
======================= -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buy Product</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .form-box {
      background: white;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }

    .form-box input {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .form-box button {
      padding: 12px 24px;
      background: linear-gradient(to right, #3498db, #2980b9);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="form-box">
  <h2>Buy <?= htmlspecialchars($product['product_name']) ?></h2>

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <input type="number" name="quantity" min="1" max="<?= (int)$product['quantity_in_stock'] ?>" required>

    <button type="submit">Confirm Purchase</button>
  </form>
</div>

</body>
</html>