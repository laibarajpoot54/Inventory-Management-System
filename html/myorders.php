<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
  exit();
}

include("../config/db.php"); // Database connection

$userId = $_SESSION['user_id']; // Assuming user_id is stored in session
$query = "SELECT s.*, p.product_name, p.description 
          FROM sales s
          JOIN products p ON s.product_id = p.product_id
          WHERE s.user_id = $userId
          ORDER BY s.date_of_sale DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9fbfc;
    }

    .main-content {
      margin-left: 250px;
      padding: 40px;
    }

    h2 {
      font-size: 26px;
      color: #2c3e50;
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 14px 18px;
      text-align: left;
    }

    th {
      background-color: #3498db;
      color: white;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 14px;
    }

    tr:nth-child(even) {
      background-color: #f2f6fa;
    }

    td {
      font-size: 15px;
      color: #2c3e50;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      table, thead, tbody, th, td, tr {
        display: block;
      }

      th, td {
        padding: 12px;
        text-align: right;
        position: relative;
      }

      th::before, td::before {
        position: absolute;
        left: 12px;
        text-align: left;
        font-weight: bold;
      }

      tr {
        margin-bottom: 15px;
      }
    }
    .back-btn {
    display: inline-block;
    padding: 10px 24px;
    background: linear-gradient(to right, #3498db, #2980b9);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    font-size: 16px;
    transition: background 0.3s ease, transform 0.2s ease;
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
}

.back-btn:hover {
    background: linear-gradient(to right, #2980b9, #2471a3);
    transform: translateY(-2px);
}

  </style>
</head>
<body>

<?php include 'CustomerMenu.php'; ?>


<div class="main-content">
  <h2 style="text-align: center;">📦 My Orders</h2>
  


  <?php if(mysqli_num_rows($result) > 0) { ?>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Description</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Date & Time</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= $row['quantity_sold'] ?></td>
            <td>$<?= $row['total_price'] ?></td>
            <td><?= date("d M Y, h:i A", strtotime($row['date_of_sale'])) ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  <?php } else { ?>
    <div style="display: flex; justify-content: center; align-items: center; height: 300px;">
      <div style="background: #ffffff; padding: 40px 60px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); text-align: center;">
        <i style="font-size: 60px; color: #3498db;">📭</i>
        <h3 style="margin-top: 20px; font-size: 24px; color: #2c3e50;">No orders yet</h3>
        <p style="color: #7f8c8d; font-size: 16px; margin-top: 10px;">Looks like you haven't placed any orders yet!</p>
      </div>
    </div>
  <?php } ?>

  <div style="text-align: center; margin-top: 50px; margin-bottom: 30px;">
  <a href="customerdashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
</div>
  
</div>

</body>
</html>
