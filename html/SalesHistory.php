<?php
// salesHistory.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit();
}

include("db_connection.php");
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM sales WHERE user_id = '$userId'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Sales History</title>
  <?php include("CSS.php"); ?>
</head>
<body>
  <?php include("UserMenu.php"); ?>
  <div class="container mt-5">
    <h2>My Sales History</h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Sale ID</th>
          <th>Product ID</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $row['sales_id']; ?></td>
            <td><?= $row['product_id']; ?></td>
            <td><?= $row['quantity_sold']; ?></td>
            <td><?= $row['total_price']; ?></td>
            <td><?= $row['date_of_sale']; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <?php include("JS.php"); ?>
</body>
</html>
