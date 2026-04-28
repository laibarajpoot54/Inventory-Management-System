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
// FETCH DATA
// ======================
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Update Product</title>
  <?php include("CSS.php"); ?>

  <style>
    .btn-back {
      display: inline-block;
      padding: 10px 25px;
      background: linear-gradient(135deg, #6c5ce7, #a29bfe);
      color: white;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 500;
      margin-top: 20px;
      transition: 0.3s;
    }

    .btn-back:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
      color: white;
    }
  </style>
</head>

<body>
  
<div class="container mt-5">
  <h2 class="mb-4">Update Product</h2>

  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Category</th>
        <th>Supplier ID</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
        
        <tr>
          <!-- ✅ XSS PROTECTION -->
          <td><?= htmlspecialchars($row['product_id']) ?></td>
          <td><?= htmlspecialchars($row['product_name']) ?></td>
          <td><?= htmlspecialchars($row['price']) ?></td>
          <td><?= htmlspecialchars($row['quantity_in_stock']) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td><?= htmlspecialchars($row['sup_id']) ?></td>

          <td>
            <!-- SAFE EDIT LINK -->
            <a href="editProductForm.php?id=<?= (int)$row['product_id'] ?>" 
               class="btn btn-warning btn-sm">
               Edit
            </a>
          </td>
        </tr>

      <?php } ?>
    </tbody>
  </table>

  <!-- ✅ BACK BUTTON -->
  <div class="text-center">
    <a href="AdminDashboard.php" class="btn-back">
      ⬅️ Back to Dashboard
    </a>
  </div>

</div>

<?php include("JS.php"); ?>
</body>
</html>