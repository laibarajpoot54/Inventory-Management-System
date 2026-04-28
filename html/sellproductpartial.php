<?php
session_start();
include("../config/db.php");

// Fetch products
$sql = "SELECT product_id, product_name, quantity_in_stock FROM products";
$result = mysqli_query($conn, $sql);
?>

<h2 class="mb-4">Sell a Product</h2>
<form method="POST" action="processSell.php">
  <div class="mb-3">
    <label for="product_id" class="form-label">Select Product</label>
    <select name="product_id" class="form-select" required>
      <option value="">-- Select Product --</option>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <option value="<?= $row['product_id'] ?>">
          <?= $row['product_name'] ?> (Available: <?= $row['quantity_in_stock'] ?>)
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="quantity" class="form-label">Quantity</label>
    <input type="number" name="quantity" class="form-control" required min="1">
  </div>

  <button type="submit" class="btn btn-success">Sell Now</button>
</form>
