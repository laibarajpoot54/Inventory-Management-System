<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9fbfc;
      display: flex;
    }

    .main-content {
      margin-left: 250px;
      padding: 0;
      width: calc(100% - 250px);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: white;
      padding: 20px 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .header h1 {
      font-size: 28px;
      color: #2c3e50;
      margin: 0;
    }

    .btn-container {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .dashboard-btn {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 10px 22px;
      border-radius: 6px;
      font-size: 15px;
      text-decoration: none;
      display: flex;
      align-items: center;
      transition: background-color 0.3s ease;
    }

    .dashboard-btn i {
      margin-right: 8px;
    }

    .dashboard-btn:hover {
      background-color: #2980b9;
    }

    .image-wrapper {
      padding: 20px 30px;
      flex-grow: 1;
    }

    .dashboard-image {
      width: 100%;
      max-height: 500px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }

    .footer {
      background: linear-gradient(135deg, #4361ee, #3a0ca3);
      color: white;
      text-align: center;
      padding: 18px;
      margin-top: 20px;
      font-size: 15px;
      box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
    }

    .footer p {
      margin: 0;
      letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        width: 100%;
      }

      .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }

      .btn-container {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>

<?php include 'CustomerMenu.php'; ?>

<div class="main-content">
  <div class="header">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <div class="btn-container">
      <a href="shopProducts.php" class="dashboard-btn"><i class='bx bx-shopping-bag'></i> Shop Products</a>
      <a href="myOrders.php" class="dashboard-btn"><i class='bx bx-box'></i> My Orders</a>
      <!-- <a href="contactUs.php" class="dashboard-btn"><i class='bx bx-phone-call'></i> Contact Us</a> -->
    </div>
  </div>

  <div class="image-wrapper">
    <img src="images/InventoryImg1.jpeg"
         alt="Customer Dashboard"
         class="dashboard-image">
  </div>

  <!-- Footer -->
  <footer class="footer">
      <p>© <?php echo date("Y"); ?> Inventory Management System | Designed by Laiba ❤️</p>
  </footer>

</div>

</body>
</html>