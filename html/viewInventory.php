<?php
// viewInventory.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit();
}

include("db_connection.php"); // Connect DB
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>View Inventory</title>
  <?php include("CSS.php"); ?>
  <style>
    .product-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <?php include("UserMenu.php"); ?>
  <div class="container mt-5">
    <h2>Current Inventory</h2>
    <table class="table table-bordered text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Product</th>
          <th>Price</th>
          <th>Stock</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $row['product_id']; ?></td>
            <td>
              <img src="<?= $row['product_img']; ?>" class="product-img mb-2"><br>
              <?= $row['product_name']; ?>
            </td>
            <td>Rs. <?= number_format($row['price'], 2); ?></td>
            <td><?= $row['quantity_in_stock']; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <?php include("JS.php"); ?>
</body>
</html>
