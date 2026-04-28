<?php
session_start();
include("../Config/db.php");

// ======================
// AUTH CHECK (CUSTOMER ONLY)
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
  exit();
}

// ======================
// SESSION SECURITY
// ======================
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// session timeout (30 min)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

// ======================
// CSRF TOKEN (for future forms)
// ======================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ======================
// SEARCH SECURITY (PREPARED QUERY)
// ======================
$search = '';

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);

    // Prepared statement (SAFE AGAINST SQL INJECTION)
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE CONCAT(?, '%')");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

} else {
    $stmt = $conn->prepare("SELECT * FROM products");
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shop Products</title>
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">

  <!-- YOUR CSS UNCHANGED -->
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 0;
    }

    .main-content {
      margin-left: 250px;
      padding: 40px;
    }

    h2 {
      font-size: 28px;
      color: #2c3e50;
      margin-bottom: 30px;
    }

    .product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 300px));
  justify-content: center;
  gap: 20px;
  padding: 10px;
}

    .product-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 15px;
      transition: transform 0.3s ease;
    }

    .product-card:hover {
      transform: scale(1.03);
    }

    .product-image {
      width: 100%;
      height: 140px;
      object-fit: cover;
      border-bottom: 1px solid #eee;
    }

    .product-details {
      padding: 12px;
      flex-grow: 1;
    }

    .product-title {
      font-size: 15px;
      font-weight: 600;
      color: #34495e;
      margin-bottom: 5px;
    }

    .product-desc {
      font-size: 12px;
      color: #7f8c8d;
      height: 36px;
      overflow: hidden;
      margin-bottom: 6px;
    }

    .product-price {
      font-size: 14px;
      font-weight: 600;
      color:rgb(62, 40, 184);
      margin-bottom: 10px;
    }

    .product-stock {
      font-size: 12px;
      color: #27ae60;
      margin-bottom: 10px;
    }

    .buy-btn {
      display: inline-block;
      width: 100%;
      text-align: center;
      padding: 8px 0;
      background: linear-gradient(to right, #3498db, #2980b9);
      color: white;
      border-radius: 8px;
      font-weight: 500;
      text-decoration: none;
    }

    .buy-btn:hover {
      background: linear-gradient(to right, #2980b9, #1f6391);
    }

    .back-btn {
      display: inline-block;
      padding: 8px 20px;
      background: linear-gradient(to right, #3498db, #2980b9);
      color: white;
      border-radius: 8px;
      font-weight: 500;
      text-decoration: none;
    }

    .no-products {
      text-align: center;
      font-size: 18px;
      color: #888;
    }
  </style>
</head>

<body>

<?php include 'CustomerMenu.php'; ?>

<div class="main-content">

  <!-- 🔍 SEARCH (SAFE OUTPUT) -->
  <form method="GET" action="" style="margin-bottom: 20px; display: flex; justify-content: center;">
    <div style="display: flex; align-items: center; background: #fff; padding: 10px 20px; border-radius: 50px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); width: 100%; max-width: 600px;">
      
      <i class="bx bx-search" style="font-size: 24px; color: #666;"></i>

      <input
        type="text"
        name="search"
        placeholder="Search products..."
        value="<?= htmlspecialchars($search) ?>"
        style="border: none; outline: none; margin-left: 10px; width: 100%; background: transparent;"
      />

      <button type="submit" style="margin-left: 10px; background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 30px;">
        Search
      </button>
    </div>
  </form>

  <!-- HEADER -->
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <h2>🛍️ Available Products</h2>
    <a href="customerdashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
  </div>

  <!-- PRODUCTS -->
  <?php if ($result->num_rows === 0): ?>
    <p class="no-products">No products found.</p>
  <?php else: ?>
    <div class="product-grid">

      <?php while($row = $result->fetch_assoc()): ?>
        <div class="product-card">

          <img src="<?= htmlspecialchars($row['product_img']) ?>"
               onerror="this.onerror=null;this.src='images/default.jpg';"
               class="product-image">

          <div class="product-details">
            <div class="product-title"><?= htmlspecialchars($row['product_name']) ?></div>
            <div class="product-desc"><?= htmlspecialchars($row['description']) ?></div>
            <div class="product-price">$<?= htmlspecialchars($row['price']) ?></div>
            <div class="product-stock">In stock: <?= (int)$row['quantity_in_stock'] ?></div>

            <a href="buyProduct.php?id=<?= (int)$row['product_id'] ?>" class="buy-btn">
              Buy Now
            </a>
          </div>

        </div>
      <?php endwhile; ?>

    </div>
  <?php endif; ?>

</div>

</body>
</html>