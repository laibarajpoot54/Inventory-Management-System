<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit();
}

include("../config/db.php");

/* ======================
   CSRF TOKEN
====================== */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sell Product</title>
  <?php include("CSS.php"); ?>

  <style>
    body {
      background: linear-gradient(135deg, #74ebd5, #acb6e5);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      position: relative;
      overflow-x: hidden;
    }

    .sell-form-container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      width: 500px;
      animation: fadeIn 1s ease-in-out;
      z-index: 1;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .sell-form-container h2 {
      margin-bottom: 30px;
      text-align: center;
      font-weight: bold;
      color: #3a0ca3;
    }

    .btn-combined {
      width: 100%;
      padding: 12px 30px;
      background: linear-gradient(135deg, #4361ee, #3a0ca3);
      color: white;
      border: none;
      border-radius: 30px;
      font-weight: 500;
      transition: all 0.3s ease;
      margin-top: 20px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .btn-combined:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
    }
  </style>
</head>

<body>

<div class="sell-form-container">

  <h2>Sell a Product</h2>

  <!-- SUCCESS / ERROR -->
  <?php
    if (isset($_SESSION['sell_success'])) {
      echo "<div style='text-align:center;color:green;margin-bottom:10px;'>"
        . htmlspecialchars($_SESSION['sell_success']) .
      "</div>";
      unset($_SESSION['sell_success']);
    }

    if (isset($_SESSION['sell_error'])) {
      echo "<div style='text-align:center;color:red;margin-bottom:10px;'>"
        . htmlspecialchars($_SESSION['sell_error']) .
      "</div>";
      unset($_SESSION['sell_error']);
    }
  ?>

  <!-- FORM -->
  <form action="processSell.php" method="POST">

    <!-- CSRF -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <div class="mb-3">
      <label for="product" class="form-label">Select Product</label>
      <select class="form-select" id="product" name="product_id" required>
        <option value="">-- Select Product --</option>

        <?php
          // SAFE QUERY
          $stmt = $conn->prepare("SELECT product_id, product_name FROM products WHERE quantity_in_stock > 0");
          $stmt->execute();
          $result = $stmt->get_result();

          while ($row = $result->fetch_assoc()) {
              echo "<option value='".(int)$row['product_id']."'>"
                . htmlspecialchars($row['product_name']) .
              "</option>";
          }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="quantity" class="form-label">Quantity</label>
      <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
    </div>

    <button type="submit" class="btn-combined">
      Sell Now
    </button>
  </form>

  <a href="UserDashboard.php" class="btn-combined">
    Back to Dashboard
  </a>

</div>

</body>
</html>