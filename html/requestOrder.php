<?php
// requestOrder.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Request Order</title>
  <?php include("CSS.php"); ?>
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5, #acb6e5);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
    }

    .request-form-container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      width: 500px;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .request-form-container h2 {
      margin-bottom: 30px;
      text-align: center;
      font-weight: bold;
      color: #3a0ca3;
    }

    .btn-combined {
      width: 100%;
      padding: 12px 30px;
      background: linear-gradient(135deg, #fcb045, #fd1d1d);
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
      box-shadow: 0 8px 20px rgba(252, 176, 69, 0.4);
    }
  </style>
</head>
<body>

  <?php include("UserMenu.php"); ?>

  <div class="request-form-container">
    <h2>Request New Order</h2>
    <form method="POST" action="processRequest.php">
      <div class="mb-3">
        <label for="product_id" class="form-label">Product ID</label>
        <input type="text" name="product_id" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="quantity" class="form-label">Requested Quantity</label>
        <input type="number" name="quantity" class="form-control" required>
      </div>
      <button type="submit" class="btn-combined">
        <i class="fas fa-paper-plane me-2"></i> Send Request
      </button>
    </form>
  </div>

  <?php include("JS.php"); ?>
</body>
</html>
