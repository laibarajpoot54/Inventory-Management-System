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
// CSRF TOKEN GENERATION
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ======================
// VALIDATE ID
// ======================
if (!isset($_GET['id'])) {
    die("No product selected.");
}

$product_id = intval($_GET['id']);

// ======================
// FETCH PRODUCT (SECURE)
// ======================
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

  <div class="card shadow-lg">

    <div class="card-header text-center bg-primary text-white">
      <h3>Edit Product</h3>
    </div>

    <div class="card-body">

      <form action="updateProductHandler.php" method="POST" enctype="multipart/form-data">

        <!-- ======================
             CSRF TOKEN (IMPORTANT)
        ====================== -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">

        <!-- PRODUCT NAME -->
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input type="text" name="product_name" class="form-control"
                 value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
        </div>

        <!-- DESCRIPTION -->
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" required><?php 
            echo htmlspecialchars($product['description']); 
          ?></textarea>
        </div>

        <!-- PRICE -->
        <div class="mb-3">
          <label class="form-label">Price</label>
          <input type="number" name="price" class="form-control"
                 value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
        </div>

        <!-- QUANTITY -->
        <div class="mb-3">
          <label class="form-label">Quantity in Stock</label>
          <input type="number" name="quantity_in_stock" class="form-control"
                 value="<?php echo htmlspecialchars($product['quantity_in_stock']); ?>" required>
        </div>

        <!-- CATEGORY -->
        <div class="mb-3">
          <label class="form-label">Category</label>
          <input type="text" name="category" class="form-control"
                 value="<?php echo htmlspecialchars($product['category']); ?>" required>
        </div>

        <!-- SUPPLIER ID -->
        <div class="mb-3">
          <label class="form-label">Supplier ID</label>
          <input type="number" name="sup_id" class="form-control"
                 value="<?php echo htmlspecialchars($product['sup_id']); ?>" required>
        </div>

        <!-- IMAGE -->
        <div class="mb-3">
          <label class="form-label">Product Image</label>
          <input type="file" name="product_img" class="form-control">

          <small>
            Current Image:
            <img src="images/<?php echo htmlspecialchars($product['product_img']); ?>"
                 style="width:100px; height:auto;">
          </small>
        </div>

        <!-- BUTTONS -->
        <div class="d-flex justify-content-between">
          <a href="AdminManageProducts.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">Update Product</button>
        </div>

      </form>

    </div>
  </div>

</div>

</body>
</html>