<?php
include("../config/db.php");
session_start();

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

$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Products</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include("CSS.php"); ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
     body {
      background-color: #f4f6fc;
      font-family: 'Poppins', sans-serif;
      color: #2d3436;
    }

    .container-custom {
      max-width: 1100px;
      margin: 40px auto;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
    }

    .page-title {
      text-align: center;
      margin-bottom: 30px;
      color: #6c5ce7;
      font-weight: bold;
    }

    .product-table th, .product-table td {
      vertical-align: middle;
      text-align: center;
    }

    
    thead th {
      background-color: #6c5ce7; 
      color: white; 
    }

    .btn-back {
      display: inline-flex;
      align-items: center;
      padding: 10px 25px;
      background: linear-gradient(135deg, #6c5ce7, #a29bfe);
      color: white;
      border: none;
      border-radius: 30px;
      font-weight: 500;
      font-size: 0.95rem;
      margin-top: 20px;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
    }

    .btn-back:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(108, 92, 231, 0.4);
    }

    .btn-back i {
      margin-right: 8px;
    }

    @media (max-width: 768px) {
      .container-custom {
        margin: 20px;
        padding: 15px;
      }

      .product-table th, .product-table td {
        font-size: 0.85rem;
        padding: 8px;
      }

      .btn-back {
        font-size: 0.85rem;
        padding: 8px 20px;
      }
    }

  </style>
</head>

<body>

<div class="container-custom">
  <h2 class="page-title">Manage Products</h2>

  <table class="table table-bordered product-table">
    <thead class="table-dark">
      <tr>
        <th>Product ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Category</th>
        <th>Stock</th>
        <th>Price</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <tr>
          <td><?= htmlspecialchars($row['product_id']) ?></td>

          <td>
            <img src="<?= htmlspecialchars($row['product_img']) ?>"
                 onerror="this.onerror=null;this.src='images/default.jpg';"
                 style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
          </td>

          <td><?= htmlspecialchars($row['product_name']) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td><?= htmlspecialchars($row['quantity_in_stock']) ?></td>
          <td>$<?= htmlspecialchars($row['price']) ?></td>

          <td>

            <!-- EDIT (safe GET, but controlled by admin session) -->
            <a href="updateProduct.php?id=<?= $row['product_id'] ?>" 
               class="btn btn-warning btn-sm">
               Edit
            </a>

            <!-- DELETE (SECURE POST + CSRF) -->
            <form method="POST" action="deleteProduct.php" style="display:inline;">
              <input type="hidden" name="delete_id" value="<?= $row['product_id'] ?>">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

              <button type="submit"
                      class="btn btn-danger btn-sm"
                      onclick="return confirm('Are you sure you want to delete this product?')">
                Delete
              </button>
            </form>

          </td>
        </tr>

      <?php } ?>
    </tbody>
  </table>

  <a href="AdminDashBoard.php" class="btn-back">
    <i class="bi bi-arrow-left"></i> Back to Dashboard
  </a>
</div>

<?php include("JS.php"); ?>

</body>
</html>